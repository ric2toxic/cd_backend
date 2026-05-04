var token = $('#hidden_token').val();

$(document).ready(function(){

  $(document).on('click','.overlay-btn',function(e){
      $(this).attr('disabled','disabled');
      $('.overlay-screen').show();
  })

  //$('#last_tenative_refund_amount').text($('#tenative_refund_amount').text());
  $('.products').each(function(){
    if( $(this).find('.return_action').val() > 1 ){
      $(this).find('.return_reason, .product_quantity, .shipping_methods, .comment').prop('disabled',true);
    }
  });
  $(location.hash).click();
  $('#msg-class').css('display','none');
});

$('#courier_partner').change(function(){
  var courier = $(this).val();
  if($.trim(courier) !== '') {
    $('.reverse_shippment_box').css('display','');
    $('.api-service-type').css('display','');
    $("#pincode_servisability_type").css('display','none');
    if(courier.toLowerCase() == 'dotzot') {
      $("#pincode_servisability_type").css('display','');
    }else if(courier.toLowerCase() == 'bluedart'){
      $('input[name=\'service_type\'][value="Economy"]').prop('checked',true);
      $("#pincode_servisability_type").css('display','');
    }else{ // shadowfax case
      $('.api-service-type').css('display','none');
    }
  }else{
    $('.reverse_shippment_box').css('display','none');
  }
});

$('#courier_partner_forward').change(function(){ 
  var courier = $(this).val(); 
  if($.trim(courier) !== '') {
    $('.forward_shippment_box').css('display','');
    $('.api-forward-service-type').css('display','');
    $('.fedex-service-type').css('display','none');
    $("#pincode_servisability_type_forward").css('display','none');
    if(courier.toLowerCase() == 'dotzot') {
      $('input[name=\'forward_service_type\'][value="Express"]').prop('checked',true);
      $("#pincode_servisability_type_forward").css('display','');
    }else if(courier.toLowerCase() == 'bluedart'){
      $('input[name=\'forward_service_type\'][value="Economy"]').prop('checked',true);
      $("#pincode_servisability_type_forward").css('display','');
    }else if(courier.toLowerCase() == 'fedex'){
      $('.api-forward-service-type').css('display','none');
      $('.fedex-service-type').css('display','');
    }else{ // for other couriers, srevice type fixed
      $('.api-forward-service-type').css('display','none');
      $('.fedex-service-type').css('display','none');
    }
  }else{
    $('.forward_shippment_box').css('display','none');
    $('.fedex-service-type').css('display','none');
  }
});

$('#add-return-btn').on('click', function(){ 
  item = validateChange();
  if($('.flash-error').css('display') == 'none' && item.length > 0){
    $.ajax({
      type: 'POST',
      data: {data:item},
      url: 'index.php?route=sale/return/addNewReturns&token='+token,
      beforeSend: function() {
        show_overlay();
        $(this).attr("disabled", "disabled");
      },
      complete: function() {
        location.reload();
      },
    });
  }else{
    return false;
  }
});

function show_overlay()
{
   $('.overlay-screen').show();
}
function hide_overlay()
{
  $('.overlay-screen').hide();
}

function validateChange(){
  var isValid    = true;
  var err_msg    = [];
  var error_flag = 0;
  var item       = [];

  $( ".error-msg" ).html('');
  $( ".flash-error" ).css('display', 'none');

  $('tbody#products .products').each(function(){
    var return_id         = $(this).data('returnid');
    var op_id             = $(this).data('orderproductid');
    var combo_id          = $(this).find('.add_return_chck').data('combo-product-id');
    var seller_invoice_id = $(this).find('.add_return_chck').data('seller-invoice-id');
    var customer_payment_company = $(this).find('.add_return_chck').data('customer-payment-company');
    var return_checkbox   = $(this).find('.add_return_chck');
    if(return_checkbox.prop('checked') == true){

      var last_return_id       = $(this).find('.last_return_id').val();
      var last_return_reason   = $(this).find('.last_return_reason').val();
      var last_return_quantity = $(this).find('.last_return_quantity').val();
      var last_shipping_method = $(this).find('.last_shipping_method').val();
      var last_master_return_id= $(this).find('.last_master_return_id').val();
      var last_comment         = $(this).find('.last_comment').val();

      var return_reason        = $(this).find('.return_reason').val();
      var return_quantity      = $(this).find('.product_quantity').val();
      var buyer_invoice_id     = $(this).find('.buyer_invoice_id').val();
      var seller_id            = $(this).find('.seller_id').val();
      var total_quantity       = $(this).find('.total_quantity').val();
      var shipping_method      = $(this).find('.shipping_methods').val();
      var comment              = $(this).find('.comment').val();

      if(!return_reason){
        error_flag = 1;
        err_msg.push('Return Reason is not selected for : ' + op_id);
        $('#'+return_id).find('.return_reason').addClass('error');
      }
      if(return_quantity <= 0){
        error_flag = 1;
        err_msg.push('Return Qty is not selected for: ' + op_id);
        $('#'+return_id).find('.product_quantity').addClass('error');
      }
      if(!shipping_method){
        error_flag = 1;
        err_msg.push('Shipping Method is not selected for : ' + op_id);
        $('#'+return_id).find('.shipping_methods').addClass('error');
      }
      
      //Check if there is not any validation error
      if(error_flag == 0){
        if( last_return_reason != return_reason 
            || 
            last_return_quantity != return_quantity 
            || 
            last_shipping_method != shipping_method
          )
        {
          temp_item = {
                      "order_product_id"     : op_id,
                      "combo_id"             : combo_id,
                      "seller_invoice_id"    : seller_invoice_id,
                      "customer_payment_company" : customer_payment_company,
                      "order_id"             : $('#hddn_order_id').val(),
                      "order_no"             : $('#hddn_order_no').val(),
                      "customer_id"          : $('#hddn_customer_id').val(),
                      "first_name"           : $('#hddn_first_name').val(),
                      "last_name"            : $('#hddn_last_name').val(),
                      "email"                : $('#hddn_email').val(),
                      "telephone"            : $('#hddn_telephone').val(),
                      "last_return_id"       : last_return_id,
                      "last_return_reason"   : last_return_reason, 
                      "return_reason"        : return_reason,
                      "last_return_quantity" : last_return_quantity,
                      "return_quantity"      : return_quantity,
                      "last_shipping_method" : last_shipping_method,
                      "shipping_method"      : shipping_method,
                      "last_master_return_id": last_master_return_id,
                      "buyer_invoice_id"     : buyer_invoice_id,
                      "seller_id"            : seller_id,
                      "total_quantity"       : total_quantity,
                      "last_comment"         : last_comment,
                      "comment"              : comment
                      };

          item.push(temp_item);
        }
      }else{
        $( ".error-msg" ).append( err_msg + '<br>' );
        $( ".flash-error" ).css('display', 'block');
        err_msg = [];
      }
    }
  });
  return item;
}

$('select.return_reason, input.product_quantity, select.shipping_methods, .comment').change(function() {
  
  $(this).parent().parent().find("input.add_return_chck").prop( "checked", true );

  /*Select all combo products */
    // get parent TR >first TD checkbox data values for selected HTML element
      var order_product_id  = $(this).parent().parent().find("input.add_return_chck").data('order-product-id');
      var combo_product_id  = $(this).parent().parent().find("input.add_return_chck").data('combo-product-id');
      var master_return_id  = $(this).parent().parent().find("input.add_return_chck").data('master-return-id');
      var seller_invoice_id = $(this).parent().parent().find("input.add_return_chck").data('seller-invoice-id');
      
     // get values for selected Row elements 
      var return_reason_id = $(this).parent().parent().find('.return_reason').val();
      var return_quantity  = $(this).parent().parent().find('.product_quantity').val();
      var shipping_methods = $(this).parent().parent().find('.shipping_methods').val();
      var comment          = $(this).parent().parent().find('.comment').val();

    //Loop over all products to match for combo products
       $('tbody#products .products').each(function(){

          //get current TR > TD checkbox data values
            var cur_order_product_id  = $(this).find("input.add_return_chck").data('order-product-id');
            var cur_product_id        = $(this).find("input.add_return_chck").data('product-id');
            var cur_combo_product_id  = $(this).find("input.add_return_chck").data('combo-product-id');
            var cur_master_return_id  = $(this).find("input.add_return_chck").data('master-return-id');
            var cur_seller_invoice_id = $(this).find("input.add_return_chck").data('seller-invoice-id');
        // check if current TD data values matched with selected HTML element data values  
          if(combo_product_id !='' && 
             combo_product_id !='0' && 
             combo_product_id != 'undefined' && 
             combo_product_id != cur_product_id && 
             combo_product_id == cur_combo_product_id &&
             order_product_id != cur_order_product_id &&
             master_return_id == cur_master_return_id &&
             seller_invoice_id == cur_seller_invoice_id
             ) {
            // set current TR HTML element values with selected HTML element values  
              $(this).find("input.add_return_chck").prop( "checked", true );
              $(this).find('.return_reason').val(return_reason_id);
              // quantity values will updated by return reason element change event
                $(this).find('.product_quantity').val(return_quantity); 
              $(this).find('.shipping_methods').val(shipping_methods);
              $(this).find('.comment').val(comment);
          }         
       })
  /*Select all combo products */

});

$('select.return_reason').change(function() {
  var return_reason_id = $(this).val();
  var order_product_id  = $(this).parent().parent().find("input.add_return_chck").data('order-product-id');
  var combo_product_id  = $(this).parent().parent().find("input.add_return_chck").data('combo-product-id');
  var master_return_id  = $(this).parent().parent().find("input.add_return_chck").data('master-return-id');
  var seller_invoice_id = $(this).parent().parent().find("input.add_return_chck").data('seller-invoice-id');

  if($.inArray( return_reason_id, [ "2", "3", "5", "7", "9" ] ) >= 0){
    var max_qty = $(this).parent().parent().find("input.product_quantity").attr('max');
    var tr_class = $(this).parent().parent().first('tr').hasClass('bg-danger');

    $(this).parent().parent().find("input.product_quantity").val(max_qty );
     
     $('tbody#products .products').each(function(){

        var cur_order_product_id  = $(this).find("input.add_return_chck").data('order-product-id');
        var cur_product_id        = $(this).find("input.add_return_chck").data('product-id');
        var cur_combo_product_id  = $(this).find("input.add_return_chck").data('combo-product-id');
        var cur_master_return_id  = $(this).find("input.add_return_chck").data('master-return-id');
        var cur_seller_invoice_id = $(this).find("input.add_return_chck").data('seller-invoice-id');
            
          if( combo_product_id !='' && 
             combo_product_id !='0' && 
             combo_product_id != 'undefined' && 
             combo_product_id != cur_product_id && 
             combo_product_id == cur_combo_product_id && 
             order_product_id != cur_order_product_id &&
             master_return_id == cur_master_return_id && 
             seller_invoice_id == cur_seller_invoice_id
          ) {
            $(this).find('.product_quantity').val(max_qty);
         }
     })
  }

  $(this).parent().parent().find("input.add_return_chck").prop( "checked", true );

});

$('.add_return_chck').change(function(){
  var is_checked = $(this).prop('checked');
  var combo_product_id = $(this).data('combo-product-id');
  var order_product_id = $(this).data('order-product-id');
  var master_return_id = $(this).data('master-return-id');
  var seller_invoice_id = $(this).data('seller-invoice-id');

    $('tbody#products .products').each(function(){
       var cur_order_product_id  = $(this).find("input.add_return_chck").data('order-product-id');
       var cur_product_id        = $(this).find("input.add_return_chck").data('product-id');
       var cur_combo_product_id  = $(this).find("input.add_return_chck").data('combo-product-id');
       var cur_master_return_id  = $(this).find("input.add_return_chck").data('master-return-id');
       var cur_seller_invoice_id = $(this).find("input.add_return_chck").data('seller-invoice-id');
         
       if( combo_product_id !='' && 
           combo_product_id !='0' && 
           combo_product_id != 'undefined' && 
           combo_product_id != cur_product_id && 
           combo_product_id == cur_combo_product_id && 
           order_product_id != cur_order_product_id &&
           master_return_id == cur_master_return_id && 
           seller_invoice_id == cur_seller_invoice_id
        ) {
          $(this).find('.add_return_chck').prop('checked',is_checked);
       }   
    })
})


$('#button-search').click(function () {
  $('.address_form_list').empty();
  $.ajax({
    url : 'index.php?route=sale/order/getSearchAddress&token='+token,
    type: 'POST',
    dataType: 'json',
    data: '&filter_warehouse=' + encodeURIComponent($('input[name=\'filter_warehouse_name\']').val()) +
           '&filter_city=' + encodeURIComponent($('input[name=\'filter_warehouse_city\']').val()),
    beforeSend: function() {
      $('#button-search').button('loading');
    },
    complete: function() {
      $('#button-search').button('reset');
    },
    success: function(json) {
      var row_count = 0;
      $.each(json, function(index,element){
        row_count += 1;
        if(row_count > 10){ return false; }
        var warehouse_address1 = '';
        var warehouse_address2 = '';
        if(element.address_1 != ''){
          warehouse_address1 = element.address_1 + '<br>';
        }
        if(element.address_2 != ''){
          warehouse_address2 = element.address_2 + ' <br>';
        }
        
        $('.address_form_list').append(
            '<div class="col-sm-3">' +
              '<label class="custom_lbl" for="input-address-'+element.warehouse_id+'">' +
                '<div class="warehouse_addresses_list address-div warehouse_address_height_set" id="warehouse_id_'+element.warehouse_id+'" data-address-id="'+element.warehouse_id+'">'
                                      + element.warehouse_name +'<br>'
                                      + warehouse_address1 
                                      + warehouse_address2 
                                      + element.city +'-'
                                      + element.postcode +'<br>' 
                                      + element.zone_name +'-'
                                      + element.country_name+'<br>'
                                      + element.telephone
                +'</div>'
                +'</label>'
                +'<div class="address_select">' +
                  '<input type="radio" id="input-address-'+element.warehouse_id+'"  name="select_warehouse_id" value="'+element.warehouse_id+'" />' +
                  '<label class="" for="input-address-'+element.warehouse_id+'" >'+element.zone_name+' , '+ element.country_name +'</label>' +
                  '<input type="hidden" id="input-gati-vendor-'+element.warehouse_id+'"  name="gati_vendor_code" value="'+element.gati_vendor_code+'" />' +
                '</div>' +
            '</div>'
        );
      });
    }
  });
});

$('#button-search-forward').click(function () {
  $('.address_form_list_forward').empty();
  $.ajax({
    url : 'index.php?route=sale/order/getSearchAddress&token='+token,
    type: 'POST',
    dataType: 'json',
    data: '&filter_warehouse=' + encodeURIComponent($('input[name=\'filter_warehouse_name_forward\']').val()) +
           '&filter_city=' + encodeURIComponent($('input[name=\'filter_warehouse_city_forward\']').val()),
    beforeSend: function() {
      $('#button-search-forward').button('loading');
    },
    complete: function() {
      $('#button-search-forward').button('reset');
    },
    success: function(json) {
      var row_count = 0;
      $.each(json, function(index,element){
        row_count += 1;
        if(row_count > 10){ return false; }
        var warehouse_address1 = '';
        var warehouse_address2 = '';
        if(element.address_1 != ''){
          warehouse_address1 = element.address_1 + '<br>';
        }
        if(element.address_2 != ''){
          warehouse_address2 = element.address_2 + ' <br>';
        }
        
        $('.address_form_list_forward').append(
            '<div class="col-sm-3">' +
              '<label class="custom_lbl" for="input-address-'+element.warehouse_id+'-forward">' +
                '<div class="warehouse_addresses_list address-div warehouse_address_height_set" id="warehouse_id_'+element.warehouse_id+'-forward" data-address-id="'+element.warehouse_id+'">'
                                      + element.warehouse_name +'<br>'
                                      + warehouse_address1 
                                      + warehouse_address2 
                                      + element.city +'-'
                                      + element.postcode +'<br>' 
                                      + element.zone_name +'-'
                                      + element.country_name+'<br>'
                                      + element.telephone
                +'</div>'
                +'</label>'
                +'<div class="address_select">' +
                  '<input type="radio" id="input-address-'+element.warehouse_id+'-forward" class="" name="select_warehouse_id_forward" value="'+element.warehouse_id+'" />' +
                  '<label class="" for="input-address-'+element.warehouse_id+'" >'+element.zone_name+' , '+ element.country_name +'</label>' +
                  '<input type="hidden" id="input-gati-vendor-'+element.warehouse_id+'-forward" class="" name="gati_vendor_code" value="'+element.gati_vendor_code+'" />' +
                '</div>' +
            '</div>'
        );
      });
    }
  });
});

function isPincodeServiceable(){
    var courier_partner = $('#courier_partner').val();
    if($.trim(courier_partner) === '') {
        alert('Select courier partner.'); return false
    }
    var pincode = $('#pincode').val();
    if($.trim(pincode) === '') {
        alert('Enter pincode value'); return false
    }

    var serviceability_type   = $('input[name=\'serviceability_type\']:checked').val();

    $('#check_serviceable').text('Checking...');

    $.ajax({
      type: 'GET',
      data: {'pincode' : pincode,'courier_partner' : courier_partner,'serviceability_type' : serviceability_type},
      url: 'index.php?route=sale/return/checkAreaServiceableOrNot&token='+token,
      dataType: 'json',
      success: function(data) {
        if(data.success == 1){ 
          $('#msg-class').css('display','block');
          $('#msg-class').text(pincode+' PIN is serviceable.');
          $('#msg-class').removeClass();
          $('#msg-class').addClass('alert-success');
        }else{ 
          $('#msg-class').css('display','block');
          $('#msg-class').text(pincode+' PIN is not serviceable.');
          $('#msg-class').removeClass();
          $('#msg-class').addClass('alert-danger');
        }
        $('#check_serviceable').text('Check');
      },
    });
}

function isForwardPincodeServiceable(){
    var courier_partner = $('#courier_partner_forward').val();

    if($.trim(courier_partner) === '') {
        alert('Select courier partner.'); return false
    }
    var pincode = $('#pincode_forward').val();

    if($.trim(pincode) === '') {
        alert('Enter pincode value'); return false
    }

    var serviceability_type   = $('input[name=\'serviceability_type_forward\']:checked').val();

    $('#check_serviceable_forward').text('Checking...');

    $.ajax({
      type: 'GET',
      data: {'pincode' : pincode,'courier_partner' : courier_partner,'serviceability_type' : serviceability_type},
      url: 'index.php?route=sale/return/checkAreaServiceableOrNot&token='+token,
      dataType: 'json',
      success: function(data) {
        if(data.success == 1){ 
          $('#msg-class-forward').css('display','block');
          $('#msg-class-forward').text(pincode+' PIN is serviceable.');
          $('#msg-class-forward').removeClass();
          $('#msg-class-forward').addClass('alert-success');
        }else{ 
          $('#msg-class-forward').css('display','block');
          $('#msg-class-forward').text(pincode+' PIN is not serviceable.');
          $('#msg-class-forward').removeClass();
          $('#msg-class-forward').addClass('alert-danger');
        }
        $('#check_serviceable_forward').text('Check');
      },
    });
}

function addReverseShipment(){

  var order_no        = $('input[name=\'hddn_order_no\']').val();
  var order_id        = $('input[name=\'order_id\']').val();
  var customer_email  = $('input[name=\'hddn_email\']').val();
  var customer_id     = $('input[name=\'customer_id\']').val();
  var courier_partner = $('#courier_partner option:selected').val();
  
  if($.trim(courier_partner) == '') {
    alert('Select courier partner'); return false;
  }
  var pincode         = $('input[name=\'pincode\']').val();
  if($.trim(pincode) == '') {
    alert('Enter pincode value'); return false;
  }
  var selected_master_return_id = [];
    $("input[name='selected_master_return_id[]']:checked").each(function ()
    {
        selected_master_return_id.push(parseInt($(this).val()));
    });
  if(selected_master_return_id.length == 0){
    alert('Select master return ids'); return false;
  } 
  var package_desc  = $('input[name=\'package_desc\']').val();
  if($.trim(package_desc) == ''){
    alert('Enter package description'); return false;
  }
  var package_value = $('input[name=\'package_value\']').val(); 
  if($.trim(package_value) == '' || $.trim(package_value) == '0'){
    alert('Package value can not be blank or 0'); return false;
  }
  var hddn_return_ids = $('input[name=\'hddn_return_ids\']').val(); 
  var package_qty   = $('input[name=\'package_qty\']').val();
  if($.trim(package_qty) == '' || $.trim(package_qty) == '0'){
    alert('Package qty can not be blank or 0'); return false;
  }
  var package_weight= $('input[name=\'package_weight\']').val();
  if($.trim(package_weight) == '' || $.trim(package_weight) == '0'){
    alert('Package weight can not be blank or 0'); return false;
  }
  var return_reason = $('input[name=\'return_reason\']').val(); 
  var select_warehouse_id = $('input[name=\'select_warehouse_id\']:checked').val()
  if($.trim(select_warehouse_id) == '' || $.trim(select_warehouse_id) == '0'){
    alert('Select warehouse value'); return false;
  }
  var pickup_address_id = $('input[name=\'pickup-address-id\']:checked').val();
  if($.trim(pickup_address_id) == '' || $.trim(pickup_address_id) == '0'){
    alert('Select pickup address'); return false;
  }
  var cust_name = $('input[name=\'cust_name_'+pickup_address_id+'\']').val();

  if($.trim(cust_name) == ''){
    alert('Customer name can not be blank, check order details.'); return false;
  }
  var cust_address1 = $('input[name=\'cust_address1_'+pickup_address_id+'\']').val();
  var cust_address2 = $('input[name=\'cust_address2_'+pickup_address_id+'\']').val();
  var cust_city = $('input[name=\'cust_city_'+pickup_address_id+'\']').val();
  var cust_pin = $('input[name=\'cust_pin_'+pickup_address_id+'\']').val();
  if($.trim(cust_pin) == ''){
    alert('Customer pincodes can not be blank, check order details.'); return false;
  }
  var cust_telephone = $('input[name=\'cust_telephone_'+pickup_address_id+'\']').val();
  var remarks        = $('#remarks').val();
  var service_type   = $('input[name=\'service_type\']:checked').val();
  
  var cust_zone = $('input[name=\'cust_zone_'+pickup_address_id+'\']').val();
  var cust_country = $('input[name=\'cust_country_'+pickup_address_id+'\']').val();

  var query  = '&customer_id='+customer_id+'&order_no=' + order_no + '&order_id=' + order_id + '&courier_partner='+courier_partner;
      query += '&pincode='+encodeURIComponent(pincode) +'&selected_master_return_id='+selected_master_return_id+'&service_type='+service_type;
      query += '&package_desc='+encodeURIComponent(package_desc)+'&remarks='+encodeURIComponent(remarks)+'&package_value='+package_value+'&return_ids='+hddn_return_ids;
      query += '&package_qty='+package_qty+'&package_weight='+package_weight+'&return_reason='+encodeURIComponent(return_reason);
      query += '&select_warehouse_id='+select_warehouse_id+'&pickup_address_id='+pickup_address_id+'&cust_email='+customer_email;
      query += '&cust_name='+encodeURIComponent(cust_name)+'&cust_address1='+encodeURIComponent(cust_address1)+'&cust_address2='+encodeURIComponent(cust_address2);
      query += '&cust_city='+encodeURIComponent(cust_city)+'&cust_pin='+encodeURIComponent(cust_pin)+'&cust_telephone='+encodeURIComponent(cust_telephone);
      query += '&cust_zone='+encodeURIComponent(cust_zone)+'&cust_country='+encodeURIComponent(cust_country);     

 //console.log(query); return;

  $.ajax({
    type: 'POST',
    data: query,
    url: 'index.php?route=sale/return/addReverseShipment&token='+token,
    dataType: 'json',
    beforeSend: function() {
      $('.reverse-shippment').html('loading...');
    },
    complete: function() {
      $('.reverse-shippment').html('Submit');
    },
    success: function(response) {

        $.each(response,function(index,item){

            if(index === 'success') {
               alert('Reverse shipment added successfully.');
               window.location.reload(); 
            }
            if(index === 'error') {
               alert(item);
            }
        })

       $('.reverse-shippment').html('Submit');
    },
    error: function(xhr, ajaxOptions, thrownError) { 
      var string = xhr.responseText.replace(/<b>/g,'');
          string = string.replace(/<\/b>/g,'');
          alert("Error occure during request processing ::\n"+string);
      $('.reverse-shippment').html('Submit');
    }
  });
}

function addForwardShipment(){

  var order_no        = $('input[name=\'hddn_order_no\']').val();
  var order_id        = $('input[name=\'order_id\']').val();
  var customer_email  = $('input[name=\'hddn_email\']').val();
  var customer_id     = $('input[name=\'customer_id\']').val();
  var courier_partner = $('#courier_partner_forward option:selected').val();
  
  if($.trim(courier_partner) == '') {
    alert('Select courier partner'); return false;
  }
  var pincode         = $('input[name=\'pincode_forward\']').val();
  if($.trim(pincode) == '') {
    alert('Enter pincode value'); return false;
  }
  var selected_master_return_id = [];
    $("input[name='selected_master_return_id_forward[]']:checked").each(function ()
    {
        selected_master_return_id.push(parseInt($(this).val()));
    });
  if(selected_master_return_id.length == 0){
    alert('Select return ids'); return false;
  } 
  var package_desc  = $('input[name=\'package_desc_forward\']').val();
  if($.trim(package_desc) == ''){
    alert('Enter package description'); return false;
  }
  var package_value = $('input[name=\'package_value_forward\']').val(); 
  if($.trim(package_value) == '' || $.trim(package_value) == '0'){
    alert('Package value can not be blank or 0'); return false;
  }
  var hddn_return_ids = $('input[name=\'hddn_return_ids_forward\']').val(); 
  var package_qty   = $('input[name=\'package_qty_forward\']').val();
  if($.trim(package_qty) == '' || $.trim(package_qty) == '0'){
    alert('Package qty can not be blank or 0'); return false;
  }
  var package_weight= $('input[name=\'package_weight_forward\']').val();
  if($.trim(package_weight) == '' || $.trim(package_weight) == '0'){
    alert('Package weight can not be blank or 0'); return false;
  }
  var return_reason = $('input[name=\'return_reason_forward\']').val(); 
  var select_warehouse_id = $('input[name=\'select_warehouse_id_forward\']:checked').val()
  if($.trim(select_warehouse_id) == '' || $.trim(select_warehouse_id) == '0'){
    alert('Select pickup location [WareHouse]'); return false;
  }
  var pickup_address_id = $('input[name=\'pickup-address-id-forward\']:checked').val();
  if($.trim(pickup_address_id) == '' || $.trim(pickup_address_id) == '0'){
    alert('Select delivery address'); return false;
  }
  var cust_name = $('input[name=\'cust_name_'+pickup_address_id+'_forward\']').val();

  if($.trim(cust_name) == ''){
    alert('Customer name can not be blank, check order details.'); return false;
  }
  var cust_address1 = $('input[name=\'cust_address1_'+pickup_address_id+'_forward\']').val();
  var cust_address2 = $('input[name=\'cust_address2_'+pickup_address_id+'_forward\']').val();
  var cust_city = $('input[name=\'cust_city_'+pickup_address_id+'_forward\']').val();
  var cust_pin = $('input[name=\'cust_pin_'+pickup_address_id+'_forward\']').val();
  var cust_zone_id = $('input[name=\'zone_id_'+pickup_address_id+'_forward\']').val();
  var cust_zone = $('input[name=\'cust_zone_'+pickup_address_id+'_forward\']').val();
  var cust_country = $('input[name=\'cust_country_'+pickup_address_id+'_forward\']').val();

  if($.trim(cust_pin) == ''){
    alert('Customer pincodes can not be blank, check order details.'); return false;
  }
  var cust_telephone = $('input[name=\'cust_telephone_'+pickup_address_id+'_forward\']').val();
  var remarks       = $('#remarks_forward').val();

  if($.trim(courier_partner).toLowerCase() == 'fedex') {
    var service_type   = $('#fedex_service_type option:selected').val();
    if($.trim(service_type)==''){
      service_type = 'STANDARD_OVERNIGHT';
    }
  }else{
    var service_type   = $('input[name=\'forward_service_type\']:checked').val(); 
  }
  var fedex_account_code   = $('input[name=\'fedex_account_code\']:checked').val();

  var query  = '&customer_id='+customer_id+'&order_no=' + order_no + '&order_id=' + order_id + '&courier_partner='+courier_partner;
    query += '&pincode='+encodeURIComponent(pincode) +'&selected_master_return_id='+selected_master_return_id+'&service_type='+service_type;
    query += '&package_desc='+encodeURIComponent(package_desc)+'&remarks='+encodeURIComponent(remarks)+'&package_value='+package_value+'&return_ids='+hddn_return_ids;
    query += '&package_qty='+package_qty+'&package_weight='+package_weight+'&return_reason='+encodeURIComponent(return_reason);
    query += '&select_warehouse_id='+select_warehouse_id+'&pickup_address_id='+pickup_address_id+'&cust_email='+customer_email;
    query += '&cust_name='+encodeURIComponent(cust_name)+'&cust_address1='+encodeURIComponent(cust_address1)+'&cust_address2='+encodeURIComponent(cust_address2);
    query += '&cust_city='+encodeURIComponent(cust_city)+'&cust_pin='+encodeURIComponent(cust_pin)+'&cust_telephone='+encodeURIComponent(cust_telephone);
    query += '&cust_zone_id='+cust_zone_id+'&account_code='+fedex_account_code;
    query += '&cust_zone='+encodeURIComponent(cust_zone)+'&cust_country='+encodeURIComponent(cust_country);
   //console.log(query); return false;
  $.ajax({
    type: 'POST',
    data: query,
    url: 'index.php?route=sale/return/addForwardShipment&token='+token,
    dataType: 'json',
    beforeSend: function() {
      $('.forward-shippment').html('loading...');
    },
    complete: function() {
      $('.forward-shippment').html('Submit');
    },
    success: function(response) {

        $.each(response,function(index,item){

            if(index === 'success') {
               alert('Shipment added successfully.');
               window.location.reload(); 
            }
            if(index === 'error') {
               alert(item);
            }
        })

       $('.forward-shippment').html('Submit');
    },
    error: function(xhr, ajaxOptions, thrownError) { alert('Error');
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      $('.forward-shippment').html('Submit');
    }
  });
}

$('.add_cust_pickup_address').click(function(){

  if(validateAddPickupAddress()){
    $('.cust_zone_2').val($(".select_zone_2 option:selected").text());
    $('.cust_country_2').val($(".select_country_2 option:selected").text());

    var pickup_address = '';
    pickup_address += $('.firstname_2').val() + '<br>';
    pickup_address += $('.address1_2').val() + ', ' + $('.address2_2').val() + '<br>';
    pickup_address += 'Contact No.: ' + $('.cust_telephone_2').val() + '<br>';
    pickup_address += $('.city_2').val() + '<br>';
    pickup_address += $('.postcode_2').val() + '<br>';
    pickup_address += $(".select_zone_2 option:selected").text() + '<br>';
    pickup_address += $(".select_country_2  option:selected").text();

    $('.pickup_address_2').html($.trim(pickup_address));
    $('.pickup-address-2').css('display', 'block');
    $("#pickup-address-2").prop("checked", true);
    $(".close-btn").click();
  }
});

function validateAddPickupAddress(){
  var error_flag = false;
  var error_msg  = [];
  if( $('.firstname_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Customer Name is mendatory.");
  }
  if( $('.address1_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Address1 is mendatory.");
  }
  if( $('.cust_telephone_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Phone No. is mendatory.");
  }
  if( $('.postcode_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* PostCode is mendatory.");
  }else{
    var pincode = $('.postcode_2').val();

  var courier_partner = $('#courier_partner').val();

    $.ajax({
      type: 'GET',
      data: {'pincode' : pincode,'courier_partner' : courier_partner},
      url: 'index.php?route=sale/return/checkAreaServiceableOrNot&token='+token,
      dataType: 'json',
      async: false,
      success: function(data) {
        if(!data.success){
          error_flag    = true;
          error_msg.push("* PostCode is not serviceable.");
        }else{
          
        }
      },
    });
  }
  if( $('.city_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* City is mendatory.");
  }
  if( $('#select_country_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Country is mendatory.");
  }
  if( $('.select_zone_2').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Zone is mendatory.");
  }
  $('.add_pickup_address_error').html('');
  if(error_flag){
    var errors = '';
    $.each( error_msg, function( index, value ){
      errors += value + "<br>";
    });
    $('.add_pickup_address_error').html($.trim(errors));
    $('.add_pickup_address_error').css('display', 'block');
    return false;
  }else{
    $('.add_pickup_address_error').css('display', 'none');
    return true;
  }
}

function onPopupLoad(){
  zone_id = $('#zone_id_2').val();
  var cust_country_2 = $('select[name=\'select_country_2\']').val();
  country(cust_country_2, zone_id);
  return true;
}

function country(country_id, zone_id) {
  $.ajax({
    url: 'index.php?route=sale/customer/country&token='+token+'&country_id=' + country_id,
    dataType: 'json',
    beforeSend: function() {
      $('select[name=\'select_country_2\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
    },
    complete: function() {
      $('.fa-spin').remove();
    },
    success: function(json) {
      html = '<option value="">Select</option>';

      if (json['zone'] && json['zone'] != '') {
        for (i = 0; i < json['zone'].length; i++) {
          html += '<option value="' + json['zone'][i]['zone_id'] + '"';

          if (json['zone'][i]['zone_id'] == zone_id) {
            html += ' selected="selected"';
          }

          html += '>' + json['zone'][i]['name'] + '</option>';
        }
      } else {
        html += '<option value="0">None</option>';
      }

      $('select[name=\'select_zone_2\']').html(html);
    },
    error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
}

$('.add_cust_pickup_address_forward').click(function(){

  if(validateAddPickupAddress1()){
    $('.cust_zone_2_forward').val($(".select_zone_2_forward option:selected").text());
    $('.cust_country_2_forward').val($(".select_country_2_forward option:selected").text());

    var pickup_address = '';
    pickup_address += $('.firstname_2_forward').val() + '<br>';
    pickup_address += $('.address1_2_forward').val() + ', ' + $('.address2_2_forward').val() + '<br>';
    pickup_address += 'Contact No.: ' + $('.cust_telephone_2_forward').val() + '<br>';
    pickup_address += $('.city_2_forward').val() + '<br>';
    pickup_address += $('.postcode_2_forward').val() + '<br>';
    pickup_address += $(".select_zone_2_forward option:selected").text() + '<br>';
    pickup_address += $(".select_country_2_forward  option:selected").text();
    $('.pickup_address_2_forward').html($.trim(pickup_address));
    $('.pickup-address-2-forward').css('display', 'block');
    $("#pickup-address-2-forward").prop("checked", true);
    $(".close-btn").click();
  }
});

function validateAddPickupAddress1(){
  var error_flag = false;
  var error_msg  = [];
  if( $('.firstname_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Customer Name is mendatory.");
  }
  if( $('.address1_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Address1 is mendatory.");
  }
  if( $('.cust_telephone_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Phone No. is mendatory.");
  }
  if( $('.postcode_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* PostCode is mendatory.");
  }else{
    var pincode = $('.postcode_2_forward').val();

  var courier_partner = $('#courier_partner_forward').val();

    $.ajax({
      type: 'GET',
      data: {'pincode' : pincode,'courier_partner' : courier_partner},
      url: 'index.php?route=sale/return/checkAreaServiceableOrNot&token='+token,
      dataType: 'json',
      async: false,
      success: function(data) {
        if(!data.success){
          error_flag    = true;
          error_msg.push("* PostCode is not serviceable.");
        }else{
          
        }
      },
    });
  }
  if( $('.city_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* City is mendatory.");
  }
  if( $('#select_country_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Country is mendatory.");
  }
  if( $('.select_zone_2_forward').val().length == 0 ) {
    error_flag    = true;
    error_msg.push("* Zone is mendatory.");
  }
  $('.add_pickup_address_error_forward').html('');
  if(error_flag){
    var errors = '';
    $.each( error_msg, function( index, value ){
      errors += value + "<br>";
    });
    $('.add_pickup_address_error_forward').html($.trim(errors));
    $('.add_pickup_address_error_forward').css('display', 'block');
    return false;
  }else{
    $('.add_pickup_address_error_forward').css('display', 'none');
    return true;
  }
}

function onPopupLoad1(){
  zone_id = $('#zone_id_2_forward').val();
  var cust_country_2 = $('select[name=\'select_country_2_forward\']').val();
  country1(cust_country_2, zone_id);
  return true;
}

function country1(country_id, zone_id) {
  $.ajax({
    url: 'index.php?route=sale/customer/country&token='+token+'&country_id=' + country_id,
    dataType: 'json',
    beforeSend: function() {
      $('select[name=\'select_country_2_forward\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
    },
    complete: function() {
      $('.fa-spin').remove();
    },
    success: function(json) {
      html = '<option value="">Select</option>';

      if (json['zone'] && json['zone'] != '') {
        for (i = 0; i < json['zone'].length; i++) {
          html += '<option value="' + json['zone'][i]['zone_id'] + '"';

          if (json['zone'][i]['zone_id'] == zone_id) {
            html += ' selected="selected"';
          }

          html += '>' + json['zone'][i]['name'] + '</option>';
        }
      } else {
        html += '<option value="0">None</option>';
      }

      $('select[name=\'select_zone_2_forward\']').html(html);
    },
    error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
}

$('.date').datetimepicker({
   pickTime: false,
   minDate:new Date()
});

function getReverseShipmentHistory(shipping_id){ 
  $('#history_body').html('');
  $('#shipmentHistoryBody').html('');
  $('#history_body').parent().parent().find('.alert').remove();
  var tracking_no     = $('#tracking_no_'+shipping_id).val();
  var courier_company = $('#courier_company_'+shipping_id).val();
  var row  = '';

  $('#shipmentHistoryTab').html('Shipment history - ' + tracking_no);
  $('.warehouse-detail').html('WareHouse - ' + $('#vendor_'+shipping_id).val() + ', ' + $('#vendor_city_'+shipping_id).val());
  
  $('#tracking_button_'+shipping_id).html('<i class="fa fa-refresh fa-spin"></i>');
  
  $.ajax({
    type: 'GET',
    url: 'index.php?route=sale/return/getCourierTrackingHistory&tracking_no='+tracking_no+'&courier_company='+courier_company+'&token='+token,
    dataType: 'json',
    beforeSend: function() {
      $('#tracking_button_'+shipping_id).html('<i class="fa fa-refresh fa-spin"></i>');
    },
    success: function(data) {
      //console.log(data); return ;
      if(data.response === 'success'){
        $('.no-shipment-history').css('display', 'none');
        $('#shipmentHistoryBody').html(data.history);
      }else{
        $('#shipmentHistoryBody').html(data.error);
        $('.no-shipment-history').css('display', 'block');
      }
      $('#tracking_button_'+shipping_id).html('<i class="fa fa-eye"></i>');
      $('#shipment_histopry_popup').modal('show');
    },
  });
}

function cancelReverseShipment(shipping_id, type)
{

  var courier_company = $('#courier_company_'+shipping_id).val();
  
    if(window.confirm('Are you sure, you want to cancel this reverse shipment')) {

      $('#shipment_cancel_button_'+shipping_id).html('<i class="fa fa-refresh fa-spin"></i>');
      
      $.ajax({
            type: 'GET',
            url: 'index.php?route=sale/return/cancelReverseShipment&shipping_id='+shipping_id+'&type='+type+'&token='+token,
            dataType: 'json',
            beforeSend: function() {
              $('#shipment_cancel_button_'+shipping_id).html('<i class="fa fa-refresh fa-spin"></i>');
            },
            success: function(data) {
              if(data.success != '1') {
                alert(data.error);
              }
            },
            complete: function(){
              document.location.reload();
            }
          });

    }
}

$('.selected_master_return_id').change(function() {
  var master_return_id = $(this).val();
  var package_value    = $('#package_value').val();
  var package_qty      = $('#package_qty').val();
  var package_weight   = $('#package_weight').val();
  var hddn_return_ids  = $('#hddn_return_ids').val();
  var is_added = false;
  if($(this).prop('checked') == true){
    is_added = true;
  }
  $.ajax({
    type: 'GET',
    url: 'index.php?route=sale/return/getReturnDetailByMaster&master_return_id='+master_return_id+'&token='+token,
    async: false,
    dataType: 'json',
    success: function(data) {
      if(data.response == 'success'){
        if(is_added){
          package_value   = parseInt(package_value)     + parseInt(data.return_value);
          package_qty     = parseInt(package_qty)       + parseInt(data.return_qty);
          package_weight  = parseFloat(package_weight)  + parseFloat(data.return_weight);
          hddn_return_ids = hddn_return_ids + ',' + data.return_ids;

        }else{
          package_value   = parseInt(package_value)     - parseInt(data.return_value);
          package_qty     = parseInt(package_qty)       - parseInt(data.return_qty);
          package_weight  = parseFloat(package_weight)  - parseFloat(data.return_weight);
          hddn_return_ids = hddn_return_ids.replace(data.return_ids, "");
        }
        
        package_weight = Math.round(package_weight * 100) / 100;
        package_value  = Math.round(package_value * 100) / 100;

        hddn_return_ids = hddn_return_ids.replace(',,',',');
        $('#package_value').val(package_value);
        $('#package_qty').val(package_qty);
        $('#package_weight').val(package_weight);
        $('#return_reason').val(data.return_reason);
        $('#hddn_return_ids').val(hddn_return_ids.replace(/^,|,$/g,''));
      }else if(typeof data['error'] !== 'undefined') {
          alert(data['error']);
      }
    },
  });
});

$('.selected_master_return_id_forward').change(function() {
  var return_id = $(this).val();
  var forward = '_forward';
  var package_value    = $('#package_value'+forward).val();
  var package_qty      = $('#package_qty'+forward).val();
  var package_weight   = $('#package_weight'+forward).val();
  var hddn_return_ids  = $('#hddn_return_ids'+forward).val();
  var is_added = false;
  if($(this).prop('checked') == true){
    is_added = true;
  }
  
  $.ajax({
    type: 'GET',
    url: 'index.php?route=sale/return/getReturnDetailById&return_id='+return_id+'&token='+token,
    async: false,
    dataType: 'json',
    success: function(data) {
      if(data.response == 'success'){
        if(is_added){
          package_value   = parseInt(package_value)     + parseInt(data.return_value);
          package_qty     = parseInt(package_qty)       + parseInt(data.return_qty);
          package_weight  = parseFloat(package_weight)  + parseFloat(data.return_weight);
          hddn_return_ids = hddn_return_ids + ',' + data.return_ids;

        }else{
          package_value   = parseInt(package_value)     - parseInt(data.return_value);
          package_qty     = parseInt(package_qty)       - parseInt(data.return_qty);
          package_weight  = parseFloat(package_weight)  - parseFloat(data.return_weight);
          hddn_return_ids = hddn_return_ids.replace(data.return_ids, "");
        }
        hddn_return_ids = hddn_return_ids.replace(',,',',');
        package_value = Math.round(package_value * 100) / 100;
        package_weight = Math.round(package_weight * 100) / 100;
        $('#package_value'+forward).val(package_value);
        $('#package_qty'+forward).val(package_qty);
        $('#package_weight'+forward).val(package_weight);
        $('#hddn_return_ids'+forward).val(hddn_return_ids.replace(/^,|,$/g,''));
      }
    },
  });
});

/* MSA march 18 */
$(document).on('change','.return_action_list, .shipping_methods_list',function(){
  if($.trim($(this).val())!=='') {
    $(this).parent().siblings(":first").find('input:checkbox').prop("checked",true);
  }
})
/* MSA march 18 */

function clickAllId(obj,tab){
    if($(obj).hasClass('addForm')){
      $('.order_product_id').prop('checked', obj.checked);
    }
    if($(obj).hasClass('statusForm'))
    {
     $('tbody'+'#products-change-status-'+tab).find('.products-change-status').find('.change_status').prop('checked',obj.checked);
    }

    if($(obj).hasClass('cnForm'))
    {
      $('tbody'+'#cn-products-'+tab).find('.cn-products-list-'+tab).find('.cn-return-ids-'+tab).prop('checked',obj.checked);

        var old_value = $('#reversal_shipping_charges_'+tab).data('privious');
        $('#reversal_shipping_charges_'+tab).html(old_value); 
    }
}

$('.resetChangeStatusRow').click(function(){
   var row_id           = $(this).data('return-id');
   var parent_div_id    = $(this).data('container-id');
   var tab_key          = $(this).data('return-tab');
   if($("#"+parent_div_id+'-'+tab_key).find('.'+parent_div_id).length) {
      $(this).parent().siblings(":first").find('input:checkbox').prop("checked",false);
    
      var return_action_list_id =   "#return_action_list_"+tab_key+'_'+row_id;     
      var return_action_list_privious = $(return_action_list_id).data('privious');
      if($(return_action_list_id+" option[value='"+return_action_list_privious+"']").length!=0){
        $(return_action_list_id).val(return_action_list_privious);
      }else{
        $(return_action_list_id).val('0');
      }
      
      var return_shipping_method_id = "#shipping_methods_list_"+tab_key+'_'+row_id;
      var return_shipping_method_privious = $(return_shipping_method_id).data('privious');
      
      if(!$(return_shipping_method_id).prop('disabled')) {
        if($(return_shipping_method_id+" option[value='"+return_shipping_method_privious+"']").length!=0){
          $(return_shipping_method_id).val(return_shipping_method_privious);
        }else{
          $(return_shipping_method_id).val('0');
        }
      }

      var return_action_quantity_id = "#return_action_quantity_"+tab_key+'_'+row_id;
      var return_action_quantity_privious = $(return_action_quantity_id).data('privious');
          $(return_action_quantity_id).val(return_action_quantity_privious);

      var return_action_reason_id = "#action_reason_"+tab_key+'_'+row_id;
      var return_action_reason_privious = $(return_action_reason_id).data('privious');
      if($(return_action_reason_id+" option[value='"+return_action_reason_privious+"']").length!=0){
        $(return_action_reason_id).val(return_action_reason_privious);
      }else{
        $(return_action_reason_id).val('0');
      }

      var return_internal_note_id = "#internal_note_"+tab_key+'_'+row_id;
      var return_internal_note_privious = $(return_internal_note_id).data('privious');
          $(return_internal_note_id).val(return_internal_note_privious);
      
   }
})

$('.change_return_quantity').change(function(){
  
  var obj = $(this);
  var return_id = obj.data('return-id');
  var tab = obj.data('tab');
  var return_quantity = obj.data('quantity');

  var action_id = "return_action_list_"+tab+"_"+return_id;
  var selected_action = $("#"+action_id).val();
  var value = $.trim($(this).val());

  // Extra Goods
  if(selected_action == extra_goods_received_action_id) // value(extra_goods_received_action_id) set in return_order_form.tpl
  {
     if(value < return_quantity || value <= 0) {
        obj.val(return_quantity);
     }
  }
  // Short Goods
  if(selected_action == short_goods_receved_action_id) // value(short_goods_receved_action_id) set in return_order_form.tpl
  {
     if(value > return_quantity || value <= 0) {
        obj.val(return_quantity);
     }
  }

})

function resetRow(oop,key,text){ 

  var pid = oop.replace('#','');
  rowId = '#'+key;
  if(key == '0'){
    rowId = oop+text;
  }

  if($(rowId).length) {

      $(rowId).find('input:checkbox').prop("checked",false);

      var return_reason_privious = $(rowId).find('.return_reason').data('privious');
      $(rowId).find('.return_reason').val(return_reason_privious);

      var product_quantity_privious = $(rowId).find('.product_quantity').data('privious');
      $(rowId).find('.product_quantity').val(product_quantity_privious);

      var shipping_methods_privious = $(rowId).find('.shipping_methods').data('privious');
      $(rowId).find('.shipping_methods').val(shipping_methods_privious);

      var comment_privious = $(rowId).find('.comment').data('privious');
      $(rowId).find('.comment').val(comment_privious);

  }

}

function putSelectValue(obj,products,tar,products_list,element_class) {
   if($(obj).val() != ''){
     $('tbody'+products).find(products_list).each(function(index,element){
       var row = $(element);
        if(row.find(element_class).prop('checked') == true){
         if(row.find(tar).prop('disabled') != true){
           row.find(tar).val($(obj).val()).change();
         }
       }
      });
   }
}

function splitReturn(obj){

  var tab_key = $(obj).data('tab_key');
  var rqty    = $(obj).data('rqty');
  var rid     = $(obj).data('rid');
  var oid     = $(obj).data('order_id');

  $('.ttl-return-qty-'+tab_key).html(rqty);

  $('.order_id-'+tab_key).val(oid);

  $('.return_id-'+tab_key).val(rid);

  $('.split-return-qty-'+tab_key).val(0);

  $('.split-return-qty-'+tab_key).attr('max', rqty-1);

  $('#split-return-popup-'+tab_key).modal('show');
}

function splitReturnSubmit(tab_key){
  $('#err-div-'+tab_key).css("display", "none");

  var order_id         = $('.order_id-'+tab_key).val();
  var return_id        = $('.return_id-'+tab_key).val();
  var split_return_qty = $('.split-return-qty-'+tab_key).val();
  if(split_return_qty <= 0){
    $('#err-div-'+tab_key).html('Split Return Quantity must be greater then 0.');
    $('#err-div-'+tab_key).css("display", "block");
    return false;
  }

  $('#slit-button-'+tab_key).attr('disabled','disabled');

  //Split Return into 2 returns by ajax calling
  $.ajax({
    type: 'POST',
    data: {
           'order_id'  : order_id,
           'return_id' : return_id,
           'qty'       : split_return_qty
          },
    url: 'index.php?route=sale/return/splitReturn&token='+token,
    dataType: 'json',
    success: function(data) {
      document.location.reload();
    },
  });
}

function convertReturn(obj){
  var tab_key    = $(obj).data('tab_key');
  var rid        = $(obj).data('rid');
  var oid        = $(obj).data('order_id');
  var rtype      = $(obj).data('rtype');
  var raction_id = $(obj).data('raction_id');

  $('.order_id-'+tab_key).val(oid);
  $('.return_id-'+tab_key).val(rid);
  $('.rtype-'+tab_key).val(rtype);

  //Get Return / Replacement Reason to convert
  $.ajax({
    type: 'POST',
    data: {
           'rtype'  : rtype
          },
    dataType: 'json',
    url: 'index.php?route=sale/return/getReturnReasonsForConversion&token='+token,
    
    success: function(data) {
      var len = data.length;
      var reasons_str = '<option value="0">Select</option>';
      for (i=0; i<len; i++) {
        reasons_str += '<option value="'+ data[i]['return_reason_id'] +'">';
        reasons_str += data[i]['name'];
        reasons_str += '</option>';
      }
      $('.convert-return-reason').html(reasons_str);
    },
  });

  $('#convert-return-action-'+tab_key).val(raction_id);

  //Show pop-up
  $('#return_replacement_convert_popup-'+tab_key).modal('show');
}

function convertReturnSubmit(tab_key){
  $('#convert-err-div-'+tab_key).css("display", "none");

  var order_id         = $('.order_id-'+tab_key).val();
  var return_id        = $('.return_id-'+tab_key).val();
  var return_reason_id = $('#convert-return-reason-'+tab_key).val();
  var return_action_id = $('#convert-return-action-'+tab_key).val();
  var internal_note    = encodeURIComponent($('#convert-internal-note-'+tab_key).val());
  var rtype            = $('.rtype-'+tab_key).val();

  if(return_reason_id <= 0){
    $('#convert-err-div-'+tab_key).html('Return Reason must be selected.');
    $('#convert-err-div-'+tab_key).css("display", "block");
    return false;
  }

  $('#convert-return-button-'+tab_key).attr('disabled','disabled');

  //Convert Return to Replacement or Vice-Versa
  $.ajax({
    type: 'POST',
    data: {
           'order_id'         : order_id,
           'return_id'        : return_id,
           'rtype'            : rtype,
           'return_reason_id' : return_reason_id,
           'return_action_id' : return_action_id,
           'internal_note'    : internal_note
          },
    url: 'index.php?route=sale/return/convertReturn&token='+token,
    dataType: 'json',
    success: function(data) {
      document.location.reload();
    },
  });
}

function getHistory(obj){
  $('#history_body').html('');
  $('#history_body').parent().parent().find('.alert').remove();
  var i = 1;
  var row  = '';
  var pid = $(obj).data('opid');
  var model = $(obj).data('pmodel');
  var returnid = $(obj).data('returnid');
  var masterid = $(obj).data('masterid');
  $('#historyTab').html('Product History - ' + model);
  if( returnid != "" && order_product_history[pid][masterid]){
    var keys = Object.keys(order_product_history[pid][masterid]);
    $(keys.reverse()).each(function(index,element){
      element = order_product_history[pid][masterid][element];
      var reason = element.return_reason_id == 0 ? "" : element.return_reason.name;
      var action = element.return_action_id == 0 ? "" : element.return_action.name;
      row  += '<tr>';
      row     +=    '<td>' + i  +'</td>';
      row     +=    '<td>' +  element.quantity + '</td>';
      row     +=    '<td>' + reason  + '</td>';
      row     +=    '<td>' + action  + '</td>';
      row     +=    '<td>' +  element.shipping_method + '</td>';
      row     +=    '<td>' +  element.comment + '</td>';
      row     +=    '<td>' +  element.internal_note + '</td>';
      row     +=    '<td>' +  element.date_added + '</td>';
      row     +=    '<td>' +  element.user + '</td>';
      row     += '</tr>';
      i++;
    });
    $('#history_body').html(row);
  }
  else{
    $('#history_body').parent().after('<p class="alert alert-danger">No History find for this product<p>');
  }
  $('#myModal').modal('show');
}

function toggleStatusForm(obj,cases){
  if($(obj).parent().hasClass('active')){
    return false;
  }
  var con = true;
  if(cases != '.addForm'){
    $('.products.addForm').each(function(){
      $(this).find('.return_reason, .product_quantity, .comment, .shipping_methods').removeClass('value_changed');
      if($(this).find('.return_reason').val() != 0){
        $(this).find('.return_reason').addClass('value_changed');
      }
      if($(this).find('.product_quantity').val() != 0){
        $(this).find('.product_quantity').addClass('value_changed');
      }
      if($(this).find('.comment').val().trim() != ""){
        $(this).find('.comment').addClass('value_changed');
      }
      if($(this).find('.shipping_methods').val() != ""){
        $(this).find('.shipping_methods').addClass('value_changed');
      }
    });
  }
  if($('.value_changed').length > 0){
    con = confirm("This Tab contains changes.. Are you sure to go on new tab");
  }
  if(!con){
    var li = $(obj).parents('ul').find('li.active');
    setTimeout(function(){
      $(obj).parents('ul').find('li').removeClass('active');
      li.addClass('active');
      $('.tab-pane').removeClass('active');
      $(li.find('a').attr('href')).addClass('active');
      $(obj).blur();
    },10);
    return false;
  }
  $('#products').removeClass();
  //$('.resetRow').click();
  $('.order_product_id, .selectAll').prop('checked', false);
  if(cases == '.statusForm'){
    $('#products').addClass('statusForm_active');
    $('.addForm').addClass('hidden');
  }
  if(cases == '.addForm'){
    $('#products').addClass('addForm_active');
    $('.statusForm').addClass('hidden');
  }
  if(cases == '.other'){
    $('#products').addClass('other_active');
  }
  $('#input-return-action, #input-return-reason').val(0);
  $('#input-payment-methods, #input-shipping-methods').val('');
  $('.form-control').removeClass('value_changed');
  $('.last_value').addClass('hidden');
  location.hash = $(obj).attr('id');
  $(cases).removeClass('hidden');
}

function showLastValue(obj){
  var value = $(obj).val().trim();
  var last_value = $(obj).parent().find('.last_value');
  $(obj).removeClass("value_changed");
  last_value.addClass('hidden');

  if($(obj).hasClass('return_action')){
    value = value == 0 ? "" : $(obj).find('option[value="'+ value +'"]').text().trim();
  }
  if( value != last_value.text().trim()){
    last_value.removeClass('hidden');
    $(obj).addClass("value_changed");
  }
  if($(obj).parents('tr').data('returnid') == 0){
    last_value.addClass('hidden');
  }
}

function submitManuallyShipment(formId,tab)
{
    var courier = $('#manually-shipment-courier-'+tab).val();
    var docket  = $('#manually-shipment-docketno-'+tab).val();
    var warehouse  = $('#manually-shipment-warehouse-'+tab).val();
    var error = '';
    if(courier == '') {
       error += "Select courier partner for manually generate shipment <br />";
    }
    if(docket == '') {
       error += "Enter docket number for manually generate shipment ";
    }
    if(warehouse == '') {
       error += "Select warehouse for manually generate shipment ";
    }
    if(error!=''){
       $('#manually-shipment-err-div-'+tab).html(error);
       $('#manually-shipment-err-div-'+tab).css('display','');
       return;
    }else{
      $('#manually-shipment-err-div-'+tab).css('display','none');
      $('#manually_shipment_popup-'+tab).modal('hide');
    }
    $("input[name='manually_shipment_courier_"+tab+"']").val(courier);
    $("input[name='manually_shipment_docket_"+tab+"']").val(docket);
    $("input[name='manually_shipment_warehouse_"+tab+"']").val(warehouse);

    formSubmit(formId,tab);
}

function save_change_status(formId,tab)
{
  var return_action_ids = [];
  $('tbody#products-change-status-'+tab+' .products-change-status').each(function(){
    return_id       = $(this).find('td').find('input:checkbox').val();
    action = $('#return_action_list_'+tab+'_'+return_id).val();
    if(action > 0) {
      return_action_ids.push(parseInt(action));  
    }
  })
  if(jQuery.inArray(manually_generated_reverse_shipment, return_action_ids) !== -1) {  
    $('#manually_shipment_popup-'+tab).modal('show');
  }else{
    formSubmit(formId,tab);
  }
}

function formSubmit(formId,tab){ 
  
  var selected_products = [];
  var error = false;
  $('tbody#products-change-status-'+tab+' .products-change-status').each(function(){
    if( $(this).find('td').find('input:checkbox').prop('checked') ) {
        return_id       = $(this).find('td').find('input:checkbox').val();
        var return_action_id = '#return_action_list_'+tab+'_'+return_id;
        
        var return_quantity = $('#return_action_quantity_'+tab+'_'+return_id).val();  
        if(return_quantity <= 0) {
          alert('item quantity can not be 0'); return false;
        }
        if($(return_action_id).val() == '' || $(return_action_id).val() == '0') {
            $(return_action_id).addClass('error');
            error = true;
        }else{
            $(return_action_id).removeClass('error');
        }
        var shipping_method_id = '#shipping_methods_list_'+tab+'_'+return_id;
        if($(shipping_method_id).val() == '' || $(shipping_method_id).val() == '0') {
            $(shipping_method_id).addClass('error');
            error = true;
        }else{
            $(shipping_method_id).removeClass('error');
        }
       
        var return_action_reason_id = '#action_reason_'+tab+'_'+return_id;
        if($(return_action_reason_id).css('display')!='none') {
           var selected_option_text = $(return_action_reason_id + " option:selected").text();
           if($(return_action_reason_id).val() == '' || $(return_action_reason_id).val() == '0') {
              $(return_action_reason_id).addClass('error');
              error = true;
           }else{
              $(return_action_reason_id).removeClass('error');
              internal_note_id = '#internal_note_'+tab+'_'+return_id;
             if(selected_option_text == 'Others') {
               if($.trim($(internal_note_id).val()) == '') {
                $(internal_note_id).addClass('error');
                  error = true;
                }else{
                  $(internal_note_id).removeClass('error');
                }
             }
           }
        }

        if( !error ) 
        {
            order_id           = $('#hddn_order_id').val();
            order_no           = $('#hddn_order_no').val();
            customer_id        = $('#customer_id').val();

            is_qty_editable    = $(this).find('td').find('input:checkbox').data('is-qty-editable');
            seller_id          = $(this).find('td').find('input:checkbox').data('seller-id');
            seller_invoice_id  = $(this).find('td').find('input:checkbox').data('seller-invoice-id');
            seller_nickname    = encodeURIComponent( $(this).find('td').find('input:checkbox').data('seller-nickname'));
            seller_company     = encodeURIComponent( $(this).find('td').find('input:checkbox').data('seller-company'));
            suborder_id        = $(this).find('td').find('input:checkbox').data('suborder-id');
            is_returnable      = $(this).find('td').find('input:checkbox').data('is-returnable');
            model              = $(this).find('td').find('input:checkbox').data('model');
            return_action_id   = $(return_action_id).val();
            shipping_method_id = $(shipping_method_id).val();
            return_action_reason_id = $(return_action_reason_id).val();
            internal_note      = encodeURIComponent( encodeURI( $('#internal_note_'+tab+'_'+return_id).val() ) );
           
            credit_note_id     = $(this).find('td').find('input:checkbox').data('credit-note-id'); 
            credit_note_no     = $(this).find('td').find('input:checkbox').data('credit-note-no'); 
            credit_note_amount = $(this).find('td').find('input:checkbox').data('credit-note-amount'); 
            helpdesk_ticket_id = $(this).find('td').find('input:checkbox').data('helpdesk-ticket-id');

            manually_shipment_courier = $("input[name='manually_shipment_courier_"+tab+"']").val();
            manually_shipment_docket  = $("input[name='manually_shipment_docket_"+tab+"']").val();
            manually_shipment_warehouse  = $("input[name='manually_shipment_warehouse_"+tab+"']").val();

            if(return_action_id > 0) 
            {

              if(is_returnable === 0 && return_action_id == generat_dn_action_id){
                if(confirm('"'+model+'" is Non-Returnable Product, Do you really want to Generate Debit Note??')){
                  selected_products.push({
                                    order_id,
                                    order_no,
                                    customer_id,
                                    suborder_id,
                                    seller_id,
                                    seller_invoice_id,
                                    seller_nickname,
                                    seller_company,
                                    return_id,
                                    return_quantity,
                                    is_qty_editable,
                                    return_action_id,
                                    shipping_method_id,
                                    return_action_reason_id,
                                    internal_note,
                                    credit_note_id,
                                    credit_note_no,
                                    credit_note_amount,
                                    helpdesk_ticket_id
                                  });
                }
              
              }else if(return_action_id == manually_generated_reverse_shipment){ 
                  selected_products.push({
                                    order_id,
                                    order_no,
                                    customer_id,
                                    suborder_id,
                                    seller_id,
                                    seller_invoice_id,
                                    seller_nickname,
                                    seller_company,
                                    return_id,
                                    return_quantity,
                                    is_qty_editable,
                                    return_action_id,
                                    shipping_method_id,
                                    return_action_reason_id,
                                    internal_note,
                                    credit_note_id,
                                    credit_note_no,
                                    credit_note_amount,
                                    helpdesk_ticket_id,
                                    manually_shipment_courier,
                                    manually_shipment_docket,
                                    manually_shipment_warehouse
                                  }); 
              }else{
                selected_products.push({
                                    order_id,
                                    order_no,
                                    customer_id,
                                    suborder_id,
                                    seller_id,
                                    seller_invoice_id,
                                    seller_nickname,
                                    seller_company,
                                    return_id,
                                    return_quantity,
                                    is_qty_editable,
                                    return_action_id,
                                    shipping_method_id,
                                    return_action_reason_id,
                                    internal_note,
                                    credit_note_id,
                                    credit_note_no,
                                    credit_note_amount,
                                    helpdesk_ticket_id
                                  });
              }
          }
        }
      }
  });

  if( error ) {
    return false;
  }
  if(selected_products.length > 0 ) {
    show_overlay();
    $(".change-status-button").attr("disabled", "disabled");
    query_string = JSON.stringify(selected_products);
    other_values = '&order_id='+order_id+'&order_no='+order_no+'&customer_id='+customer_id+'&suborder_id='+suborder_id;
    //console.log(query_string); return false;
      $.ajax({
        type: 'POST',
        url: 'index.php?route=sale/return/change_status&token='+token,
        dataType:'json',
        data:'params='+query_string+other_values,
        success:function(response){
          if($.trim(response.msg).length != 0) {
            alert($.trim(response.msg));
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
         var string = xhr.responseText.replace(/<b>/g,'');
             string = string.replace(/<\/b>/g,'');
             alert("Error occure during request processing ::\n"+string);
        },
        complete: function(){ 
          window.location.reload(); 
        }
    });
  }
}

$('#save_debit_note').click(function(){
  
  obj = $('#damaged_by_courier_cmpny_form');

  if($(obj).find('.check_checkbox:checked').length < 1){
    alert('Please Select a Product');
    return false
  }
  if($("input[name='select_custom_id']:checked").length <= 0){
    alert('Please Select Custom Party.');
    return false
  }
  if($("input[name='custom_debit_note_ref']").val().length <= 0){
    alert('DebitNote Ref must be entered.');
    return false;
  }
  show_overlay();
  obj.submit()

})

$('.debit_note_remove').click(function(){
  var debit_note_no = $(this).data('debit-no');
  var debit_note_id = $(this).data('debit-id');
  var data_order_id = $(this).data('order-id');
  var dn_type       = $(this).data('dn-type');
  var data_order_no = $('#hddn_order_no').val();
  if(confirm('Are you sure ? ')){
    show_overlay();
    $.ajax({
      type: 'GET',
      url: 'index.php?route=sale/return/cancelDebitNoteDownloadPdf&token='+token+'&order_id='+data_order_id+'&debit_note_no='+debit_note_no+'&debit_note_id='+debit_note_id+'&order_no='+data_order_no+'&dn_type='+dn_type,
      dataType: 'json',
      success:function(data){
        alert(data.status);
      },
      complete:function(){
        window.location.reload();
      }
    });
  }
});

$('.credit_note_remove').click(function(){
  var order_id       = $(this).data('order-id');
  var order_no       = $('#hddn_order_no').val();
  var credit_note_id = $(this).data('credit-id');
  var customer_id    = $('#hddn_customer_id').val();
  var name           = $('#hddn_customer_name').val();
  var email          = $('#hddn_email').val();
  var telephone      = $('#hddn_telephone').val();

  var query_string   = 'token='+token+'&order_id='+order_id+'&cn_id='+credit_note_id;
  query_string      += '&customer_id='+customer_id+'&name='+name;
  query_string      += '&order_no='+order_no+'&email='+email+'&telephone='+telephone;

  if(confirm('Are you sure, You want to cancel CreditNote ? ')){
    show_overlay();
    $.ajax({
      type: 'GET',
      url: 'index.php?route=sale/return/cancelCreditNote&'+query_string,
      dataType: 'json',
      success:function(data){
        alert(data.status);
      },
      complete:function(){
        window.location.reload();
      }
    });
  }
});


$(document).delegate('div.addresses_list','click',function(){
  $('div.addresses_list').removeClass('selected_custom_address');
  $('input[name=\'select_custom_id\']').removeAttr('checked');
  $(this).addClass('selected_custom_address');
  var selected_warehouse_id = $(this).attr('data-address-id');
  $('#select_custom_id-'+selected_custom_id).prop('checked',true);   
});

$(document).delegate('input[name=\'select_custom_id\']','click',function(){
  var selected_warehouse_id = $(this).val();
  $('div.addresses_list').removeClass('selected_warehouse_address');
  $('#warehouse_id_'+selected_warehouse_id).addClass('selected_warehouse_address');
});

$('#save_custom_party').on('click', function() {
  if(validateForm()){
    data = {
          firm_name   : $('#firm_name').val(),
          address1    : $('#address1').val(),
          address2    : $('#address2').val(),
          country_id  : $('#country').val(),
          country     : $("#country option:selected").text(),
          zone_id     : $('#zone').val(),
          state       : $("#zone option:selected").text(),
          city        : $('#city').val(),
          post_code   : $('#postcode').val(),
          gst_num     : $('#gst_num').val()
         };
    $.ajax({
      type:'post',
      url:'index.php?route=sale/return/save_custom_party&token='+token,
      data:data,
      dataType:'json',
      success: function(json) {
        $("#close-btn").click();
      }
    });
    $("#close-btn").click();
    location.reload();
  }
});

function validateForm(){
  var error_flag = 0;
  var msg ='';
  var valid_gst = false;
  if ($('#gst_num').val().length > 0){
    valid_gst = gstin_validatation($('#gst_num').val());
  }
  if ($('#firm_name').val().length <= 0){
    msg = "* Firm Name is Mandatory.";
    error_flag = 1;
  }else if ($('#address1').val().length <= 0){
    msg = "* Address1 is Mandatory.";
    error_flag = 1;
  }else if ($('#country').val() == 0){
    msg = "* Country is Mandatory.";    
    error_flag = 1;
  }else if ($('#zone').val() == 0){
    msg = "* State / Zone is Mandatory.";
    error_flag = 1;
  }else if ($('#city').val().length <= 0){
    msg = "* City is Mandatory.";
    error_flag = 1;
  }else if ($('#postcode').val().length <= 0){
    msg = "* Post Code is Mandatory.";
    error_flag = 1;
  }else if ($('#gst_num').val().length <= 0){
    msg = "* GST Number is Mandatory.";
    error_flag = 1;
  }else if(!valid_gst){
    msg = "* GST Number is not valid.";
    error_flag = 1;
  }

  if(error_flag == 1){
      $('#msg').html(msg);
      $('#msg').addClass('error');
      $('#msg').css('display','block');
      return false;
  }
  return true;
}

$('input[name=\'filter_firm_name\'], input[name=\'filter_city\']').on('keypress',function(e){
  if (e.keyCode == 13) {
    $('#button-search').trigger('click');
  }
});

$('input[name=\'filter_firm_name_forward\'], input[name=\'filter_city_forward\']').on('keypress',function(e){
  if (e.keyCode == 13) {
    $('#button-search-forward').trigger('click');
  }
});

function edit_cn(){
  order_id           = $('#edit_cn_order_id').val(); 
  cn_id              = $('#edit_cn_id').val();
  cod_penality       = $('#cod_penality').val();
  rev_shipping       = $('#rev_shipping').val();
  cn_comment         = $('#cn_comment').val();
  is_cod_failed      = $('#is_cod_failed_cn_edit').val();

  if(cod_penality.length == 0 && rev_shipping.length == 0){
    $('#cn_error').text('* COD Failed Penality OR Reverse Shipping is mendatory.');
    return false;
  }else if(cn_comment.length == 0){
    $('#cn_error').text('* Comment is mendatory.');
    return false;
  }else{
    $('#cn_error').text('');
    var data = $("#edit_cn_form").serialize();

    $.ajax({
      type:'post',
      url:'index.php?route=sale/return/update_credit_note&token='+token,
      data: {
            'data' : data
            },
      dataType:'json',
      complete: function(data) {
        $(".edit_cn_close").click();
        window.location.href = $('#generate_cn_link'+cn_id).attr('href');
        setTimeout(function(){  location.reload(); }, 2000);
      },
      error: function(xhr, ajaxOptions, thrownError) {

        console.log(xhr);
         console.log(ajaxOptions);

        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
    return true;
  }
}

$(document).on("click", ".cn_edit_btn", function () {
  var order_id          = $(this).data('order_id');
  var cn_id             = $(this).data('cn_id');
  var is_cod_failed     = $(this).data('iscod');
  var cod_penality      = $(this).data('penality');
  var shipping          = $(this).data('revship');
  var reversal_shipping = $(this).data('reversal_shipping');
  var other_charges     = $(this).data('other_charges');
  var refund_on_hold    = $(this).data('refund_on_hold');
  
  $(".edit_cn_modal_body #is_cod_failed_cn_edit").val( is_cod_failed );
  $(".edit_cn_modal_body #edit_cn_id").val( cn_id );
  $(".edit_cn_modal_body #edit_cn_order_id").val( order_id );

  if(is_cod_failed == 1){
    $(".edit_cn_modal_body #cod_penality").val( cod_penality );
    $(".edit_cn_modal_body #old_cod_penality").val( cod_penality );

    $('.edit_cn_modal_body .rev_shipping_div').css('display', 'none');
    $('.edit_cn_modal_body .reversal_shipping_div').css('display', 'none');
  }else{
    $(".edit_cn_modal_body #rev_shipping").val( shipping );
    $(".edit_cn_modal_body #old_rev_shipping").val( shipping );
    $(".edit_cn_modal_body #reversal_shipping").val( reversal_shipping );
    $(".edit_cn_modal_body #old_reversal_shipping").val( reversal_shipping );

    $('.edit_cn_modal_body .cod_penality_div').css('display', 'none');
  }

  $(".edit_cn_modal_body #old_other_charges").val( other_charges );
  $(".edit_cn_modal_body #other_charges").val( other_charges );

  //If CN is already mark for refund ON_HOLD
  if(refund_on_hold == 'ON_HOLD'){
    $(".edit_cn_modal_body #refund_on_hold").attr('checked', 'checked');
  }
  $(".edit_cn_modal_body #old_refund_on_hold").val( refund_on_hold );

});

/* Show POPUP to Edit custom DN Ref data */
$('.custom_dn_edit_btn').click(function(){
    var dn_id = $(this).data('dn-id');
    var custom_debit_note_ref = $(this).data('custom-debit-note-ref');
    var custom_dn_order_no = $(this).data('order-no');
    $('#custom_dn_id').val(dn_id);
    $('#custom_dn_order_no').val(custom_dn_order_no);
    $('#edit_custom_debit_note_ref').val(custom_debit_note_ref);
    $('#edit_custom_dn_pop_up').modal('show');
})
/* Saved Custom DN Ref Data*/
$('.submit_custom_debit_note_ref').click(function(){
    var dn_id = $('#custom_dn_id').val();
    var custom_debit_note_ref = $('#edit_custom_debit_note_ref').val();
    var order_no = $('#custom_dn_order_no').val();
    $.ajax({
      type: 'POST',
      url: 'index.php?route=sale/return/editCustomDNRef&order_no='+order_no+'&dn_id='+dn_id+'&custom_debit_note_ref='+encodeURIComponent(custom_debit_note_ref)+'&token='+token,
      async: false,
      success: function() {
         location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    }); // end of ajax
})


$('.return_action_list').change(function() {
  thisObj = $(this);
  var return_action_id = parseInt(thisObj.val(), 10); //Get selected return action
    if(jQuery.isNumeric(this.value) && return_actions[parseInt(return_action_id)] !== 'undefined') {
       var retrun_id = $(this).data('rid'); 
       var tab       = $(this).data('tab'); 
       var quantity  = $(this).data('quantity'); 

       $('#internal_note_'+tab+'_'+retrun_id).text('');

       var is_qty_editable = parseInt(return_actions[parseInt(return_action_id)]['is_qty_editable']);
       $('#change_status_return_'+tab+'_'+retrun_id+'').data('is-qty-editable', is_qty_editable); 
       if(is_qty_editable) {
         $('#action_quantity_'+tab+'_'+retrun_id).css('display','');
         
       }else{
         $('#action_quantity_'+tab+'_'+retrun_id).css('display','none');
         $('#return_action_quantity_'+tab+'_'+retrun_id).val($('#action_quantity_fixed_'+tab+'_'+retrun_id).text());
       }

       $('#return_action_quantity_'+tab+'_'+retrun_id).val(quantity);
    }
    
  //show quantity box for editable


  //Ajax request to get action reasons by passing return_action_id
  $.ajax({
    type:'GET',
    url:'index.php?route=sale/return/getActionReasonByActionId&token='+token,
    data: {
          'return_action_id' : return_action_id
          },
    dataType:'json',
    success: function(jdata){
      //Check if ReturnActionReason is existed for selected ReturnAction
      if ( jdata.length != 0 ) {
        htmlText =  '<option value="0">Select</option>';
        $.each(jdata, function(index,element){
          htmlText += '<option value="'+element.id+'">'+element.action_reason_name+'</option>';
        });

        if(thisObj.attr('id') != "input-return-action"){
          var rid = thisObj.data('rid');

          $('#action_reason_'+tab+'_'+rid).html(htmlText);
          $('#action_reason_'+tab+'_'+rid).css('display', 'block');
        }else{ 
          //If ReturnAction is selected from bulk selection in header
          $('#products-change-status').find('.products-change-status').each(function(index,element) {
            var row = $(element);
            if(row.find('.order_product_id').prop('checked') == true){
              if(row.find('.action_reason').prop('disabled') != true){
                row.find('.action_reason').html(htmlText);
                row.find('.action_reason').css('display', 'block');
              }
            }
          });
        }
      }else{
        //If ReturnActionReason Dropdown is shown
        if(thisObj.attr('id') != "input-return-action"){
          var rid = thisObj.data('rid');
          $('#action_reason_'+tab+'_'+rid).css('display', 'none');
        }else{
          $('#products-change-status').find('.products-change-status').each(function(index,element) {
            var row = $(element);
            if(row.find('.order_product_id').prop('checked') == true){
              if(row.find('.action_reason').prop('disabled') != true){
                row.find('.action_reason').css('display', 'none');
              }
            }
          });
        }
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {
      console.log(xhr);
      console.log(ajaxOptions);
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
});

$('.select_custom_return').change(function() {
  var return_id = $(this).val();
  var package_value    = parseFloat($('#custom_dn_value').val());
  var is_added = false;
  if($(this).prop('checked') == true){
    is_added = true;
  }
  $.ajax({
    type: 'GET',
    url: 'index.php?route=sale/return/getReturnDetailByReturn&return_id='+return_id+'&token='+token,
    async: false,
    dataType: 'json',
    success: function(data) {
      if(data.response == 'success'){

        if(is_added){
          package_value   = parseFloat(package_value) + parseFloat(data.return_value);
        }else{
          package_value   = parseFloat(package_value) - parseFloat(data.return_value);
        }
        package_value = package_value.toFixed(2);
        $('#custom_dn_value').val(package_value);
      }
    },
  });
});
$('.admin_mode_block').change(function(){
  $( "#admin_mode_form").submit();
});
//show replacememt defected image in popup box
$('.show_defected_image').click(function(){ 
  var master_return_id = $(this).data('master-return-id');
  var order_product_id = $(this).data('order-product-id');
  $.ajax({
    type: 'GET',
    url: 'index.php?route=sale/return/getReturnDefectedImages&master_return_id='+master_return_id+'&order_product_id='+order_product_id+'&token='+token,
    async: false,
    dataType: 'json',
    success: function(data) {
        if(data.length > 0 ) {
          var image = '';
          $.each(data,function(index, value){ 
            image += "<img src='"+static_content_url+"return/"+order_product_id+'/'+value+"'>";
          })
          $('#replacement-defected-image').html(image);
          $('#replacement_defected_image_popup').modal('show');
        }else{
          alert('No Image');
        }
    },
  });
})

