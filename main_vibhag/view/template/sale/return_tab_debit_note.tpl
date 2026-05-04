<?php if(!empty($returns_for_custom_dn)){ ?>
  <a href="<?php echo $_SERVER['REQUEST_URI'];?>#damaged_by_courier_cmpny_form">
    <h3>
      <u>Generate Custom Debit Notes>>>>>></u>
    </h3>
  </a>
<?php } ?>

<!-- ##################### Seller Debit Notes Starts ###################  -->
<?php  
if(!empty($debit_notes)){ 
  foreach($debit_notes as $seller_id => $dns){
    $unique_dn = array();
?>
<table style="width:100%;" border="0" >
  <tr>
    <td>
      <h4><?php echo !empty($sellers[$seller_id]['company']) ? $sellers[$seller_id]['company']: ''; ?> - <?php echo !empty($sellers[$seller_id]['nickname']) ? $sellers[$seller_id]['nickname']: ''; ?></h4>
    </td>
    <?php 
      foreach($dns as $return_id => $debit_note) {
        $dn_id = $debit_note['debit_note_id'];
        $unique_dn[$dn_id] = $debit_note;
      }
    foreach($unique_dn as $dn_id => $debit_note) { ?>
    <td style="width:11%; text-align:right;">
      <div>
        <span><b><?php echo $debit_note['debit_note_no']; ?></b></span>&nbsp
        <a href="<?php echo $debit_note['debit_note_dload']; ?>" class="btn btn-sm btn-warning" id="debit_note_no_<?php echo $debit_note['debit_note_no']; ?>">
          <i class="fa fa-download"></i>
        </a>
        <?php if( $debit_note['debit_note_status'] && strtolower($admin_mode) == 'on' ) { ?>
        <button class="btn btn-sm btn-danger debit_note_remove" 
          type="button" 
          data-dn-type = "seller"
          data-debit-no="<?php echo $debit_note['debit_note_no']; ?>" 
          data-debit-id="<?php echo $debit_note['debit_note_id']; ?>" 
          data-order-id="<?php echo $order_id; ?>"
        > 
          <i class="fa fa-close"></i> 
        </button>
        <?php } ?>
      </div>
      <span><b>(<?php echo $debit_note['date_show'];?>)</b></span>
    </td>
    <?php } ?>
  </tr>
</table><br>
<table class="table table-bordered">
  <thead>
    <tr>
      <td class="text-left">Debit Note Id</td>
      <td class="text-left">Debit Note No.</td>
      <td class="text-left">Debit Note Date</td>
      <td class="text-left"><?php echo $column_sku; ?></td>
      <td class="text-left"><?php echo $column_desc; ?></td>
      <td class="text-left">Payment Status</td>
      <td class="text-left">DebitNote Amount</td>
      <td class="text-right"><?php echo $column_piece_in_set; ?></td>
      <td class="text-right"><?php echo $column_ttl_rtn_pcs; ?></td>
      <td class="text-right"><?php echo $column_tax_per_pcs; ?></td>
      <td class="text-right">Transfer Price / Piece</td>
      <td class="text-right"><?php echo $column_transfer_price; ?></td>
      <td class="text-right"><?php echo $column_return_reason; ?></td>
      <td class="text-right"><?php echo $column_return_action; ?></td>
      <td class="text-right"><?php echo $column_user; ?></td>
    </tr>
  </thead>
  <tbody>   
    <?php
    if(!empty($dns)){
      foreach($dns as $return_id => $debit_note){
        $op_id = $debit_note['order_product_id'];
    ?>
    <tr>
      <td><?php echo $debit_note['debit_note_id']; ?></td>
      <td><b><?php echo $debit_note['debit_note_prefix'].$debit_note['debit_note_no']; ?></b></td>
      <td>(<?php echo $debit_note['date_show']; ?>)</td>
      <td class="text-left" style="width:5%">
        <?php echo !empty($products[$op_id]['seller_sku']) ? $products[$op_id]['seller_sku'] : ''; ?>
        <i><?php echo !empty($oop_options[$op_id]['name'])? 
          "<br>".$oop_options[$op_id]['name'].' : '.$oop_options[$op_id]['value'] :
          '' ?> 
        </i>
        <?php
          if($products[$op_id]['is_returnable'] == 0){
            echo '<span style="color: red;"><br>(Non-Returnable Product)</span>';
          }
        ?>
      </td>
      <td class="text-left" style="width:25%;">
        <b><i><?php echo !empty($products[$op_id]['model']) ? $products[$op_id]['model'] : ''; ?></i></b><br>
        <?php echo !empty($products[$op_id]['name']) ? $products[$op_id]['name'] : ''; ?>
      </td>
      <td>
        <?php echo $debit_note['trxn_done'];?><br>
        Amount: <?php echo !empty($debit_note['trxn_amount'])?$debit_note['trxn_amount'] : 0; ?>
      </td>
      <td>
        <?php echo @$debit_note['debit_note_amount'];?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['piece_in_set']) ? $products[$op_id]['piece_in_set'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['quantity']) ? $debit_note['quantity'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo (float)$products[$op_id]['seller_tax_per_piece'] ; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['transfer_price_per_piece']) ? $products[$op_id]['transfer_price_per_piece'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['transfer_price_per_piece']) ? $products[$op_id]['transfer_price_per_piece']*$debit_note['quantity'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['return_reason_name']) ? $debit_note['return_reason_name'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['return_action_name']) ? $debit_note['return_action_name'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['user']) ? $debit_note['user'] : ''; ?>
      </td>
    </tr>
    <?php
      }
    }
    ?>
  </tbody>
</table>
<?php
  }
}
?>
<!-- ##################### Seller Debit Notes Ends ###################  -->

<!-- ##################### Custom Debit Notes Starts ###################  -->
<?php  
if(!empty($custom_debit_notes)){ 
  $unique_dn = array();
?>
<table style="width:100%;" border="0" >
  <tr>
    <td>
      <h4><b>Custom Debit Note(s):</b></h4>
    </td>
      <?php 
        foreach($custom_debit_notes as $return_id => $debit_note) { 
          $unique_dn[$debit_note['debit_note_id']] = $debit_note;
        }
      ?>
      <?php foreach($unique_dn as $debit_note) { ?>
      <td style="width:11%; text-align:right;">
        <div>
          <span><b><?php echo $debit_note['debit_note_no']; ?></b></span>&nbsp
          <a href="<?php echo $debit_note['debit_note_dload']; ?>" class="btn btn-sm btn-warning" id="debit_note_no_<?php echo $debit_note['debit_note_no']; ?>">
          <i class="fa fa-download"></i>
          </a>
          <?php if( $debit_note['debit_note_status'] && strtolower($admin_mode) == 'on' ) { ?>
            <button class="btn btn-sm btn-danger debit_note_remove" 
            type="button" 
            data-dn-type = "custom"
            data-debit-no="<?php echo $debit_note['debit_note_no']; ?>" 
            data-debit-id="<?php echo $debit_note['debit_note_id']; ?>" 
            data-order-id="<?php echo $order_id; ?>"
            > 
              <i class="fa fa-close"></i> 
            </button>
          <?php } ?>
        </div>
        <span><b>(<?php echo $debit_note['date_show'];?>)</b></span>
      </td>
      <?php } ?>
  </tr>
</table><br>
<table class="table table-bordered">
  <thead>
    <tr>
      <td class="text-left">Debit Note Id</td>
      <td class="text-left">Debit Note No.</td>
      <td class="text-left">Debit Note Date</td>
      <td class="text-left"><?php echo $column_sku; ?></td>
      <td class="text-left"><?php echo $column_desc; ?></td>
      <td class="text-left">Payment Status</td>
      <td class="text-left">DebitNote Amount</td>
      <td class="text-right">DN Ref.</td>
      <td class="text-right"><?php echo $column_ttl_rtn_pcs; ?></td>
      <td class="text-right"><?php echo $column_tax_per_pcs; ?></td>
      <td class="text-right">Transfer Price / Piece</td>
      <td class="text-right">Price / Piece</td>
      <td class="text-right"><?php echo $column_return_reason; ?></td>
      <td class="text-right"><?php echo $column_return_action; ?></td>
      <td class="text-right"><?php echo $column_user; ?></td>
      <?php if($admin_mode == 'on'){ ?>
        <td class="text-right">Action</td>
      <?php } ?>
    </tr>
  </thead>
  <tbody>   
    <?php 
    foreach($custom_debit_notes as $return_id => $debit_note){
      $op_id = $debit_note['order_product_id'];
    ?>
    <tr>
      <td><?php echo $debit_note['debit_note_id']; ?></td>
      <td><b><?php echo $debit_note['debit_note_prefix'].$debit_note['debit_note_no']; ?></b></td>
      <td>(<?php echo $debit_note['date_show']; ?>)</td>
      <td class="text-left" style="width:5%">
        <?php echo !empty($products[$op_id]['seller_sku']) ? $products[$op_id]['seller_sku'] : ''; ?>
        <i><?php echo !empty($oop_options[$op_id]['name'])? 
          "<br>".$oop_options[$op_id]['name'].' : '.$oop_options[$op_id]['value'] :
          '' ?> 
        </i>
      </td>
      <td class="text-left" style="width:25%;">
        <b><i><?php echo !empty($products[$op_id]['model']) ? $products[$op_id]['model'] : ''; ?></i></b><br>
        <?php echo !empty($products[$op_id]['name']) ? $products[$op_id]['name'] : ''; ?>
      </td>
      <td>
        <?php echo $debit_note['trxn_done'];?><br>
        Amount: <?php echo !empty($debit_note['trxn_amount'])?$debit_note['trxn_amount'] : 0; ?>
      </td>
      <td>
        <?php echo @$debit_note['debit_note_amount'];?>
      </td>
      <td class="text-right">
        <?php echo nl2br($debit_note['custom_debit_note_ref']);?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['quantity']) ? $debit_note['quantity'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo (float)$products[$op_id]['seller_tax_per_piece'] ; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['transfer_price_per_piece']) ? $products[$op_id]['transfer_price_per_piece'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['price_per_piece']) ? $products[$op_id]['price_per_piece']+$products[$op_id]['discount_per_piece'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['return_reason_name']) ? $debit_note['return_reason_name'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['return_action_name']) ? $debit_note['return_action_name'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['user']) ? $debit_note['user'] : ''; ?>
      </td>
      <?php if($admin_mode == 'on'){ ?>
        <td class="text-right">
          <a 
            href="javascript:void(0);"
            data-dn-id="<?php echo $debit_note['debit_note_id'];?>" 
            data-order-no="<?php echo $order_no;?>"
            data-custom-debit-note-ref="<?php echo $debit_note['custom_debit_note_ref'];?>"
            id="custom_dn_<?php echo $debit_note['debit_note_id'];?>" 
            class="custom_dn_edit_btn" >
            <i class="fa fa-edit fa-2x" aria-hidden="true"></i>
          <a>
        </td>
      <?php } ?>
    </tr>
    <?php
    }
    ?>
  </tbody>
</table>
<?php
}
?>
<!-- ##################### Custom Debit Notes Ends ###################  -->

<!-- ##################### Cancelled Debit Notes Starts ###################  -->
<?php  
if(!empty($cancelled_debit_notes)){ 
  $unique_dn = array();
?>
<table style="width:100%;" border="0" >
  <tr>
    <td>
      <h4><b>Cancelled Debit Note(s):</b></h4>
    </td>
      <?php foreach($cancelled_debit_notes as $debit_note) { ?>
      <td style="width:11%; text-align:right;">
        <div>
          <span><b><?php echo $debit_note['debit_note_no']; ?></b></span>&nbsp
          <a href="<?php echo $debit_note['debit_note_dload']; ?>" class="btn btn-sm btn-warning" id="debit_note_no_<?php echo $debit_note['debit_note_no']; ?>">
          <i class="fa fa-download"></i>
          </a>
        </div>
        <span><b>(<?php echo $debit_note['date_added'];?>)</b></span>
      </td>
      <?php } ?>
  </tr>
</table><br>
<table class="table table-bordered">
  <thead>
    <tr>
      <td class="text-left">Debit Note Id</td>
      <td class="text-left">Debit Note No.</td>
      <td class="text-left">Debit Note Date</td>
      <td class="text-left">Payment Status</td>
      <td class="text-left">DebitNote Amount</td>
      <td class="text-right"><?php echo $column_user; ?></td>
    </tr>
  </thead>
  <tbody>   
  <?php foreach($cancelled_debit_notes as $return_id => $debit_note){ ?>
    <tr class="btn-danger">
      <td><?php echo $debit_note['debit_note_id']; ?></td>
      <td><b><?php echo $debit_note['debit_note_prefix'].$debit_note['debit_note_no']; ?></b></td>
      <td>(<?php echo $debit_note['date_added']; ?>)</td>
      <td>
        <?php echo $debit_note['trxn_done'];?><br>
        Amount: <?php echo !empty($debit_note['trxn_amount'])?$debit_note['trxn_amount'] : 0; ?>
      </td>
      <td>
        <?php echo @$debit_note['debit_note_amount'];?>
      </td>
      <td class="text-right">
        <?php echo !empty($debit_note['user']) ? $debit_note['user'] : ''; ?>
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php
}
?>
<!-- ##################### Cancelled Debit Notes Ends ###################  -->



<!-------------------------------Custom Debit Note Section-------------------------------->
<?php if(!empty($returns_for_custom_dn)) { ?>
  <form action="<?php echo $gnrt_dbt_note_no; ?>" method="post" enctype="multipart/form-data" id="damaged_by_courier_cmpny_form" class="form-horizontal">
    <input type="hidden" name="return_type" value="damaged_by_courier_cmpny">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <input type="hidden" name="order_no" value="<?php echo $order_no; ?>">
    <div class="parcel-lost-returns">
      <div class="block-50-50 courier-cmpny-listing">
        <div class="col-sm-12 custom_parties ">
          <fieldset>
            <div class="col-sm-12 listing-title">
              <legend>Custom Party(s) :</legend>
              <button type="button" id="add_custom_party_btn" class="btn btn-primary pull-right" data-toggle="modal" data-target="#add_custom_party_form">+ Add</button>
            </div>
            <div class="form-group">
              <div class="col-sm-4">
                <input type="text" name="filter_firm_name" class="form-control" placeholder="Search By Firm Name" value="" />
              </div>
              <div class="col-sm-4">
                <input type="text" name="filter_city" class="form-control" placeholder="Search City" value=""/>
              </div>
              <div class="col-sm-3">
                <button type="button" name="search" class="btn btn-primary" id="btn-search-custom-party"><?php echo $button_search; ?></button>
              </div>
            </div>
            <div class="custom_address_list">
              <?php if(isset($custom_parties)){
                        foreach($custom_parties as $address_list){
                          $address_1 = '';
                          $address_2 = '';
                          if(!empty($address_list['address1'])){
                            $address_1 = $address_list['address1'] . '<br>';
                          }
                          if(!empty($address_list['address2'])){
                            $address_2 = $address_list['address2'] . '<br>';
                          }
              ?>
              <div class="col-sm-3 bottom-padding">
                <label class="custom_lbl" for="select_custom_id-<?php echo $address_list['custom_id'];?>" >
                  <div class="custom-party-address address-div warehouse_address_height_set" id="custom_id_<?php echo $address_list['custom_id'];?>" data-address-id="<?php echo $address_list['custom_id'];?>">
                      <?php echo $address_list['firm_name'];?><br>
                      <?php echo $address_1; ?>
                      <?php echo $address_2; ?>
                      <?php echo $address_list['city'];?> - 
                      <?php echo $address_list['pincode'];?><br>
                      <?php echo $address_list['state'];?> - 
                      <?php echo $address_list['country'];?><br>
                      <?php echo 'GST Number: '.$address_list['gst_number'];?>
                  </div>
                </label>
                <div class="address_select">
                  <input type="radio" id="select_custom_id-<?php echo $address_list['custom_id'];?>" class="select_custom_id" name="select_custom_id" value="<?php echo $address_list['custom_id'];?>" />
                  <label class="" for="select_custom_id-<?php echo $address_list['custom_id'];?>" ><?php echo $address_list['state'];?> - <?php echo $address_list['country'];?> </label>
                </div>
              </div>
              <?php } ?>
              <?php }else{ ?>
              No Result for <b>Custom Party(s)</b>.
              <?php } ?>
            </div>
          </fieldset>
        </div>
      </div>
      <div class="block-50-50 ">
        <div class="col-sm-12">
          <div class="col-sm-2">
            <label for="custom_dn_value">DebitNote Amt: <br>(<i>Tax Exclusive Amount</i>)</label>
          </div>
          <div class="col-sm-3">
            <input type="text" name="custom_dn_value" class="form-control" value="0.00" id="custom_dn_value" />
          </div>
          <div class="col-sm-2 "></div>
          <div class="col-sm-2">
            <label for="custom_debit_note_ref">Debit Note Ref: </label>
          </div>
          <div class="col-sm-3">
            <input type="text" name="custom_debit_note_ref" class="form-control" value="" id="custom_debit_note_ref" />
          </div>
        </div>
        <div class="col-sm-12 listing-title">
          <legend>Custom Return(s) :</legend>
        </div>
        <?php foreach($returns_for_custom_dn as $return){ ?>
        <?php $suborder_id = $products[$return['order_product_id']]['suborder_id']; ?>
        <label for="custom_rtn_<?php echo $return['return_id'];?>">
        <div class="pending-debit-div">
          <input type="checkbox" class="check_checkbox select_custom_return" name="return_ids[]" value="<?php echo $return['return_id'];?>" id="custom_rtn_<?php echo $return['return_id'];?>" /><br>
          <input type="hidden" name="suborder_id_<?php echo $return['return_id']; ?>" value="<?php echo $suborder_id; ?>">
          <input type="hidden" name="seller_id_<?php echo $return['return_id']; ?>" value="<?php echo $products[$return['order_product_id']]['seller_id'];?>">
          <span>
            <b>SuborderId : <?php echo !empty($suborder_id) ? $suborder_id : '';?></b>
          </span><br>
          <span><b>SKU :</b> </span><span><?php echo !empty($products[$return['order_product_id']]) ? $products[$return['order_product_id']]['seller_sku'] : '';?></span><br>
          <span><b>Product Description :</b> </span><span><?php echo !empty($products[$return['order_product_id']]) ? $products[$return['order_product_id']]['name'] : '';?></span><br>
          <span><b>Total Return Pieces :</b> </span><span><?php echo !empty($return['quantity']) ? $return['quantity'] : '';?></span><br>
        </div>
        </label>
        <?php }?>
        <div>
            <button id="save_debit_note" type="button" form= "damaged_by_courier_cmpny_form" data-toggle="tooltip" title="" class="btn btn-warning debit_note_btn" data-original-title="Generate Debit Note"><i class="fa fa-cog"></i> Generate </button>
        </div>
      </div>
    </div>
  </form>  
<?php }?>

<!-- Starts Edit Custom DN Customer Ref pop-up -->
<div id="edit_custom_dn_pop_up" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Custom DN Ref Note</h4>
      </div>
      <div class="edit_cn_modal_body modal-body">
          <input type="hidden" name="custom_dn_id" id="custom_dn_id" value="" />
          <input type="hidden" name="custom_debit_note_ref" id="custom_debit_note_ref" value="" />
          <input type="hidden" name="custom_dn_order_no" id="custom_dn_order_no" value="" />
          <div name="cn_comment_div col-sm-12">
            <label class="col-sm-3" for="cn_comment">Custom DN Ref: </label>
            <input type="text" name="edit_custom_debit_note_ref" id="edit_custom_debit_note_ref" placeholder="DN Ref Note" class="form-control" style="width: 300px;">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" title="Submit" class="btn btn-primary submit_custom_debit_note_ref">Submit</button>
        <button type="button" class="btn btn-default edit_custom_dn_close" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End Edit Custom DN Customer Ref pop-up -->



<!-- Add Custom Parties Pop-up -->
<div class="modal fade" id="add_custom_party_form" role="dialog"><?php echo $add_custom_party_popup;?></div>
<script type="text/javascript">

$('#btn-search-custom-party').click(function () {
  $('.custom_address_list').empty();
  $.ajax({
    url : 'index.php?route=sale/return/getSearchAddress&token=<?php echo $token;?>',
    type: 'POST',
    dataType: 'json',
    data: '&filter_firm_name=' + encodeURIComponent($('input[name=\'filter_firm_name\']').val()) +
           '&filter_city=' + encodeURIComponent($('input[name=\'filter_city\']').val()),
    beforeSend: function() {
      $('#btn-search-custom-party').button('loading');
    },
    complete: function() {
      $('#btn-search-custom-party').button('reset');
    },
    success: function(json) {
      if(Object.keys(json).length == 0){
        $('.custom_address_list').append('No Result for <b>Custom Party(s)</b>.');
      }else{
        $.each(json, function(index,element){
          var firm_address1 = '';
          var firm_address2 = '';
          if(element.address_1 != ''){
            firm_address1 = element.address_1 + '<br>';
          }
          if(element.address_2 != ''){
            firm_address2 = element.address_2 + ' <br>';
          }
          
          $('.custom_address_list').append(
              '<div class="col-sm-3 bottom-padding">' +
                  '<label class="custom_lbl" for="select_custom_id-'+element.custom_id+'" >'+
                    '<div class="custom-party-address address-div warehouse_address_height_set" id="warehouse_id_'+element.custom_id+'" data-address-id="'+element.custom_id+'">'
                        + element.firm_name +'<br>'
                        + firm_address1 
                        + firm_address2 
                        + element.city +'-'
                        + element.pincode +'<br>' 
                        + element.state +'-'
                        + element.country+'<br>'
                        + element.gst_number
                    +'</div>'
                  +'</label>'
                  +'<div class="address_select">' +
                    '<input type="radio" id="select_custom_id-'+element.custom_id+'" class="select_custom_id" name="select_custom_id" value="'+element.custom_id+'" />' +
                    '<label for="select_custom_id-'+element.custom_id+'" >'+element.state+' , '+ element.country +'</label>' +
                   '</div>' +
              '</div>'
          );
        });
      }
    }
  });
});
$('select[name="country"]').on('change', function() {
  country_id = this.value;
  $.ajax({
      type:'post',
      url:'index.php?route=sale/order/getZones&token=<?php echo $token; ?>',
      data:{country_id},
      dataType:'json',
      success: function(json) {
          html = '<option value=""><?php echo $text_select; ?></option>';
          if (json['zones'] && json['zones'] != '') {
              for (i = 0; i < json['zones'].length; i++) {
                  html += '<option value="' + json['zones'][i]['zone_id'] + '"';
                  html += '>' + json['zones'][i]['name'] + '</option>';
              }

          } 
          $('select[name=\'zone\']').html(html);
      }
  });
});
</script>
<style type="text/css">
.debit_note_info{background: #000;color: #fff;text-align: center;}
.block-50-50{display: inline-block;width:90%;margin: 10px;padding: 15px;float:left;}
.parcel-lost-returns{border:1px solid #E0E0E0;}
.pending-debit-div{border: 1px solid #E0E0E0;display: inline-block;padding: 7px;border-radius: 5px;margin-right: 10px;min-height: 150px;vertical-align: top;} 
.debit_note_btn{margin-top: 15px;}
.listing-title {display:block; padding: 0px; clear: both; border-bottom: 1px solid #e5e5e5;margin-bottom: 17px;}
.listing-title legend{float: left;width: 70%; border-bottom: 0px;margin-bottom: 0px; }
.listing-title .btn{padding: 3px 6px;margin-bottom: 8px;}
.required_field{color: red;}
.warehouse_addresses_list{border: 1px solid #E0E0E0!important;}
.address-div{border: 1px solid #E0E0E0;width: 100%;display: inline-block;padding: 7px;border-radius: 5px;margin-right: 10px;min-height: 180px;vertical-align: top;} 
.bottom-padding{padding-bottom: 15px;}
</style>