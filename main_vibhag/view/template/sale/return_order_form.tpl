<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid col-lg-8 col-md-8">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php if($is_admin){ ?>
    <form method="post" id="admin_mode_form">
      <h4><b>Admin Mode: </b></h4>
      <!-- Rounded switch -->
      <label class="switch admin_mode_block">
        <?php 
          if($admin_mode == 'on') { $str = "checked"; }
          else{ $str = "";}
        ?>
        <input type="checkbox" name="admin_mode" id="admin_mode" <?php echo $str;?> >
        <span class="slider round"></span>
      </label>
    </form>
    <?php } ?>
  </div>
  <div class="container-fluid">
    <div class="alert alert-danger flash-error" style="display: none;">
      <i class="fa fa-exclamation-circle"></i>
      <span class="error-msg"></span>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title show clearfix">
        <div class="pull-left col-sm-4">
         <div class="col-sm-12 nopadding">
        Order No.:  <a  href="<?php echo $order_href; ?>">#<?php echo $order_no; ?></a>
        </div>
        </div>
        </h3>
      </div>
      <div class="panel-body">
        <input type="hidden" name="order_id" id="hddn_order_id" value="<?php echo $order_id; ?>"/>
        <input type="hidden" name="order_no" id="hddn_order_no" value="<?php echo $order_no; ?>"/>
        <input type="hidden" name="customer_id" id="hddn_customer_id" value="<?php echo $customer_id; ?>"/>
        <input type="hidden" name="customer_name" id="hddn_customer_name" value="<?php echo trim($customer['firstname'].' '.$customer['lastname']); ?>"/>

        <input type="hidden" name="hddn_first_name" id="hddn_first_name" value="<?php echo trim($customer['firstname']); ?>"/>
        <input type="hidden" name="hddn_last_name" id="hddn_last_name" value="<?php echo trim($customer['lastname']); ?>"/>
        <input type="hidden" name="hddn_email" id="hddn_email" value="<?php echo $customer['email']; ?>"/>
        <input type="hidden" name="hddn_telephone" id="hddn_telephone" value="<?php echo $customer['telephone']; ?>"/>
        <div class="row">
          <div class="col-lg-4 col-md-4">
            <h5>Customer Name: <?php echo (!empty($customer['firstname'])) ? $customer['firstname'].' '.$customer['lastname'] : '' ; ?></h5>
          </div>
          <div class="col-lg-4 col-md-4">
            <h5>Customer Email:
              <a href="mailto:<?php echo $customer['email']; ?>">
                <?php echo (!empty($customer['email']))?$customer['email']:''; ?>
              </a>
            </h5>
          </div>
          <div class="col-lg-4 col-md-4 text-right">
            <h5>Customer Telephone:
              <a href="tel:<?php echo $customer['telephone']; ?>">
              <?php echo (!empty($customer['telephone']))?$customer['telephone']:''; ?>
              </a>
            </h5>
          </div>
        </div>
        <div class="show-error error" style="display: none;"></div><br>
        <ul class="nav nav-tabs">
          <li class="active">
            <a id="addForm" onclick="toggleStatusForm(this,'.addForm')" href="#tab-add-update-return" data-toggle="tab">Add / Update Return</a>
          </li>
          <?php foreach($available_action_tabs as $tab_key => $tab){ ?>
          <li>
            <a 
            id="<?php echo $tab_key;?>" 
            onclick="toggleStatusForm(this,'.statusForm')" 
            href="#tab-<?php echo $tab_key;?>" 
            data-toggle="tab">
                <?php echo $tab; ?>
            </a>
          </li>
          <?php } ?>

          <?php if(
                  !empty($debit_notes) 
                        || 
                  !empty($returns_for_custom_dn)
                        || 
                  !empty($custom_debit_notes) 
                        || 
                  !empty($cancelled_debit_notes)
          ) { ?>
          <li>
            <a id="debit_note_button" onclick="toggleStatusForm(this,'.other')" href="#tab-seller-return-product-break-up" data-toggle="tab">
            Debit Notes
            <?php if(count($returns_for_custom_dn) > 0 ){ ?>
              <span class="badge" data-toggle="tooltip" title="Return Pending to generate Custom DebitNote"><?php echo count($returns_for_custom_dn);?></span>
            <?php } ?>
            </a>
          </li>
          <?php } ?>

          <?php if(!empty($replacement_notes))
           { ?>
          <li>
            <a id="replacement_note_button" onclick="toggleStatusForm(this,'.other')" href="#tab-replacement-notes" data-toggle="tab">
                Replacement Notes
            </a>
          </li>
          <?php } ?>

          <?php if(
                    !empty($credit_notes) 
                        || 
                    !empty($returns_for_cn) 
                        || 
                    !empty($cancelled_cn)
                  ) { ?>
          <li>
            <a id="credit_note_button" onclick="toggleStatusForm(this,'.credit')" href="#tab-credit" data-toggle="tab">
            Credit Notes 
            <?php if(count($returns_for_cn) > 0 ){ ?>
              <span class="badge" data-toggle="tooltip" title="Pending Credit Note(s)"><?php echo count($returns_for_cn);?></span>
            <?php } ?>
            </a>
          </li>
          <?php } ?>
          
          <?php if(!empty($data['master_return_ids'])) {?>
          <li>
            <a id="reverse_shipment_button" onclick="toggleStatusForm(this,'.reverse-shipment')" href="#tab-reverse-shipment" data-toggle="tab">
              Reverse Shipment
              <span class="badge" data-toggle="tooltip" title="Pending Master Return Id(s) for Reverse Shipment"><?php echo count($data['master_return_ids']);?></span>
            </a>
          </li>
          <?php } ?>

          <?php if(!empty($data['forward_shipment_return_ids'])) {?>
          <li>
            <a id="shipment_backto_customer_button" onclick="toggleStatusForm(this,'.shipment-backto-customer')" href="#tab-shipment-backto-customer" data-toggle="tab">
              Shipment Back to Customer
              <span class="badge" data-toggle="tooltip" title="Pending Return Id(s) to send Shipment back to customer"><?php echo count($data['forward_shipment_return_ids']);?></span>
            </a>
          </li>
          <?php } ?>
        
          <?php if(!empty($reverse_shipments) || !empty($shipments_backto_cutomer) || !empty($cancel_reverse_shipments)){ ?>
          <li>
            <a id="reverse_shipment_tracking_button" onclick="toggleStatusForm(this,'.reverse-shipment-tracking')" href="#tab-reverse-shipment-tracking" data-toggle="tab">Shipments Tracking</a>
          </li>
          <?php } ?>
          <li>
            <a id="bank_detail_button" onclick="toggleStatusForm(this,'.bamk-detail')" href="#tab-bank-detail" data-toggle="tab">Bank Detail</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab-add-update-return">
            <?php echo $tab_all_return; ?>
          </div>
          <?php foreach($tab_return_actions as $tab_key => $tab){ ?>
            <div class="tab-pane" id="tab-<?php echo $tab_key; ?>">
              <?php echo $tab; ?>
            </div>
          <?php } ?>
          <div class="tab-pane" id="tab-seller-return-product-break-up">
            <?php echo $tab_debit_note; ?>
          </div>
          <div class="tab-pane" id="tab-replacement-notes">
            <?php echo $tab_replacement_note; ?>
          </div>
          <div class="tab-pane" id="tab-credit">
            <?php echo $tab_credit_note; ?>
          </div>
          <div class="tab-pane" id="tab-reverse-shipment">
            <?php echo $tab_add_shipment; ?>
          </div>
          <div class="tab-pane" id="tab-shipment-backto-customer">
            <?php echo $tab_shipment_backto_customer; ?>
          </div>
          <div class="tab-pane" id="tab-reverse-shipment-tracking">
            <?php echo $tab_reverse_shipment_status; ?>
          </div>
          <div class="tab-pane" id="tab-bank-detail">
            <?php echo $tab_bank_details; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="historyTab">Product history -  </h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Quantity</th>
              <th>Return Reason</th>
              <th>Return Action</th>
              <th>Shipping Methods</th>
              <th>Comment</th>
              <th>Internal note</th>
              <th>Date</th>
              <th>User</th>
            </tr>
          </thead>
          <tbody id="history_body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!----- Show replacement defected image uploaded by customer from front-end with return details----- ------>
<div class="modal fade" id="replacement_defected_image_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="shipmentHistoryTab">Defected Image (uploaded by customer)</h4>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <div id="replacement-defected-image">
          <!-- shipping slip -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!----- Show replacement defected image uploaded by customer from front-end with return details----- ------>

<script>
var order_product_history = <?php echo !empty($return_history) ? json_encode($return_history) : "{}"; ?>;
var return_actions = <?php echo !empty($return_actions) ? json_encode($return_actions) : "{}"; ?>;

var extra_goods_received_action_id = <?php echo RETURN_ACTION_IDS['Extra_Goods_Received'];?> ; 
var short_goods_receved_action_id  = <?php echo RETURN_ACTION_IDS['Short_Goods_Received'];?> ; 
var generat_dn_action_id  = <?php echo RETURN_ACTION_IDS['DN_Generated_For_Seller'];?> ; 
var manually_generated_reverse_shipment = <?php echo RETURN_ACTION_IDS['Manually_Generated_Reverse_Shipment'];?> ; 
var static_content_url = '<?php echo STATIC_CONTENT_URL?>';


$('.cn_reversal_shipping').change(function(){
   
  var order_id        = $(this).data('order-id');
  var suborder_id     = $(this).data('suborder-id');
  var order_no        = $(this).data('order-no');
  var shipping_charge = $(this).data('suborder-shipping-charge');

  var cn_return_ids = [];
  
  //get suborder wise return ids
  $('tbody'+'#cn-products-'+suborder_id).find('.cn-products-list-'+suborder_id).each(function(){
      if( $(this).find('td').find('input:checkbox').prop('checked') ) {
          return_id       = $(this).find('td').find('input:checkbox').val();
          cn_return_ids.push(return_id);
      }
  })

    if(cn_return_ids.length > 0) {

      $.ajax({
          type: 'POST',
          data: {
                 'return_ids'        : cn_return_ids,
                 'order_id'          : order_id,
                 'order_no'          : order_no,
                 'suborder_id'       : suborder_id,
                 'shipping_charge'   : shipping_charge
                },
          url: 'index.php?route=sale/return/resetReversalShipping&token=<?php echo $token; ?>',
          success: function(data) {
              $('#reversal_shipping_charges_'+suborder_id).html(data); 
              $('#reversal_shipping_'+suborder_id).val(data);           
          }
      })
   }else{
      //set old reversal shipping chanrges for suborder id
      var old_value = $('#reversal_shipping_charges_'+suborder_id).data('privious');
       $('#reversal_shipping_charges_'+suborder_id).html(old_value); 
   }

})

function generate_credit_note(obj, tab) {  
 
  var cn_return_ids = [];

  //get suborder wise return ids
  $('tbody'+'#cn-products-'+tab).find('.cn-products-list-'+tab).each(function(){
      if( $(this).find('td').find('input:checkbox').prop('checked') ) {
          return_id       = $(this).find('td').find('input:checkbox').val();
          cn_return_ids.push(return_id);
      }
  })

  if(cn_return_ids.length == 0) {
     alert("Select atleast one product to generate credit note"); 
     return false;
  }

  var return_ids       = cn_return_ids;
  //var return_ids     = $.parseJSON($(obj).parent().find('.return_id').val());
  var order_id         = $(obj).attr('data-order_id');
  var seller_id        = $(obj).attr('data-seller_id');
  var customer_id      = $('#hddn_customer_id').val();
  var order_no         = $(obj).attr('data-order_no');
  var customer_name    = $('#hddn_customer_name').val();
  var customer_email   = $('#hddn_email').val();
  var telephone        = $('#hddn_telephone').val();
  var shipping_charges = $(obj).parents('.generate').find('input[name="shipping_charges"]').val();
  var suborder_id = $(obj).attr('data-suborder_id');
  var remaining_advance  = $(obj).attr('data-remaining_advance');
  var cod_failed_penalty = $(obj).parents('.generate').find('input[name="cod_failed_penalty"]').val();
  var advance_collected  = $(obj).parents('.generate').find('input[name="advance_collected"]').val();
  var cash_discount     = $(obj).parents('.generate').find('input[name="cash_discount"]').val();
  var actual_advance_collected = $(obj).parents('.generate').find('input[name="actual_advance_collected"]').val();
  var cod_security_balance = $(obj).parents('.generate').find('input[name="cod_security_balance"]').val();
  var other_charges     = $(obj).parents('.generate').find('input[name="other_charges"]').val();

  if($(obj).parents('.generate').find('input[name="reversal_shipping"]').prop('checked') == true){
    var reversal_shipping = $(obj).parents('.generate').find('input[name="reversal_shipping"]').val();
  }else{
    var reversal_shipping = 0;
  }
  
  if($(obj).parents('.generate').find('input[name="refund_onhold"]').prop('checked') == true){
    var refund_onhold = $(obj).parents('.generate').find('input[name="refund_onhold"]').val();
  }else{
    var refund_onhold = 0;
  }

  if(shipping_charges < 0){
    alert('Shipping Charges cannot be negative.');
    return false;
  }else if(cod_failed_penalty < 0){
    alert('COD Failed Penalty cannot be negative.');
    return false;
  }

// COD failed penalty can be greater of COD security balanc + Actual advance collected (Except cashback and coupons)  
  remaining_advance = parseFloat(cod_security_balance) + parseFloat(actual_advance_collected);

  if(parseFloat(cod_failed_penalty) > parseFloat(remaining_advance)){
    alert("COD Failed Panalty can not higher then remaining collected advance( advance collected + cod security ).");
    return false;
  }

  $(obj).prop('disabled',true);
  $(obj).css('display', 'none');

  show_overlay(); 
  
  $.ajax({
    type: 'POST',
    data: {
           'return_ids'        : return_ids,
           'order_id'          : order_id,
           'order_no'          : order_no,
           'customer_id'       : customer_id,
           'customer_name'     : customer_name,
           'customer_email'    : customer_email,
           'telephone'         : telephone,
           'seller_id'         : seller_id,
           'shipping_charges'  : shipping_charges,
           'suborder_id'       : suborder_id,
           'cod_failed_penalty': cod_failed_penalty,
           'advance_collected' : advance_collected,
           'cash_discount'    : cash_discount,
           'actual_advance_collected' : actual_advance_collected,
           'cod_security_balance' : cod_security_balance,
           'reversal_shipping' : reversal_shipping,
           'refund_onhold'     : refund_onhold,
           'other_charges'     : other_charges
          },
    url: 'index.php?route=sale/return/generate_credit_note_no&token=<?php echo $token; ?>',
    dataType: 'json',
    //async: false,
    success: function(data) {
      if(data.success == "success"){
        location.assign(data.credit_note_download_link);
        alert('CreditNote generated successfully.');
        $("#link-td").html("<a href='"+data.credit_note_download_link+"'><i class='fa fa-download fa-2x' aria-hidden='true'</i></a>");
        $(obj).parents('.generate').find('input[name="shipping_charges"]').prop('disabled',true);
      }else{
        alert(data.error);
      }
    },
    complete: function(){
       location.reload();
    }
  });
}
</script>
<script type="text/javascript" src="view/javascript/sale/return.js?v=3.1.10"></script>
<link rel="stylesheet" type="text/css" href="view/stylesheet/sale/return.css?v=1.02" media="screen" />
<?php echo $footer;  ?>