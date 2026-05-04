<!-- used in requested.tpl-->
$(document).ready(function(){
  
  $('input[name="product_quantity_update"]').keyup(function() {
    var letter = $(this).val().match(/^([0-9])/);
    if( letter == null){
      $(this).val('');
      return false;
    }
    return true;
  });

  $('input[name="filter_order_no_requested"]').keyup(function(e) {
    if (/\D/g.test(this.value)) {
     var node = $(this);
     node.val(node.val().replace(/[^0-9]/g,'') );
    }
  });

  $('input[name="filter_order_amount_from"]').keyup(function(e) {
    if (/\D/g.test(this.value)) {
     var node = $(this);
     node.val(node.val().replace(/[^0-9]/g,'') );
    }
  });

  $('input[name="filter_order_amount_to"]').keyup(function(e) {
    if (/\D/g.test(this.value)) {
     var node = $(this);
     node.val(node.val().replace(/[^0-9]/g,'') );
    }
  });

   // invoice date date_picker
  $('input[name=\"datefilter_requested_from\"]').datepicker({
    format: "dd MM yyyy",
  });  
  $('input[name=\"datefilter_requested_to\"]').datepicker({
    format: "dd MM yyyy",
  });


  $('.range_requested').click(function(){
    $($(this).data('toggle')).slideToggle();  
  });

  // $('.profile_tebination a[href="' + location.hash + '"]').click();

  $('#button_filter_requested').click(function(){

    var url = "index.php?route=seller_panel/account-order/getPickpupOrderRequested";

    var filter_order_no_requested = $('input[name=\'filter_order_no_requested\']').val();

    if (filter_order_no_requested != '') {
        url += '&filter_order_no_requested=' + encodeURIComponent(filter_order_no_requested);
    }

    var filter_order_processing_date_from = $('input[name=\'datefilter_requested_from\']').val();

    if (filter_order_processing_date_from != '') {
     url += '&filter_order_processing_date_from=' + encodeURIComponent(filter_order_processing_date_from);
    }

    var filter_order_processing_date_to = $('input[name=\'datefilter_requested_to\']').val();

    if (filter_order_processing_date_to != '') {
     url += '&filter_order_processing_date_to=' + encodeURIComponent(filter_order_processing_date_to);
    }

    var filter_order_amount_from = $('input[name=\'filter_order_amount_from\']').val();
    if (filter_order_amount_from != '') {
        url += '&filter_order_amount_from=' + encodeURIComponent(filter_order_amount_from);
    }

    var filter_order_amount_to = $('input[name=\'filter_order_amount_to\']').val();
    if (filter_order_amount_to != '') {
        url += '&filter_order_amount_to=' + encodeURIComponent(filter_order_amount_to);
    }

    var date_from = new Date(filter_order_processing_date_from);
    var date_to = new Date(filter_order_processing_date_to);
    var date_diff = date_to.getTime() - date_from.getTime();
    if( date_diff < 0 ){
      alert('From date should be less then To date !!');
      return false;
    }

    if( (parseInt( filter_order_amount_from )) > (parseInt( filter_order_amount_to ))){
      alert('From amount should be less then To amount !!');
      return false;
    }

    var filter_record_range = $('select[name=\'filter_record_range\']').val();
    if (filter_record_range != '*') {
        url += '&filter_record_range=' + encodeURIComponent(filter_record_range);
    }

    var edit_type_status = $('select[name=\'edit_type_status\']').val();
    if (edit_type_status != '*') {
        url += '&edit_type_status=' + encodeURIComponent(edit_type_status);
    }

    location = url;
  });

  $('input').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button_filter_requested').trigger('click');
    }
  });

  $('.requested_sale_apply_btn').click(function(){
    var value_from = $('input[name="filter_order_amount_from"]').val();
    var value_to = $('input[name="filter_order_amount_to"]').val();
    var combine_value = value_from +'-'+ value_to;
    if( value_from !='' && value_to !=''){
      $('.price_range_total').val(combine_value);
      $('#total_price').slideUp();
    }else {
      alert('Please fill value of from and to !');
    }
  });

  applyChangesToRows();

  function getChanges(){
    let changes = {};
    if( changes = localStorage.getItem('changes')){
      changes = $.parseJSON(changes);
    }
    else{
      changes = {};
    }
    return changes;
  }

  function applyChangesToRows(){
    let changes = getChanges();
    if(changes){
      $('.product_table tbody tr').each(function(){
        let order_product_id = $(this).attr('data-order_product_id');
        if(changes[order_product_id]){
          if(changes[order_product_id]['quantity']){
            let quantity = changes[order_product_id]['quantity'];
            $(this).find('.product_quantity').val(quantity);    
            $(this).find('.quantity_label').text(quantity);
          }
          if(changes[order_id][order_product_id]['SELLER_APPROVED']){
             $(this).find('.popup_products').click();
          }
        }      
      });  
    }    
  }

  function setChanges(order_id, order_product_id,field,value){
    let changes = getChanges();

    if(!changes[order_id]){
      changes[order_id] = {};
    }
    changes[order_id][order_product_id] = {[field]: value}; 
    changes = JSON.stringify(changes);
    localStorage.setItem('changes',changes);
  }

  function edit_track(suborder_id){
    console.clear();
    var flag = 1;
// alert(suborder_id);
    $('.edit_track_'+suborder_id).each(function(){
// alert(suborder_id);
      var data_change = $(this).attr('data-change');
// alert(data_change);
      if (data_change == 'false') {
          flag = 0;
          return false;
      }
    });
    return flag;
  }

  var selected_total_pieces = 0;
  var selected_product_amount = 0;
  var total_selected_box = 0;
  var main_order_id = '';
  var model_suborder_id = '';
  var total_order_products = 0;
  var total_decline_products = 0;
  var total_later_dis_products = 0;

  $('.data_model_button').click(function(){
    main_order_id = $(this).data('order-id');
    model_suborder_id = $(this).data('suborder-id');
    selected_total_pieces = 0;
    selected_product_amount = 0;
    total_selected_box = 0;
    total_order_products = $(this).data('total-products');
    total_decline_products = 0;
    var total_later_dis_products = 0;
  });


  $('[data-toggle="popover"]').popover();

  function returnTwoDecimalValue(x){
    return Math.round(x * 100) / 100;
  }

  // Total Amount format 
  function numberWithCommas(x) {
    var amt_value = returnTwoDecimalValue(x);
    return amt_value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  // agree
  $('.popup_products').on('click',function(){ 
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).data('product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let total_piece = $(this).data('total-piece');
    let total_amount = $(this).data('total-amount');
    let product_amount = $(this).data('product-amount');
    let tax_amount = $(this).data('tax-amount');

    
    if($(this).prop("checked")){
      $('#checkbox_information_agree_'+order_product_id).hide();
      $('#checkbox_information_undo_'+order_product_id).show();
      $('#checkbox_information_edit_'+order_product_id).hide();
      $('#checkbox_information_decline_'+order_product_id).hide();
      $('#checkbox_information_dispatch_'+order_product_id).hide();
      $('#list_order_product_id_'+order_product_id).addClass('label-success');
      setChanges(order_id, order_product_id,'SELLER_APPROVED',{ 'value': 1, 'product_id': product_id });
      $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
      if( edit_track(main_suborder_id) ){
        $('#invoice_number_'+main_suborder_id).removeAttr('disabled');
        $('#invoice_date_'+main_suborder_id).removeAttr('disabled');
        $('#invc_dwnlod_'+main_suborder_id).removeAttr('disabled');  
      }
      
      $('#block_products_'+order_id+'_'+order_product_id).addClass('complete');
      // selected_total_products += 1;
      if(main_suborder_id == model_suborder_id){
        $('.prod_amt_'+order_product_id+'_'+product_id).text(product_amount);
        $('.tax_amt_'+order_product_id+'_'+product_id).text(tax_amount);
        $('.total_prod_amt_'+order_product_id+'_'+product_id).text(total_amount);

        let selected_products = ($('#total_products_'+main_suborder_id).text());
        $('#total_products_'+main_suborder_id).text( selected_products - 1 );
        selected_total_pieces += total_piece;
        selected_product_amount += total_amount;
        $('#table_total_box_'+main_suborder_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'">&#8377; '+ numberWithCommas( selected_product_amount ) +'</span>');
        total_selected_box += 1 ;
      }

      if( total_selected_box ){
        $('.inv_no_inv_date_form_'+main_suborder_id).removeClass('hidden');
        $('.invc_dwnlod_'+main_suborder_id).removeClass('hidden');
        $('.out_of_stock_'+main_suborder_id).addClass('hidden');
      }
    }
  });

  // decline

  $('.checkbox_information_decline').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let product_id = $(this).data('product-id');
    $(this).hide();
    $('#information_decline_'+order_product_id).prop('checked',true);
    $('#checkbox_information_agree_'+order_product_id).hide();
    $('#checkbox_information_undo_'+order_product_id).show();
    $('#checkbox_information_edit_'+order_product_id).hide();
    $('#checkbox_information_dispatch_'+order_product_id).hide();
    $('#list_order_product_id_'+order_product_id).addClass('label-danger');
    setChanges(order_id, order_product_id,'SELLER_NOT_SUPPLIED',{ 'value': 1, 'product_id': product_id });
    $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
    $('#block_products_'+order_id+'_'+order_product_id).addClass('cancel');
    if(main_suborder_id == model_suborder_id){
      let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
      $('#total_products_'+main_suborder_id).text( selected_products - 1 );      
    }
    if( edit_track(main_suborder_id) ){
      $('#invoice_number_'+main_suborder_id).removeAttr('disabled');
      $('#invoice_date_'+main_suborder_id).removeAttr('disabled');
      $('#invc_dwnlod_'+main_suborder_id).removeAttr('disabled');  
    }
    
    total_decline_products++;
    if(total_order_products == total_decline_products){
      var suborder_id = main_suborder_id; //$('.pending_order_'+order_id).data('suborder-id');
      var order_product_ids = [];
      var product_ids = [];
      $('.edit_order_'+suborder_id).each(function(){
        pro_id = $(this).data('product-id')
        product_ids.push(pro_id);
        ord_prod_id = $(this).data('order-product-id');
        order_product_ids.push(ord_prod_id);
      });

      if(confirm('If seller frequently mark SKU "Out of Stock" then Wholesalebox may block seller.')){
        $('#block_products_'+order_id+'_'+order_product_id).addClass('cancel');
          $.ajax({
            type:'POST',
            dataType:'json', 
            url:'index.php?route=seller_panel/account-order/setMarkOutOfStock',
            data:{'product_ids':product_ids, 'order_product_ids':order_product_ids, 'order_id':order_id, 'suborder_id':suborder_id},
            success:function(response){
              if( response['success'] ){
                window.location = window.location.href;
              }
            }
          });
      } else{
        $('#information_decline_'+order_product_id).prop('checked',false);
        $('#checkbox_information_agree_'+order_product_id).show();
        $('#checkbox_information_undo_'+order_product_id).hide();
        $('#checkbox_information_edit_'+order_product_id).show();
        $('#checkbox_information_dispatch_'+order_product_id).show();
        $(this).show();
        $('#block_products_'+order_id+'_'+order_product_id).removeClass('cancel');
        total_decline_products--;
        $('#total_products_'+main_suborder_id).text( total_order_products-total_decline_products );
      }
    }
  });

  // later dispatch
  $('.checkbox_information_dispatch').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).data('product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    $(this).hide();
    $('#information_dispatch_'+order_product_id).prop('checked',true);
    $('#checkbox_information_agree_'+order_product_id).hide();
    $('#checkbox_information_undo_'+order_product_id).show();
    $('#checkbox_information_edit_'+order_product_id).hide();
    $('#checkbox_information_decline_'+order_product_id).hide();
    $('#list_order_product_id_'+order_product_id).addClass('label-warning');
    setChanges(order_id, order_product_id,'SELLER_LATER_DISPATCH',{ 'value': 1, 'product_id': product_id });
    $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
    $('#block_products_'+order_id+'_'+order_product_id).addClass('processing');
    
    let selected_products = '';
    if(main_suborder_id == model_suborder_id){
      let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
      $('#total_products_'+main_suborder_id).text( selected_products - 1 );   
    }
    if( edit_track(main_suborder_id) ){
      $('#invoice_number_'+main_suborder_id).removeAttr('disabled');
      $('#invoice_date_'+main_suborder_id).removeAttr('disabled');
      $('#invc_dwnlod_'+main_suborder_id).removeAttr('disabled');  
    }
    
    total_later_dis_products++;
    if(total_order_products == total_later_dis_products){
      var suborder_id = main_suborder_id; //$('.pending_order_'+order_id).data('suborder-id');
      var order_product_ids = [];
      var product_ids = [];
      $('.edit_order_'+suborder_id).each(function(){
        pro_id = $(this).data('product-id')
        product_ids.push(pro_id);
        ord_prod_id = $(this).data('order-product-id');
        order_product_ids.push(ord_prod_id);
      });
      
      if(confirm('Do you really want to mark ALL Products as Later Dispatch?')){
        $('#block_products_'+order_id+'_'+order_product_id).addClass('processing');
          $.ajax({
            type:'POST',
            dataType:'json', 
            url:'index.php?route=seller_panel/account-order/setMarkOutOfStock',
            data:{'product_ids':product_ids, 'order_product_ids':order_product_ids, 'order_id':order_id, 'suborder_id':suborder_id,'mark_type':'seller_later_dispatch'},
            success:function(response){
              if( response['success'] ){
                window.location = window.location.href;
              }
            }
          });
      } else{
        $('#information_dispatch_'+order_product_id).prop('checked',false);
        $('#checkbox_information_agree_'+order_product_id).show();
        $('#checkbox_information_undo_'+order_product_id).hide();
        $('#checkbox_information_edit_'+order_product_id).show();
        $('#checkbox_information_decline_'+order_product_id).show();
        $(this).show();
        $('#block_products_'+order_id+'_'+order_product_id).removeClass('processing');
        total_later_dis_products--;
        $('#total_products_'+main_suborder_id).text( total_order_products-total_later_dis_products );
      }
    }
  });

  // undo
  $('.checkbox_information_undo').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let product_id = $(this).data('product-id');
    let total_piece = $(this).data('total-piece');
    let total_amount = $(this).data('total-amount');
    let old_product_amt = $(this).data('product-amount');
    let old_tax_amt = $(this).data('tax-amount');
    let old_total_amt = $(this).data('total-amount');
    $(this).hide();

    $('#checkbox_information_agree_'+order_product_id).show();
    $('#checkbox_information_edit_'+order_product_id).show();
    $('#checkbox_information_decline_'+order_product_id).show();
    $('#checkbox_information_dispatch_'+order_product_id).show();
    $('#list_order_product_id_'+order_product_id).removeClass('label-success');
    $('#list_order_product_id_'+order_product_id).removeClass('label-danger');
    $('#list_order_product_id_'+order_product_id).removeClass('label-warning');
    
    if($('#list_sub_order_product_id_'+order_product_id).is(':checked')){
      let checkedagreedcheckbox = $('.popup_products:checked').length;
      $('#list_sub_order_product_id_'+order_product_id).prop('checked',false);
      setChanges(order_id, order_product_id,'SELLER_APPROVED',0);
      $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#block_products_'+order_id+'_'+order_product_id).removeClass('complete');

      if( !edit_track(main_suborder_id) ){
        $('#invoice_number_'+main_suborder_id).attr('disabled',true);
        $('#invoice_date_'+main_suborder_id).attr('disabled',true);
        $('#invc_dwnlod_'+main_suborder_id).attr('disabled',true);
      }

      if( main_suborder_id == model_suborder_id ){
        let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
        selected_products += 1;
        $('#total_products_'+main_suborder_id).text( selected_products );
        selected_total_pieces -= total_piece;
        selected_product_amount -= total_amount;
        if(parseInt(selected_product_amount)<=0){
          selected_product_amount=0;
        }
        $('#table_total_box_'+main_suborder_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'">&#8377; '+ numberWithCommas( selected_product_amount ) +'</span>');
      }
      total_selected_box -= 1;
      if( !total_selected_box ){
        $('.inv_no_inv_date_form_'+main_suborder_id).addClass('hidden');
        $('.invc_dwnlod_'+main_suborder_id).addClass('hidden');
        $('.out_of_stock_'+main_suborder_id).removeClass('hidden');        
      }
    }

    if($('#information_decline_'+order_product_id).is(':checked')){
      $('#information_decline_'+order_product_id).prop('checked',false);
      setChanges(order_id, order_product_id,'SELLER_NOT_SUPPLIED',0);
      $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#block_products_'+order_id+'_'+order_product_id).removeClass('cancel');
      if( main_suborder_id == model_suborder_id ){
        let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
        selected_products += 1;
        $('#total_products_'+main_suborder_id).text( selected_products );
      }
      if( !edit_track(main_suborder_id) ){
        $('#invoice_number_'+main_suborder_id).attr('disabled',true);
        $('#invoice_date_'+main_suborder_id).attr('disabled',true);
        $('#invc_dwnlod_'+main_suborder_id).attr('disabled',true);
      }

      total_decline_products--;
    }

    if($('#information_dispatch_'+order_product_id).is(':checked')){
      $('#information_dispatch_'+order_product_id).prop('checked',false);
      setChanges(order_id, order_product_id,'SELLER_LATER_DISPATCH',0);
      $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#block_products_'+order_id+'_'+order_product_id).removeClass('processing');
      if( main_suborder_id == model_suborder_id ){
        let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
            selected_products += 1;
        $('#total_products_'+main_suborder_id).text( selected_products );
      }
      if( !edit_track(main_suborder_id) ){
        $('#invoice_number_'+main_suborder_id).attr('disabled',true);
        $('#invoice_date_'+main_suborder_id).attr('disabled',true);
        $('#invc_dwnlod_'+main_suborder_id).attr('disabled',true);
      }
      
      total_later_dis_products--;
    }

    if($('#information_edit_'+order_product_id).is(':checked')){
      let old_value = $('#input_quantity_'+order_product_id+'_'+product_id).data('old-value');
      let new_value = $('#input_quantity_'+order_product_id+'_'+product_id).val();

      $('.prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(old_product_amt));
      $('.tax_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(old_tax_amt));
      $('.total_prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(old_total_amt));

      $('#product_quantity_'+order_product_id+'_'+product_id ).text(old_value);
      $('#information_edit_'+order_product_id).prop('checked',false);
      setChanges(order_id, order_product_id,'SELLER_PARTIAL',0);
      $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#block_products_'+order_id+'_'+order_product_id).removeClass('edit');
      if( !edit_track(main_suborder_id) ){
        $('#invoice_number_'+main_suborder_id).attr('disabled',true);
        $('#invoice_date_'+main_suborder_id).attr('disabled',true);
        $('#invc_dwnlod_'+main_suborder_id).attr('disabled',true);
      }
      if( main_suborder_id == model_suborder_id ){
        let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
            selected_products += 1;
        $('#total_products_'+main_suborder_id).text( selected_products );
        selected_total_pieces -= new_value;
        selected_product_amount -= (total_amount*new_value / old_value);
        if(parseInt(selected_product_amount)<=0){
          selected_product_amount = 0;
        }
        $('#table_total_box_'+main_suborder_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'">&#8377; '+ numberWithCommas( selected_product_amount ) +'</span>');
      }
      total_selected_box -= 1;
      if( !total_selected_box ){
        $('.inv_no_inv_date_form_'+main_suborder_id).addClass('hidden');
        $('.invc_dwnlod_'+main_suborder_id).addClass('hidden');
        $('.out_of_stock_'+main_suborder_id).removeClass('hidden');  
      }      
    }

  });

  // edit
  $('.checkbox_information_edit').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).data('product-id');
    $('#product_quantity_'+order_product_id+'_'+product_id).hide();
    $('#prod_quantity_'+order_product_id+'_'+ product_id).show();
    $('input#input_quantity_'+order_product_id+'_'+product_id).focus();

    $(this).hide();
    $('#checkbox_information_agree_'+order_product_id).hide();
    $('#checkbox_information_decline_'+order_product_id).hide();
    $('#checkbox_information_dispatch_'+order_product_id).hide();
    $('#checkbox_information_undo_'+order_product_id).hide();
  });

  $('.quantity_close_input').click(function(){
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).attr('data-product-id');
    let old_value_close = $('#input_quantity_'+order_product_id+'_'+product_id).data('old-value');
    $('#product_quantity_'+order_product_id+'_'+product_id).show();
    $('#prod_quantity_'+order_product_id+'_'+ product_id).hide();
    $('#checkbox_information_agree_'+order_product_id).show();
    $('#checkbox_information_edit_'+order_product_id).show();
    $('#checkbox_information_decline_'+order_product_id).show();
    $('#checkbox_information_dispatch_'+order_product_id).show();
    $('#input_quantity_'+order_product_id+'_'+product_id).val(old_value_close);
  });

  $('.quantity_save_input').click(function(){
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let order_product_id = $(this).data('order-product-id');
    let product_id= $(this).attr('data-product-id');
    let old_value = $('#input_quantity_'+order_product_id+'_'+product_id).data('old-value');
    let new_value = $('#input_quantity_'+order_product_id+'_'+product_id).val();
    let price_per_piece = $('.price_per_piece_'+order_product_id+'_'+product_id).text();
    let tax_rate  = $('.tax_rate_'+order_product_id+'_'+product_id).data('tax-rate');
    let new_td_prod_amt = (new_value * price_per_piece);
    let new_td_tax_amt = ((new_td_prod_amt * tax_rate)/100);

    let calculate_amount = ( new_td_prod_amt + new_td_tax_amt );

    //product_quantity_update
    if( new_value > old_value ){
      alert('Max quantity is ' +old_value);
      return false;
    }

    if( new_value <= 0 ){
      alert('Please enter minimum quantity is 1 !');
      $('#input_quantity_'+order_product_id+'_'+product_id).val('1')
      return false;
    }
    
    $('.prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(new_td_prod_amt));
    $('.tax_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(new_td_tax_amt));
    $('.total_prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(calculate_amount));
    $(this).attr('data-total-amount',calculate_amount);

    $('#block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
    $('#product_quantity_'+order_product_id+'_'+product_id).show();
    $('#product_quantity_'+order_product_id+'_'+product_id ).text(new_value);
    $('#prod_quantity_'+order_product_id+'_'+product_id).hide();
    $('#checkbox_information_undo_'+order_product_id).show();
    $('#information_edit_'+order_product_id).prop('checked',true);
    setChanges(order_id, order_product_id,'SELLER_PARTIAL',{ 'value': new_value, 'product_id': product_id });
    $('#block_products_'+order_id+'_'+order_product_id).addClass('edit');
    if( edit_track(main_suborder_id) ){
      $('#invoice_number_'+main_suborder_id).removeAttr('disabled');
      $('#invoice_date_'+main_suborder_id).removeAttr('disabled');
      $('#invc_dwnlod_'+main_suborder_id).removeAttr('disabled');  
    }
    if(main_suborder_id == model_suborder_id){
      let selected_products = parseInt($('#total_products_'+main_suborder_id).text());
      $('#total_products_'+main_suborder_id).text( selected_products - 1 );
      selected_total_pieces += parseInt(new_value) ;
      selected_product_amount += (calculate_amount);
      $('#table_total_box_'+main_suborder_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'">&#8377; '+ numberWithCommas( selected_product_amount ) +'</span>');
      total_selected_box += 1;
    }
    $('.inv_no_inv_date_form_'+main_suborder_id).removeClass('hidden');
    $('.invc_dwnlod_'+main_suborder_id).removeClass('hidden');
    $('.out_of_stock_'+main_suborder_id).addClass('hidden');
  });

  // it's used for view & download invoice button

  $('.submit_invoice_button').on('click',function(){
    $(this).attr('disabled',true);
    let order_id = $(this).data('order-id');
    let order_no = $(this).data('order-no');
    let suborder_id = $(this).data('suborder-id'); //$('.pending_order_'+order_id).data('suborder-id');
    let fieldset_count = $('.edit_order_'+suborder_id).length;
    let checkbox_selected = $('.edit_order_'+suborder_id+' input[type="checkbox"]:checked').length;
    let invoice_number = $('#invoice_number_'+suborder_id).val();
    let invoice_date = $('#invoice_date_'+suborder_id).val();
    let seller_id = $('input[name=hidden_seller_id').val();

    if(fieldset_count == checkbox_selected ){
      let change = getChanges();
      $.ajax({
        type:'POST',
        dataType:'json', 
        url:'index.php?route=seller_panel/account-order/splitOrderProducts',
        data:{'invoice_number':invoice_number, 'invoice_date':invoice_date, 'changes':change[order_id]},
        beforeSend: function() {
          $('.submit_invoice_button').button('loading');
          $('.invoice_popup_little_btn_cancel').hide();
          $('.close_btn_invoice_popup').removeClass('fa fa-times');
          $('.model_popup_close').hide();
        },
        complete: function() {
          $('.submit_invoice_button').button('reset');
        },
        success:function(response){
          $('.invoice_no_error').text('');
          if(response.error == 2 ){
            alert(response.error_msg);
            location = response.redirect_link; 
          } if(response.error == 3 ){
            alert(response.error_msg);
            $('#invoice_popup_'+suborder_id).addClass('hidden');
            $('#invoice_number_'+suborder_id).removeAttr('readonly',true);
            $('#invoice_date_'+suborder_id).removeAttr('disabled',true);
          } else if( response.error == 1 ){
            alert(response.error_msg);
            $('.invoice_popup_little_btn_cancel').show();
            $('.close_btn_invoice_popup').addClass('fa fa-times');
          } else if( response.error == 4 ){
            alert(response.error_msg);
            localStorage.removeItem('changes');
            window.location.reload();
          } else {
            $('#invoice_popup_'+suborder_id).addClass('hidden');
            $('#h3_'+order_no).hide();
            $('#inv_no_inv_date_form_'+suborder_id).hide();
            // $('#invoice_date_box_'+order_no).hide();
            $('#invc_dwnlod_'+suborder_id).replaceWith('<a href="'+response['link']+'" class="btn btn-success btn-view-invoice"> Download Invoice</a>');
            delete change[order_id];
            localStorage.setItem("changes",JSON.stringify(change));    
            $('.model_popup_close').show();
          }
          
        }
      });
    } else{
      alert('Please checks all products !');
    }
  });

  // it's working for invoice view & download popup
  $('.invoice_icon_btn').click(function(){
    $('.checkbox_information_undo').hide();

    let order_id = $(this).data('order-id');
    let order_no = $(this).data('order-no');
    let suborder_id = $(this).data('suborder-id');
    let inv_no = $('#invoice_number_'+suborder_id).val();
    let inv_date = $('#invoice_date_'+suborder_id).val();

    if( inv_no == '' || inv_date ==''){
      alert('Please enter your invoice number and invoice date!!');
      return false;
    }

    if(inv_no.match(/^[A-Za-z0-9/-]{1,16}$/)==null){
      alert('Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/)');
      return false;
    }

    $('.min_popup_info_'+suborder_id).text('');    
    let html = "<h4 class='invoice_popup_little_title'>Please verify below detail</h4>";
        html +="<p>Invoice Number: <b>"+inv_no+"</b></p>";
        html +="<p>Invoice Date: <b>"+inv_date+"</b></p>";
        html +="<p>Number of Pieces: <b>"+selected_total_pieces+"</b></p>";
        html +="<p>Total Invoice Value: <b> &#8377; "+numberWithCommas( selected_product_amount )+"</b></p>";
    $('.min_popup_info_'+suborder_id).append(html);  
    $('#invoice_popup_'+suborder_id).removeClass('hidden');
    $('#invoice_number_'+suborder_id).attr('readonly',true);
    $('#invoice_date_'+suborder_id).attr('disabled',true);
    $('.model_popup_close').hide();
  });

  $('.close_btn_invoice_popup, .invoice_popup_little_btn_cancel').click(function(){
    let order_no = $(this).data('order-no');
    let order_id = $(this).data('order-id');
    let suborder_id = $(this).data('suborder-id');
    $('#invoice_popup_'+suborder_id).addClass('hidden');
    $('.checkbox_information_undo').show();
    $('#invoice_number_'+suborder_id).attr('readonly',false);
    $('#invoice_date_'+suborder_id).attr('disabled',false);
    $('.model_popup_close').show();
  });

  $('.modal').on('hidden.bs.modal', function () {
    window.location = window.location.href;
  });

  $('.exclamation').tooltip();  
  var revert_order_product_ids = [];
  $('.revert_checkbox').click(function(){
    if($(this).prop('checked')==true){
      revert_order_product_ids.push($(this).data('order-product-id'));  
    } else if($(this).prop('checked')==false){
      revert_order_product_ids = revert_order_product_ids.filter(item => item !== $(this).data('order-product-id'));
    }      
  });
  
  $('.revert_button').click(function(){
    var seller_id = $("input[name=hidden_seller_id]").val();
    var revert_order_id = $(this).data('order-id');
    var revert_suborder_id = $(this).data('suborder-id');
    
    if(revert_order_product_ids != '' ){
      if(confirm('Are you sure want to move all checked SKU to the pending list ? ')){
        $.ajax({
          type: 'post',
          url : 'index.php?route=seller_panel/account-order/revertGoodsBySeller',
          data:{'revert_order_product_ids': revert_order_product_ids, 'seller_id':seller_id, 'order_id':revert_order_id, 'suborder_id':revert_suborder_id,},
          beforeSend: function() {
            $('.revert_button').button('loading');
          },
          complete: function() {
            $('.revert_button').button('reset');
          },
          success:function(response){
            if(response.status == 1){
              alert(response.message);
            }
            window.location.reload();
          }
        });
      }   
    } else {
      alert('Please choose any product for revert!');
    }
  });

  ////////////////////////////////////////////////
  //////////// After Invoiced Generated /////////
  //////////////////////////////////////////////

  var invoiced_selected_total_pieces = 0;
  var invoiced_selected_product_amount = 0;
  var invoiced_total_selected_box = 0;
  var invoiced_total_decline_products = 0;
  var invoiced_total_later_dis_products = 0;
  // var old_product_datas = '';

  // invoiced set Changes
  $('.edit_invoice_order').click(function(){
    let sllr_inv_id = $(this).data('seller-invoice-id');
    $('#edit_inv_save_'+sllr_inv_id).addClass('edit_invoice_tab');
    $('.edit_invoice_order_block').show();
    $('.edt_inv_order_actions').show();
    $('.edit_invc_and_dwnlod_invce_blck #edit_invoice_'+sllr_inv_id).hide();
    $('.edit_invc_and_dwnlod_invce_blck .btn-view-invoice').hide();

    $('#edit_inv_save_'+sllr_inv_id).show();
    $('#edit_inv_cancel_'+sllr_inv_id).show();

    $('.invoiced_update_block_'+sllr_inv_id).removeClass('hidden');
    $('.invoiced_update_block_'+sllr_inv_id+ ' input , '+ '.invoiced_update_block_'+sllr_inv_id+ ' select').removeAttr('disabled');
  });

  $('.edit_inv_cancel').click(function(){
    // localStorage.clear();
    localStorage.removeItem('seller_invoiced_changes');
    var suborder_id = $(this).data('suborder-id');
    let sllr_inv_id = $(this).data('sllr-inv-id');

    let count_tbody_row = $('.invoiced_product_count_'+sllr_inv_id).length;
    for(i=1; i<=count_tbody_row; i++){
      $('.inv_pro_piece_'+sllr_inv_id+'_'+i).html($('.inv_pro_piece_'+sllr_inv_id+'_'+i).data('original-piece'));
      $('.inv_pro_amt_'+sllr_inv_id+'_'+i).html($('.inv_pro_amt_'+sllr_inv_id+'_'+i).data('original-amt'));
      $('.inv_pro_tax_amt_'+sllr_inv_id+'_'+i).html($('.inv_pro_tax_amt_'+sllr_inv_id+'_'+i).data('original-tax-amt'));
      $('.inv_pro_total_amt_'+sllr_inv_id+'_'+i).html($('.inv_pro_total_amt_'+sllr_inv_id+'_'+i).data('original-total-amt'));
    }
    
    $('#table_total_box_'+suborder_id+'_'+sllr_inv_id).html($('#table_total_box_'+suborder_id+'_'+sllr_inv_id).data('original-total-amount'));
    invoiced_selected_product_amount = 0;
    // $('.edit_invc_and_dwnlod_invce_blck').show();
    $('.edit_invc_and_dwnlod_invce_blck #edit_invoice_'+sllr_inv_id).show();
    $('.edit_invc_and_dwnlod_invce_blck .btn-view-invoice').show();
    $('.edit_invoice_order_block').hide();
    $('.edt_inv_order_actions').hide();

    $('.invoiced_edit_order_'+suborder_id).removeClass('complete');
    $('.invoiced_edit_order_'+suborder_id).removeClass('edit');
    $('.invoiced_edit_order_'+suborder_id).removeClass('cancel');
    $('.invoiced_edit_order_'+suborder_id).removeClass('processing');
    $('.invoiced_information_agree input').prop('checked',false);
    $('.invoiced_information_edit input').prop('checked',false);
    $('.invoiced_information_decline input').prop('checked',false);
    $('.invoiced_information_dispatch input').prop('checked',false);

    $('.invoiced_information_undo').hide();
    $('.invoiced_information_agree').show();
    $('.invoiced_information_edit').show();
    $('.invoiced_information_decline').show();
    $('.invoiced_information_dispatch').show();

    $('.edit_invoice_save').removeClass('edit_invoice_tab');

    $('.invoiced_update_block_'+sllr_inv_id+ ' input , '+ '.invoiced_update_block_'+sllr_inv_id+ ' select').attr('disabled',true);
    $('.invoiced_update_block_'+sllr_inv_id).addClass('hidden');

  });

  applyInvoiceChangesToRows();

  function invoiceGetChanges(){
    let changes = {};
    if( changes = localStorage.getItem('seller_invoiced_changes')){
      changes = $.parseJSON(changes);
    }
    else{
      changes = {};
    }
    return changes;
  }

  function applyInvoiceChangesToRows(){
    let changes = invoiceGetChanges();
    if(changes){
      $('.product_table tbody tr').each(function(){
        let order_product_id = $(this).attr('data-order_product_id');
        if(changes[order_product_id]){
          if(changes[order_product_id]['quantity']){
            let quantity = changes[order_product_id]['quantity'];
            $(this).find('.product_quantity').val(quantity);    
            $(this).find('.quantity_label').text(quantity);
          }
          if(changes[order_id][order_product_id]['SELLER_APPROVED']){
             $(this).find('.popup_products').click();
          }
        }      
      });  
    }    
  }

  function invoicedSetChanges(order_id, order_product_id,field,value){
    let changes = invoiceGetChanges();

    if(!changes[order_id]){
      changes[order_id] = {};
    }
    changes[order_id][order_product_id] = {[field]: value}; 
    changes = JSON.stringify(changes);
    localStorage.setItem('seller_invoiced_changes',changes);
  }


  // agree
  $('.invoiced_input_popup_products').on('click',function(){ 
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).data('product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let total_piece = $(this).data('total-piece');
    let total_amount = $(this).data('total-amount');
    let product_amount = $(this).data('product-amount');
    let tax_amount = $(this).data('tax-amount');
    let seller_invoice_id = $(this).data('seller-invoice-id');
    let original_all_total_amt = $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).data('original-total-amount');

    if($(this).prop("checked")){
      $('#invoiced_information_agree_'+order_product_id).hide();
      $('#invoiced_information_undo_'+order_product_id).show();
      $('#invoiced_information_edit_'+order_product_id).hide();
      $('#invoiced_information_decline_'+order_product_id).hide();
      $('#invoiced_information_dispatch_'+order_product_id).hide();
      invoicedSetChanges(order_id, order_product_id,'SELLER_APPROVED',{ 'value': 1, 'product_id': product_id,'seller_invoice_id':seller_invoice_id });
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
      
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).addClass('complete');
      if(main_suborder_id == model_suborder_id){
        $('.prod_amt_'+order_product_id+'_'+product_id).text(product_amount);
        $('.tax_amt_'+order_product_id+'_'+product_id).text(tax_amount);
        $('.total_prod_amt_'+order_product_id+'_'+product_id).text(total_amount);

        invoiced_selected_total_pieces += total_piece;
        invoiced_selected_product_amount += total_amount;
        $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'_'+seller_invoice_id+'" data-original-total-amount="'+original_all_total_amt+'">&#8377; '+ numberWithCommas( invoiced_selected_product_amount ) +'</span>');
      }
    }
  });

  // decline

  $('.invoiced_information_decline').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let product_id = $(this).data('product-id');
    let seller_invoice_id = $(this).data('seller-invoice-id');
    $(this).hide();
    $('#invoiced_input_information_decline_'+order_product_id).prop('checked',true);
    $('#invoiced_information_agree_'+order_product_id).hide();
    $('#invoiced_information_undo_'+order_product_id).show();
    $('#invoiced_information_edit_'+order_product_id).hide();
    $('#invoiced_information_dispatch_'+order_product_id).hide();
    invoicedSetChanges(order_id, order_product_id,'SELLER_NOT_SUPPLIED',{ 'value': 1, 'product_id': product_id,'seller_invoice_id': seller_invoice_id,  });
    $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
    $('#invoiced_block_products_'+order_id+'_'+order_product_id).addClass('cancel');
  });

  // later dispatch
  $('.invoiced_information_dispatch').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).data('product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let seller_invoice_id = $(this).data('seller-invoice-id');
    $(this).hide();
    $('#invoiced_input_information_dispatch_'+order_product_id).prop('checked',true);
    $('#invoiced_information_agree_'+order_product_id).hide();
    $('#invoiced_information_undo_'+order_product_id).show();
    $('#invoiced_information_edit_'+order_product_id).hide();
    $('#invoiced_information_decline_'+order_product_id).hide();
    invoicedSetChanges(order_id, order_product_id,'SELLER_LATER_DISPATCH',{ 'value': 1, 'product_id': product_id, 'seller_invoice_id':seller_invoice_id });
    $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
    $('#invoiced_block_products_'+order_id+'_'+order_product_id).addClass('processing');
  });

  // undo
  $('.invoiced_information_undo').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let product_id = $(this).data('product-id');
    let total_piece = $(this).data('total-piece');
    let total_amount = $(this).data('total-amount');
    let old_product_amt = $(this).data('product-amount');
    let old_tax_amt = $(this).data('tax-amount');
    let old_total_amt = $(this).data('total-amount');
    let seller_invoice_id = $(this).data('seller-invoice-id');
    let original_all_total_amt = $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).data('original-total-amount');
    $(this).hide();

    $('#invoiced_information_agree_'+order_product_id).show();
    $('#invoiced_information_edit_'+order_product_id).show();
    $('#invoiced_information_decline_'+order_product_id).show();
    $('#invoiced_information_dispatch_'+order_product_id).show();
    
    if($('#invoiced_list_sub_order_product_id_'+order_product_id).is(':checked')){
      let checkedagreedcheckbox = $('.popup_products:checked').length;
      $('#invoiced_list_sub_order_product_id_'+order_product_id).prop('checked',false);
      invoicedSetChanges(order_id, order_product_id,'SELLER_APPROVED',0);
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).removeClass('complete');

      
      if( main_suborder_id == model_suborder_id ){
        invoiced_selected_total_pieces -= total_piece;
        invoiced_selected_product_amount -= total_amount;

        if(parseInt(invoiced_selected_product_amount)<=0){
          invoiced_selected_product_amount = 0;
          $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'_'+seller_invoice_id+'" data-original-total-amount="'+original_all_total_amt+'">'+ original_all_total_amt +'</span>');
        } else {
          $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'_'+seller_invoice_id+'" data-original-total-amount="'+original_all_total_amt+'">&#8377; '+ numberWithCommas( invoiced_selected_product_amount ) +'</span>');
        }
      }
    }

    if($('#invoiced_input_information_decline_'+order_product_id).is(':checked')){
      $('#invoiced_input_information_decline_'+order_product_id).prop('checked',false);
      invoicedSetChanges(order_id, order_product_id,'SELLER_NOT_SUPPLIED',0);
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).removeClass('cancel');
    }

    if($('#invoiced_input_information_dispatch_'+order_product_id).is(':checked')){
      $('#invoiced_input_information_dispatch_'+order_product_id).prop('checked',false);
      invoicedSetChanges(order_id, order_product_id,'SELLER_LATER_DISPATCH',0);
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).removeClass('processing');
    }

    if($('#invoiced_input_information_edit_'+order_product_id).is(':checked')){
      let old_value = $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).data('old-value');
      let new_value = $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).val();

      $('.invoiced_prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(old_product_amt));
      $('.invoiced_tax_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(old_tax_amt));
      $('.invoiced_total_prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(old_total_amt));

      $('#invoiced_product_quantity_'+order_product_id+'_'+product_id ).text(old_value);
      $('#invoiced_input_information_edit_'+order_product_id).prop('checked',false);
      invoicedSetChanges(order_id, order_product_id,'SELLER_PARTIAL',0);
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','false');
      $('#invoiced_block_products_'+order_id+'_'+order_product_id).removeClass('edit');
      if( main_suborder_id == model_suborder_id ){
        invoiced_selected_total_pieces -= new_value;
        invoiced_selected_product_amount -= (total_amount*new_value / old_value);

        if(parseInt(invoiced_selected_product_amount)<=0){
          invoiced_selected_product_amount=0
          $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'_'+seller_invoice_id+'" data-original-total-amount="'+original_all_total_amt+'">'+ original_all_total_amt +'</span>');
        } else {
          $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'_'+seller_invoice_id+'" data-original-total-amount="'+original_all_total_amt+'">&#8377; '+ numberWithCommas( invoiced_selected_product_amount ) +'</span>');          
        }

      }
    }
  });


   // edit
  $('.invoiced_information_edit').on('click',function(){
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).data('product-id');
    let input_old_value = $('input#invoiced_input_quantity_'+order_product_id+'_'+product_id).data('old-value');
    $('#invoiced_product_quantity_'+order_product_id+'_'+product_id).hide();
    $('#invoiced_prod_quantity_'+order_product_id+'_'+ product_id).show();
    $('input#invoiced_input_quantity_'+order_product_id+'_'+product_id).val(input_old_value);
    $('input#invoiced_input_quantity_'+order_product_id+'_'+product_id).focus();

    $(this).hide();
    $('#invoiced_information_agree_'+order_product_id).hide();
    $('#invoiced_information_decline_'+order_product_id).hide();
    $('#invoiced_information_dispatch_'+order_product_id).hide();
    $('#invoiced_information_undo_'+order_product_id).hide();

    $('.edit_invoice_order_block').hide();
  });

  $('.invoiced_quantity_close_input').click(function(){
    let order_product_id = $(this).data('order-product-id');
    let product_id = $(this).attr('data-product-id');
    let old_value_close = $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).data('old-value');
    $('#invoiced_product_quantity_'+order_product_id+'_'+product_id).show();
    $('#invoiced_prod_quantity_'+order_product_id+'_'+ product_id).hide();
    $('#invoiced_information_agree_'+order_product_id).show();
    $('#invoiced_information_edit_'+order_product_id).show();
    $('#invoiced_information_decline_'+order_product_id).show();
    $('#invoiced_information_dispatch_'+order_product_id).show();
    $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).val(old_value_close);
    $('.edit_invoice_order_block').show();
  });

  $('.invoiced_quantity_save_input').click(function(){
    let order_id = $(this).data('order-id');
    let main_suborder_id = $(this).data('suborder-id');
    let order_product_id = $(this).data('order-product-id');
    let product_id= $(this).attr('data-product-id');
    let old_value = $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).data('old-value');
    let new_value = $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).val();
    let price_per_piece = $('.invoiced_price_per_piece_'+order_product_id+'_'+product_id).text();
    let tax_rate  = $('.invoiced_tax_rate_'+order_product_id+'_'+product_id).data('tax-rate');
    let new_td_prod_amt = (new_value * price_per_piece);
    let new_td_tax_amt = ((new_td_prod_amt * tax_rate)/100);
    let seller_invoice_id = $(this).data('seller-invoice-id');
    let original_all_total_amt = $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).data('original-total-amount');

    let calculate_amount = ( new_td_prod_amt + new_td_tax_amt );

    //product_quantity_update
    if( new_value > old_value ){
      alert('Max quantity is ' +old_value);
      return false;
    }

    if( new_value <= 0 ){
      alert('Please enter minimum quantity is 1 !');
      $('#invoiced_input_quantity_'+order_product_id+'_'+product_id).val('1')
      return false;
    }
    
    $('.invoiced_prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(new_td_prod_amt));
    $('.invoiced_tax_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(new_td_tax_amt));
    $('.invoiced_total_prod_amt_'+order_product_id+'_'+product_id).text(returnTwoDecimalValue(calculate_amount));
    $(this).attr('data-total-amount',calculate_amount);

    $('#invoiced_block_products_'+order_id+'_'+order_product_id).attr('data-change','true');
    $('#invoiced_product_quantity_'+order_product_id+'_'+product_id).show();
    $('#invoiced_product_quantity_'+order_product_id+'_'+product_id ).text(new_value);
    $('#invoiced_prod_quantity_'+order_product_id+'_'+product_id).hide();
    $('#invoiced_information_undo_'+order_product_id).show();
    $('#invoiced_input_information_edit_'+order_product_id).prop('checked',true);
    invoicedSetChanges(order_id, order_product_id,'SELLER_PARTIAL',{ 'value': new_value, 'product_id': product_id, 'seller_invoice_id':seller_invoice_id });
    $('#invoiced_block_products_'+order_id+'_'+order_product_id).addClass('edit');
    $('.edit_invoice_order_block').show();
      invoiced_selected_total_pieces += parseInt(new_value) ;
      invoiced_selected_product_amount += (calculate_amount);
      $('#table_total_box_'+main_suborder_id+'_'+seller_invoice_id).replaceWith('<span id="table_total_box_'+main_suborder_id+'_'+seller_invoice_id+'" data-original-total-amount="'+original_all_total_amt+'">&#8377; '+ numberWithCommas( invoiced_selected_product_amount ) +'</span>');
  });


  $('.edit_invoice_save').click(function(){
    let seller_invoiced_changes = invoiceGetChanges();
    let order_id = $(this).data('order-id');
    let suborder_id = $(this).data('suborder-id');
    let seller_id = $(this).data('seller-id');
    let seller_inv_id = $(this).data('seller-invoice-id');
    let invoice_number = $('.invoiced_input_'+seller_inv_id).val();
    let invoice_date = $('.filters_input_section_'+seller_inv_id).val();

    if( invoice_number == '' || invoice_date ==''){
      alert('Please enter your invoice number and invoice date!!');
      return false;
    }

    if(invoice_number.match(/^[A-Za-z0-9/-]{1,16}$/)==null){
      alert('Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/)');
      return false;
    }
    
    if(Object.getOwnPropertyNames(seller_invoiced_changes).length > 0 ){
      $.ajax({
        type:'POST',
        dataType:'json', 
        url:'index.php?route=seller_panel/account-order/sellerEditedAfterGeneratingInvoice',
        data:{'seller_id':seller_id, 
              'order_id':order_id, 
              'suborder_id':suborder_id,
              'seller_invoiced_changes':seller_invoiced_changes[order_id],
              'invoice_no':invoice_number,
              'invoice_date':invoice_date,
              'seller_invoice_id':seller_inv_id
            },
        beforeSend: function() {
          $('.edit_invoice_save').button('loading');
          $('.edit_inv_cancel').hide();
          $('.model_popup_close').hide();
        },
        complete: function() {
          $('.edit_invoice_save').button('reset');
        },
        success:function(response){
          $('.invoice_no_error').text('');
          if(response.error == 1 ){
            alert(response.error_msg);
            return false;
          } else if(response.error == 2 ){
            alert(response.error_msg);
            location = response.redirect_link; 
          } else if(response.error == 3 ){
            alert(response.error_msg);
            return false;
          } else if(response.error == 4 ){
            alert(response.error_msg);
            return false;
          } else {
            $('.edit_invc_and_dwnlod_invce_blck .btn-view-invoice').show();      
            $('.invoiced_information_undo').hide();
            $('.invoiced_input_'+seller_inv_id).attr('disabled',true);
            $('.filters_input_section_'+seller_inv_id).attr('disabled',true);
            $('.edit_invoice_save').hide();
            localStorage.removeItem('seller_invoiced_changes');
            $('.model_popup_close').show();
            $('.edt_inv_order_actions').hide();
          }        
        }
      });   
    }else{
      alert('Please select any actions!');
    }
    
  });


  // tab Change
  $('.seller_invoiced_tab').click(function(){
    var seller_inv_id = $(this).data('seller-invoice-id');
    var suborder_id = $(this).data('suborder-id');
    let sllr_inv_id = $(this).data('seller-invoice-id');
    
    if($('.edit_invoice_tab').length > 0 ){
      if(confirm("This Tab contains changes.. Are you sure to go on new tab")){
        $('.edit_inv_cancel').trigger('click');
        $('.invoiced_quantity_close_input').trigger('click');      
        $('.edit_invoice_order_block').hide();  
      } else{
        return false;  
      }      
    }
  });
  $('.seller_not_given_tab , .pending_order_tab').click(function(){
    $('.edit_invoice_save').removeClass('edit_invoice_tab');
    $('.edit_inv_cancel').trigger('click');
    $('.invoiced_quantity_close_input').trigger('click');      
    $('.edit_invoice_order_block').hide();  
  });

  ///////////////////////////////////////////////
  //////////// END /////////////////////////////
  /////////////////////////////////////////////
});