<div id="history_invoice_no_check" data-value="<?php if(!empty($invoice_no)){ echo '1'; } else { echo '0';}?>"></div>
<div id="history"></div>
  <br />
  <div class="col-sm-12">
    <div class="form-group">
      <label class="control-label" for="input-notify">Tracking No.:</label>
      <b><span id="tracking_no_text"><?php echo ($tracking_no) ? $tracking_no : 'Not Generated!!'; ?></span></b>
    </div>
  </div>
  <div class="col-sm-12">
    <div class="form-group">
      <label class="control-label" for="input-notify">Courier Partner:</label>
      <b><span id="courier_partner_text"><?php echo ($courier_partner) ? $courier_partner : 'Not Provided!!'; ?></span></b>
    </div>
  </div>

<?php if (in_array($order_status_id, array('1', '9', '16')) && $franchise_id == 0) { ?>
<div class="col-sm-12">
    <div class="form-group" style="display: inline-block;">
        <label class="switch">
            <?php if ($no_wsb_tape) { ?>
            <input type="checkbox" id="no_wsb_tape" class="order_special_request" checked old-value="1"/>
            <?php } else { ?>
            <input type="checkbox" id="no_wsb_tape" class="order_special_request" old-value="0"/>
            <?php } ?>
            <span class="slider round"></span>
        </label>
        <label style="margin-left:10px; vertical-align:top; margin-top: 4px; font-size: 14px;"><?php echo $text_no_wsb_tape; ?></label>
    </div>

    <div class="form-group" style="display: inline-block; margin-left: 40px;">
        <label class="switch">
            <?php if ($no_invoice_with_shipment) { ?>
            <input type="checkbox" id="no_invoice_with_shipment" class="order_special_request" checked old-value="1"/>
            <?php } else { ?>
            <input type="checkbox" id="no_invoice_with_shipment" class="order_special_request" old-value="0"/>
            <?php } ?>
            <span class="slider round"></span>
        </label>
        <label style="margin-left:10px; vertical-align:top; margin-top: 4px; font-size: 14px;"><?php echo $text_no_offline_invoice; ?></label>
    </div>
</div>

<div class="col-sm-12">
  <div style="font-size: 14px; margin-bottom: 10px;">
    <label><?php echo $text_courier_preferences; ?>: &nbsp;</label><label id="courier_partner_preference_label" style="color:green"><?php echo $courier_partner_preference; ?></label>
  </div>
</div>
<?php } ?>

  <div class="col-sm-12">
    <fieldset>
      <legend><?php echo $text_history; ?></legend>
      <?php if ($can_add_history) { ?>
        <div>
          <form class="form-horizontal">
            <div class="form-group" id ="selectstatus">
              <?php 
                  $isDisabled = false;
                if( (isset($data['rbl_dpd_status']) && $data['rbl_dpd_status'] > 0) 
                      && 
                    !in_array($this->user->getId(), explode(',',ADMIN_IDS))
                  )
                  { 
                    $isDisabled = true;
                  }
                ?>
              <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
              <div class="col-sm-10" >
                <select name="order_status_id" id="input-order-status" class="form-control"  <?php if($isDisabled){ echo 'disabled="true"'; }?> >
                  <!-- <?php foreach ($order_statuses as $order_st) { ?>
                  <?php if ($order_st['order_status_id'] == $order_status_id) { ?>
                  <option value="<?php echo $order_st['order_status_id']; ?>" selected="selected"><?php echo $order_st['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_st['order_status_id']; ?>"><?php echo $order_st['name']; ?></option>
                  <?php } ?>
                  <?php } ?> -->
                </select>
              </div>
               

            </div>
            <div class="form-group" style= "display: none;" id = "shippingco" >
              <label class="col-sm-2 control-label" for="input-notify">Shipping Co</label>
              <div class="col-sm-10">
                <select name="shippingco" id="input-shipping" class="form-control">
                  <option value="">----Select Courier----</option>
                  <?php foreach ($courier_partners as $courier_partners) { ?>
                  <option value="<?php echo $courier_partners['courier_name']; ?>"><?php echo $courier_partners['courier_name']; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group" style= "display: none;" id = "tracking">
              <label class="col-sm-2 control-label" for="input-notify">Tracking No</label>
              <div class="col-sm-10">
                <input type="text" name="tracking" id="input-tracking" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-notify"><?php echo $entry_notify; ?></label>
              <div class="col-sm-1">
                <input type="checkbox" name="notify" value="1" id="input-notify" />
              </div>
              <label class="col-sm-2 control-label" for="input-notify"><?php echo $entry_dont_csh_bck; ?></label>
              <div class="col-sm-1">
                <input type="checkbox" name="dont_csh_bck" value="false" id="input-notify" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-notify"><?php echo $entry_notify_sms; ?></label>
              <div class="col-sm-10">
                <input type="checkbox" name="notify_sms" value="1" id="input-notify-sms" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-comment"><?php echo $entry_comment; ?></label>
              <div class="col-sm-10">
                <textarea name="comment" rows="8" id="input-comment" class="form-control input-comment" ></textarea>
                <select name="comment" id="input-comment" class="form-control input-comment">
                  <option value="*">--SELECT--</option>
                  <?php foreach($order_cancelled_status as $ord_cancled_data){ ?>
                    <option value="<?php echo $ord_cancled_data; ?>"><?php echo $ord_cancled_data; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-notes"><?php echo $entry_notes; ?></label>
              <div class="col-sm-10">
                <textarea name="notes" rows="8" id="input-notes" class="form-control"></textarea>
                <span class="error_message"></span>
              </div>
            </div>
          </form>
          <div class="text-right">
            <button id="button-history" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_history_add; ?></button>
          </div>
        </div>
      <?php } else { ?> 
        <div>
          <label><?php echo $history_add_error; ?></lable>
        </div>
      <?php } ?>
    </fieldset>
  </div>
  <?php
  echo $deleted_order_history;
  ?>
  <?php if( $order_status_id == 0){ ?>
          <input type="hidden" class="missing_order_redirect" value="<?php echo $sale_order_link_with_order_no; ?>" />
  <?php } ?>

  <script type="text/javascript">
    // Pagination for history
    $('#history').delegate('.pagination a', 'click', function(e) {
      e.preventDefault();

      $('#history').load(this.href);
    });

    // Display history
    $('#history').load('index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>');

    // Update History - On History Button Click
    $('#button-history').on('click', function() {
        
      var order_status_id = $('select[name=\'order_status_id\']').val();
      var comment = '';
      if( order_status_id == 2){
        comment = $('select.input-comment').val();
      } else {
        comment = $('textarea.input-comment').val();
      }
      
    if(order_status_id == '14') {  
        var shippingco = $('select[name=\'shippingco\']').val();
        if($.trim(shippingco)=='') {
            alert('Select shipping partner');
            return false;
        }
        var docket_no = $('input[name=\'tracking\']').val();
        if($.trim(docket_no)=='') {
            alert('Enter tracking number');
            return false;
        }
    } 
      
      $.ajax({
        url: 'index.php?route=sale/order/setOrderHistory&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id='+order_status_id,        
        type: 'post',
        dataType: 'json',
        data: 'order_status_id=' + encodeURIComponent($('select[name=\'order_status_id\']').val()) +
              '&notify_email=' + ($('input[name=\'notify\']').prop('checked') ? 1 : 0) +
              '&notify_sms=' + ($('input[name=\'notify_sms\']').prop('checked') ? 1 : 0) +
              '&comment=' + encodeURIComponent(comment) +
              '&notes=' + encodeURIComponent($('textarea[name=\'notes\']').val()) +
              '&shippingco=' + encodeURIComponent($('select[name=\'shippingco\']').val()) +
              '&tracking=' + encodeURIComponent($('input[name=\'tracking\']').val()) +
              '&give_cashback=' + ($('input[name=\'dont_csh_bck\']').prop('checked') ? 0 : 1) +
              '&user=' + encodeURIComponent('<?php echo $user; ?>') +
              '<?php echo $order_status_id == 0 ? "&missing_order=1" : ""; ?>',
        beforeSend: function() {
          $('#button-history').button('loading');
        },
        complete: function() {
          $('#button-history').button('reset');
        },
        success: function(json) {
          $('.alert').remove();

          if (json['error']) {
            $('#history').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
          }

          if (json['success']) {
            $('#history').load('index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>');

            $('#history').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

            $('textarea[name=\'comment\']').val('');
            $('textarea[name=\'notes\']').val('');

            $('#order-status').html($('select[name=\'order_status_id\'] option:selected').text());

            if($('.missing_order_redirect').length > 0){
                location.replace($('.missing_order_redirect').val());
            }

            if(order_status_id == 8 ){
              $('#button-history').addClass('disabled');
            }

            $('#tracking_no_text').text($('input[name=\'tracking\']').val());
            $('#courier_partner_text').text($('select[name=\'shippingco\']').val());

          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    });

  $(document).ready(function(){
    var order_status_id = $("select[name=\'order_status_id\']").val();
    if( order_status_id == 2 ){
      $("textarea#input-comment").hide();
      $("select#input-comment").show();
    }else{
      $("textarea#input-comment").show();
      $("select#input-comment").hide();
    }

    $("select[name=\'order_status_id\']").on('change',function(){
      if( $(this).val() == 2 ){
        $("textarea#input-comment").hide();
        $("select#input-comment").show();
        $("#button-history").attr('disabled',true);
      }else{
        $("textarea#input-comment").show();
        $("select#input-comment").hide();
      }

      if( $(this).val() == 8 ){
        var comment = $('#input-comment').val();
        var notes = $('#input-notes').val();

        if(comment.trim().length < 50 && notes.trim().length < 50){
          $('#button-history').addClass('disabled');
          $('#input-notes').after('<p>Please fill comment or notes with at least 50+ character.</p>');
        }else{
          $('#button-history').removeClass('disabled');
        }

        $('#input-comment').on('keyup',function(){
          var comment_box = $(this).val();
          if(comment_box.trim().length < 50){
            $('#button-history').addClass('disabled');
          }else{
            $('#button-history').removeClass('disabled');
          }
        });

        $('#input-notes').on('keyup',function(){
          var notes_box = $(this).val();
          if( notes_box.trim().length < 50){
            $('#button-history').addClass('disabled');
          }else{
            $('#button-history').removeClass('disabled');
          }
        });

      }else{
        $('#input-notes').next('p').remove();
        $('#input-comment').val('');
        $('#input-notes').val('');
        $('#button-history').removeClass('disabled');
      }
    });

    $("select#input-comment").on('change',function(){
      var cancelled_comment = $(this).val();
      if( cancelled_comment.length > 1){
        $("#button-history").attr('disabled',false);
      } else {
        $("#button-history").attr('disabled',true);
      }
    });


    if(order_status_id == 8 ){
      var comment = $('#input-comment').val();
      var notes = $('#input-notes').val();

      if(comment.trim().length < 50 && notes.trim().length < 50){
        $('#button-history').addClass('disabled');
        $('#input-notes').after('<p>Please fill comment or notes with at least 50+ character.</p>');
      }else{
        $('#button-history').removeClass('disabled');
      }
    }

      $('#no_wsb_tape').on('click', function() {
          var no_wsb_tape_data = document.getElementById('no_wsb_tape');
          var old_value = no_wsb_tape_data.attributes['old-value'].value;
          var new_value = no_wsb_tape_data.checked ? 1 : 0;
          var retVal = confirm("Are you sure, you want to change \'<?php echo $text_no_wsb_tape; ?>\' status?");
          if( retVal == false ){
              if ( old_value == 1) $('#no_wsb_tape').prop('checked', true);
              else $('#no_wsb_tape').prop('checked', false);
              return;
          }


          $.ajax({
              url: 'index.php?route=sale/order/updateWSBTapeStatus&token=<?php echo $token; ?>',
              type: 'post',
              dataType: 'json',
              data: {
                  'order_id': '<?php echo $order_id; ?>',
                  'suborder_id': '<?php echo $suborder_id; ?>',
                  'no_wsb_tape': new_value
              },
              success: function(json) {
                  if (json['error']) {
                      if ( old_value == 1) $('#no_wsb_tape').prop('checked', true);
                      else $('#no_wsb_tape').prop('checked', false);
                      alert(json['error']);
                  }

                  if (json['success']) {
                      no_wsb_tape_data.attributes['old-value'].value = new_value;
                      alert(json['success']);
                  }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                  if ( old_value == 1) $('#no_wsb_tape').prop('checked', true);
                  else $('#no_wsb_tape').prop('checked', false);
                  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
          });

      });

      $('#no_invoice_with_shipment').on('click', function() {
          var no_invoice_with_shipment_data = document.getElementById('no_invoice_with_shipment');
          var old_value = no_invoice_with_shipment_data.attributes['old-value'].value;
          var new_value = no_invoice_with_shipment_data.checked ? 1 : 0;
          var retVal = confirm("Are you sure, you want to change \'<?php echo $text_no_offline_invoice; ?>\' status?");
          if( retVal == false ){
              if ( old_value == 1) $('#no_invoice_with_shipment').prop('checked', true);
              else $('#no_invoice_with_shipment').prop('checked', false);
              return;
          }

          $.ajax({
              url: 'index.php?route=sale/order/updateSendInvoiceStatus&token=<?php echo $token; ?>',
              type: 'post',
              dataType: 'json',
              data: {
                  'order_id': '<?php echo $order_id; ?>',
                  'suborder_id': '<?php echo $suborder_id; ?>',
                  'no_invoice_with_shipment': new_value
              },
              success: function(json) {
                  if (json['error']) {
                      if ( old_value == 1) $('#no_invoice_with_shipment').prop('checked', true);
                      else $('#no_invoice_with_shipment').prop('checked', false);
                      alert(json['error']);
                  }

                  if (json['success']) {
                      no_invoice_with_shipment_data.attributes['old-value'].value = new_value;
                      alert(json['success']);
                  }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                  if ( old_value == 1) $('#no_invoice_with_shipment').prop('checked', true);
                  else $('#no_invoice_with_shipment').prop('checked', false);
                  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
          });
      });
  });
  </script>
<script type="text/javascript">
  $(document).ready(function(){
    var order_status = $.parseJSON('<?php echo json_encode($order_statuses);?>');
    var selected_order_status = '<?php echo $order_status_id;?>';
    var invoice_generated_value = $('#history_invoice_no_check').attr('data-value');
    var admin_id_string = '<?php echo ADMIN_IDS; ?>';
    var admin_id_array = admin_id_string.split(',');
    var current_user_id =  '<?php echo $this->user->getId(); ?>';
    var blocking_order_status = [];
    html = '';

    if($.inArray(current_user_id,admin_id_array) !== -1 ){
      $.each( order_status, function( key, value ) {
        html += '<option value="' +value['order_status_id']+ '"' ;
        if(value['order_status_id'] == selected_order_status ){
          html += ' selected="selected"';
        }
        html += '>' + value['name'] + '</option>';        
      });  

    } else {
      $.each( order_status, function( key, value ) {
        if(invoice_generated_value > 0){
          if(selected_order_status == 15){
            html +='';
            if(value['order_status_id'] == 5 || value['order_status_id'] == 15){
              html += '<option value="' +value['order_status_id']+ '"' ;
              html += ' selected="selected"';
              html += '>' + value['name'] + '</option>';  
            }
          } else if(selected_order_status == 5){
            html +='';
            if(value['order_status_id'] == 5 ){
              html += '<option value="' +value['order_status_id']+ '"' ;
              html += ' selected="selected"';
              html += '>' + value['name'] + '</option>';  
            }
          } else {
            html += '<option value="' +value['order_status_id']+ '"' ;
            if(value['order_status_id'] == selected_order_status ){
              html += ' selected="selected"';
            }
            html += '>' + value['name'] + '</option>';  
          }
          
        } else {
          if(selected_order_status == 0 ){
            if(value['order_status_id']==1){
              html += '<option value="' +value['order_status_id']+ '"' ;
              if(value['order_status_id'] == selected_order_status ){
                html += ' selected="selected"';
              }
              html += '>' + value['name'] + '</option>';  
            }            
          } else if($.inArray(value['order_status_id'],blocking_order_status) == -1){
            html += '<option value="' +value['order_status_id']+ '"' ;
            if(value['order_status_id'] == selected_order_status ){
              html += ' selected="selected"';
            }
            html += '>' + value['name'] + '</option>';
          }
        }
      });
    }

    $('select[name="order_status_id"]').html(html); 
  });
  
</script>

<script>
    $(document).ready(function(){
        // On Order Status Selection Change. This is Used when we select Shipped with tracking
        $('#input-order-status').change(function(){
        if ($('#input-order-status').val() == 14){
            // id for Shipped with tracking
            $('#shippingco').css("display", "block");
            $('#tracking').css("display", "block");

        }
        else{
            $('#shippingco').css("display", "none");
            $('#tracking').css("display", "none");
        }
        });
    });
</script>