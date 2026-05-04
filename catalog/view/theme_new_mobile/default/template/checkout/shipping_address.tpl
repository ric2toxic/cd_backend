<form class="form-horizontal">
  <input name="address_id" type="hidden" value="" class="form-control">
  <input name="shipping_address" type="hidden" value="existing" class="form-control">

  <?php if ($addresses) { ?>
  <div class="radio col-md-9 use_shipping_address col-xs-6">
     <!-- <input type="radio" name="shipping_address" value="existing" checked="checked" />-->
      <div class="shipping_address existing" data-type="existing" >
      <span class="use_saved_title"><?php echo $text_address_existing; ?></span> <span class="smallhead"><?php echo $text_will_deliver; ?></span> </div>
  </div>
  <div class="radio col-md-3 new_button_address_link  new_address col-xs-6">
      <!--<input type="radio" name="shipping_address" value="new" />-->
      <div class="shipping_address new button_address_link" data-type="new" >
        <?php echo $text_address_new; ?>
      </div>
  </div>
  <div class="clearfix"></div>
  <div id="shipping-existing">
    <?php /* ?>
    <select name="address_id" class="form-control">
      <?php foreach ($addresses as $address) { ?>
      <?php if ($address['address_id'] == $address_id) { ?>
      <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
      <?php } else { ?>
      <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
      <?php } ?>
      <?php } ?>
    </select>
    <?php */ ?>

    <div class="row">
      <?php
      $first_address_id = '';
      $i = 1;
      foreach ($addresses as $address) { ?>
      <?php
           if($i == 1){
             $first_address_id = $address['address_id'];
           }
            $i++;

            if($address['company'] != ''){
              $name = $address['company'];
            }else{

              $name = $address['firstname']." ". $address['lastname'];
            }
      ?>
      <?php
             $cssclass = '';
            if ($address['address_id'] == $first_address_id) {
                $cssclass = 'selected_address';
            }
      ?>
      <?php if(count($addresses)== 1){ $addClass = "selected_address"; $address_value = $address['address_id']; }else{ $addClass = ""; $address_value ="";}?>
      <div class="col-md-3 col-xs-6 shipment_address<?php echo $address['address_id']; ?>" >
        <div class="address_block shipment_address <?php echo $addClass; ?> <?php //echo $cssclass; ?>" data-address-id="<?php echo $address['address_id']; ?>">
          <h3 class=""><?php echo $name; ?></h3>
          <div class="address_data">
            <?php echo $address['address_1']; ?>, <?php echo $address['address_2']; ?> <br /><?php echo $address['city']; ?> - <?php echo $address['postcode']; ?>, <?php echo $address['zone']; ?>, <br /><?php echo $address['country']; ?>


          </div>
          <div class="shipment_action_links">
            <div class="shipment_edit_link updateAddresscontinue">
              <a class="shipment_iframe" href="index.php?route=account/address/edit&address_id=<?php echo $address['address_id'];?>&popup=true">
                <i class="fa fa-pencil"></i>
              </a>
            </div>
            <?php  if(count($addresses)>1){ ?>
              <div class="shipment_delete_link" data-address-id="<?php echo $address['address_id'];?>">
                <a href="javascript:void(0);" class="delete_shipping_address" delete_address_id="<?php echo $address['address_id'];?>">
                  <i class="fa fa-close"></i>
                </a>
              </div>
            <?php } ?>
          </div>
          <!--<div class="continue" data-index="0"><span>SELECT</span></div>-->


        </div>
      </div>
      <?php } ?>
    </div>
  </div>
  <div class="buttons">
	   <?php if($popup == false){?>
    <div class="pull-left back_button_box">
      <div class="continue back_shipment_collapse"><span><?php echo $button_back; ?></span></div>
    </div>
     <?php } ?>
    <div class="pull-right continue_button_box">
      <div class="continue shipment_continue_collapse" data-address-id="<?php echo $address_value;?>"><span><?php echo $button_continue; ?></span></div>
    </div>
  </div>
  <?php } ?>
  <br />
  <div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-firstname"><?php echo $entry_firstname; ?></label>
      <div class="col-sm-10">
        <input type="text" name="name" value="" placeholder="<?php echo $entry_firstname; ?>" id="input-shipping-firstname" class="form-control" />
      </div>
    </div>
    <?php /* ?>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-lastname"><?php echo $entry_lastname; ?></label>
      <div class="col-sm-10">
        <input type="text" name="lastname" value="" placeholder="<?php echo $entry_lastname; ?>" id="input-shipping-lastname" class="form-control" />
      </div>
    </div>
    <?php */ ?>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="input-shipping-company"><?php echo $entry_company; ?></label>
      <div class="col-sm-10">
        <input type="text" name="company" value="" placeholder="<?php echo $entry_company; ?>" id="input-shipping-company" class="form-control" />
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-address-1"><?php echo $entry_address_1; ?></label>
      <div class="col-sm-10">
        <input type="text" name="address_1" value="" placeholder="<?php echo $entry_address_1; ?>" id="input-shipping-address-1" class="form-control" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="input-shipping-address-2"><?php echo $entry_address_2; ?></label>
      <div class="col-sm-10">
        <input type="text" name="address_2" value="" placeholder="<?php echo $entry_address_2; ?>" id="input-shipping-address-2" class="form-control" />
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-city"><?php echo $entry_city; ?></label>
      <div class="col-sm-10">
        <input type="text" name="city" value="" placeholder="<?php echo $entry_city; ?>" id="input-shipping-city" class="form-control" />
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-postcode"><?php echo $entry_postcode; ?></label>
      <div class="col-sm-10">
        <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-shipping-postcode" class="form-control" />
      </div>
    </div>
    <?php if(isset($show_country) && $show_country == "1"){?>    
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-country"><?php echo $entry_country; ?></label>
      <div class="col-sm-10">
        <select name="country_id" id="input-shipping-country" class="form-control">
          <option value=""><?php echo $text_select; ?></option>
          <?php foreach ($countries as $country) { ?>
          <?php if ($country['country_id'] == $country_id) { ?>
          <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
          <?php } else { ?>
          <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
          <?php } ?>
          <?php } ?>
        </select>
      </div>
    </div>
    <?php } ?>
    <input type="text" value="99" name="country_id" id="input-shipping-country" style="visibility: hidden;" />
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-shipping-zone"><?php echo $entry_zone; ?></label>
      <div class="col-sm-10">
        <select name="zone_id" id="input-shipping-zone" class="form-control">
        </select>
      <!--  <select class="form-control" id="input-payment-zone" name="zone_id"><option value=""> --- Please Select --- </option><option value="1475">Andaman and Nicobar Islands</option><option value="1476">Andhra Pradesh</option><option value="1477">Arunachal Pradesh</option><option value="1478">Assam</option><option value="1479">Bihar</option><option value="1480">Chandigarh</option><option value="1481">Dadra and Nagar Haveli</option><option value="1482">Daman and Diu</option><option value="1483">Delhi</option><option value="1484">Goa</option><option value="1485">Gujarat</option><option value="1486">Haryana</option><option value="1487">Himachal Pradesh</option><option value="1488">Jammu and Kashmir</option><option value="1489">Karnataka</option><option value="1490">Kerala</option><option value="1491">Lakshadweep Islands</option><option value="1492">Madhya Pradesh</option><option value="1493">Maharashtra</option><option value="1494">Manipur</option><option value="1495">Meghalaya</option><option value="1496">Mizoram</option><option value="1497">Nagaland</option><option value="1498">Orissa</option><option value="1499">Pondicherry</option><option value="1500">Punjab</option><option value="1501">Rajasthan</option><option value="1502">Sikkim</option><option value="1503">Tamil Nadu</option><option value="1504">Tripura</option><option value="1505">Uttar Pradesh</option><option value="1506">West Bengal</option></select>
        -->
      </div>
    </div>
   
    <div class="buttons clearfix">
      <div class="text-center">
        <input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-address" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary button-shipping-address" />
      </div>
    </div>
  </div>

</form>
<script type="text/javascript"><!--

  $('input[name=\'address_id\']').val('<?php echo $first_address_id; ?>');

  $('div.shipping_address').on('click', function() {
    if ($(this).attr('data-type') == 'new') {
      $('#shipping-existing').hide();
      $('.shipment_continue_collapse').hide();
      $('#shipping-new').show('slow');
      $('div.new').removeClass('button_address_link');
      $('div.new_address').removeClass('new_button_address_link col-md-3 col-xs-6');
      $('div.new_address').addClass('col-xs-12');
      $('div.use_shipping_address').removeClass('col-md-9');
      $('div.existing').addClass('button_address_link');
      $('div.existing').text('Back');
      $('input[name=\'shipping_address\']').val('new');
      $('.back_shipment_collapse').hide();
    } else {
      $('#shipping-existing').show('slow');
      $('.shipment_continue_collapse').show();
      $('#shipping-new').hide();
      $('div.existing').removeClass('button_address_link');
      $('div.existing').html('<span class="use_saved_title set_address_title">Use Saved Address</span> <span class="smallhead"> (We will deliver your order here)</span>');
      $('div.new').addClass('button_address_link');
      $('div.new_address').addClass('new_button_address_link col-md-3 col-xs-6');
      $('div.new_address').removeClass('col-xs-12');
      $('div.use_shipping_address').addClass('col-md-9');
      $('input[name=\'shipping_address\']').val('existing');
      $('.back_shipment_collapse').show();
    }
  });

 /* $('div.shipment_address').on('click', function() {

    //$('input[name=\'address_id\']').val($(this).attr('data-address-id'));
    //$('div.shipment_address').removeClass('selected_address');
    //$(this).addClass('selected_address');
    //$('input#button-shipping-address').trigger('click');
    //alert($(this).attr('data-id'));
  });*/
  $('div.shipment_address').on('click', function() {
    $('div.shipment_continue_collapse').attr('data-address-id',$(this).attr('data-address-id'));
    //$('input[name=\'address_id\']').val($(this).attr('data-address-id'));
    $('div.shipment_address').removeClass('selected_address');
    $(this).addClass('selected_address');
    //$('input#button-payment-address').trigger('click');
    //alert($(this).attr('data-id'));
  });
  $('div.shipment_continue_collapse').on('click', function() {
    if($(this).attr('data-address-id')!=''){
      $('input[name=\'address_id\']').val($(this).attr('data-address-id'));
      //$('div.shipment_continue_collapse').removeClass('selected_address');
      // $(this).addClass('selected_address');
      $('input#button-shipping-address').trigger('click');
      //alert($(this).attr('data-id'));

//      $('.checkout_payment_method').show();
//      $('.checkout_payment_address').hide();
//      $('.new_shipping_address').hide();

    }else{
      alert('Please select your shipping address!');
    }
  });

  /*$('div.shipment_delete_link').on('click',function(){

    address_value = $(this).attr('data-address-id');
    $.ajax({
      type: 'POST',
      url: 'index.php?route=checkout/checkout/delete_new_address',
      data: {address_value},
      success: function(json) {
        if(json=='success'){
          $('.shipment_address'+address_value).remove();
          location.reload();
        }
      },
    });
  });*/

$('input[name=\'shipping_address\']').on('change', function() {
	if (this.value == 'new') {
		$('#shipping-existing').hide();
		$('#shipping-new').show();
	} else {
		$('#shipping-existing').show();
		$('#shipping-new').hide();
	}
});
//--></script>
<script type="text/javascript"><!--
$('#collapse-shipping-address .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#collapse-shipping-address .form-group').length) {
		$('#collapse-shipping-address .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('#collapse-shipping-address .form-group').length) {
		$('#collapse-shipping-address .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('#collapse-shipping-address .form-group').length) {
		$('#collapse-shipping-address .form-group:first').before(this);
	}
});
//--></script>
<script type="text/javascript"><!--
$('#collapse-shipping-address button[id^=\'button-shipping-custom-field\']').on('click', function() {
	var node = this;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$(node).parent().find('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input[name^=\'zone_id\']').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input[name^=\'zone_id\']').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.time').datetimepicker({
	pickDate: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
//--></script>
<script type="text/javascript"><!--
$('#collapse-shipping-address [name=\'country_id\']').on('change', function() {
  //country_id = $('#collapse-shipping-address input[name=\'country_id\']').attr('value');
  country_id = this.value;
  $('#collapse-shipping-address input[name=\'country_id\']').attr('value',country_id);
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + country_id,
		dataType: 'json',
		beforeSend: function() {
			$('#collapse-shipping-address select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#collapse-shipping-address input[name=\'postcode\']').parent().parent().addClass('required');
			} else {
				$('#collapse-shipping-address input[name=\'postcode\']').parent().parent().removeClass('required');
			}

			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}

			$('#collapse-shipping-address select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//$('#collapse-shipping-address select[name=\'country_id\']').trigger('change');
  $('#collapse-shipping-address input[name=\'country_id\']').trigger('change');
//--></script>

<!-- // it is used for edit shipping address in popup -->
<script>
  $(".shipment_iframe").fancybox({
    'hideOnContentClick': true,
    maxWidth: 980,
    width:'100%',
    padding:0,
    type:"iframe",
    iframe: {
      preload: false // fixes issue with iframe and IE
    },
    afterClose: function() {
      window.location.reload();
    }

  });
</script>

<script>
  $(document).ready(function(){
    $('.back_shipment_collapse').click(function(){
      $('.account_billing_detail').hide();
      $('.new_shipping_address').hide();
      $('.checkout_payment_address').show();
      $('.checkout_payment_method').hide();
      $('.checkout_confirm').hide();

      $("html, body").animate({scrollTop: 0});
    });

    // delete address
    $('.delete_shipping_address').click(function(){
      if(confirm('Are you sure ?')){
        location = 'index.php?route=account/address/delete&address_id='+$(this).attr('delete_address_id')+'&checkout_delete=true';
      }
    });
  });
</script>
