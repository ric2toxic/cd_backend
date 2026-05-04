<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="edit_profile_form">
        <fieldset>
          <legend><?php echo $text_your_details; ?></legend>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?> </label>
            <div class="col-sm-10">
              <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
              <?php if ($error_firstname) { ?>
              <div class="text-danger"><?php echo $error_firstname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
              <?php if ($error_lastname) { ?>
              <div class="text-danger"><?php echo $error_lastname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
              <?php if(!empty($email)) { ?>
              <label><?php echo $email; ?></label>
              &nbsp; &nbsp;
               <a href="javascript:;" data-toggle="modal" data-target="#update_number_popup">Update</a>
              <?php } else { ?>
              <input type="email" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" <?php if(isset($email_exist)) { echo "disabled"; } ?> />
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
            <div class="col-sm-10">
              <?php if(!empty($telephone) && $international_store == 0) { ?>
              <label><?php echo $telephone; ?></label>
              &nbsp; &nbsp;
               <a href="javascript:;" data-toggle="modal" data-target="#update_number_popup">Update</a>
              <?php } else { ?>
              <input type="tel" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
              <?php if ($error_telephone) { ?>
              <div class="text-danger"><?php echo $error_telephone; ?></div>
              <?php } } ?>
            </div>
          </div>
          
          <?php if($international_store == 0) { ?>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_gst_number; ?></label>
            <div class="col-sm-9">
              <input type="tel" name="gst_number" value="<?php echo $gst_number; ?>" placeholder="<?php echo $entry_gst_number; ?>" id="input-gst_number" class="form-control" onkeyup="gst_number_valid();" <?php if(isset($gst_number_exist)) { echo "readonly"; } ?> />
              <input type="hidden" name="old_gst_number" value="<?php echo $gst_number; ?>" />
              <span id="gst_error">
                  <?php if ($error_gst_number) { ?>
                  <div class="text-danger"><?php echo $error_gst_number; ?></div>
                  <?php } ?>
              </span>
            </div>
          </div>
          <?php } ?>

        </fieldset>
        <div class="buttons clearfix">
          <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
          <div class="pull-right">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary" />
          </div>
        </div>
      </form>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

    <!-- mobile(Verify) popup -->
  <div class="modal fade add_new_address" id="update_number_popup" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header address_popup_head">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" id="send_otp_title">Please enter your mobile number or Email address</h4>
             </div>
            <div class="modal-body">
            <div class="success_msg account_mobile_success" style="display:none;"></div>
             <div class="danger_msg account_mobile_error" style="display:none;"></div>  
             <div class="otp_model">
             <form name="update_number_form" id="update_number_form">
              <div class="mobile_details_panel" id="edit_telephone" style="display:none;">
                <span class="flag_area"><i class="country-flag flagstrap-icon flagstrap-"></i></span>
                 <div class="account_mobile_nmr"> </div>
                 &nbsp;
                   <a href="javascript:;" onclick="edit_register_no();"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</a>
               </div>

                <div class="mobile_details_panel" id="save_account_telephone">
                     <div class="flagstrap flag_box" id="select_update_country" data-input-id="country_code" data-input-name="country_code" data-selected-country="IN"></div>
                    <input type="text" name="update_telephone" class="mobile_input" id="update_telephone" autocomplete="off" placeholder="Mobile Number Or Email">
                     <input type="submit" name="send_otp" value="Continue" class="btn deliver_btn pull-right" id="send_otp" disabled="">
                </div>
                  </form>

                  <div class="clearfix"></div>
                 <form name="verify_otp_form" id="verify_otp_form">
                <div id="collapseverify" class="panel-collapse collapse">
                <input type="text" name="otp" class="password_box" maxlength="6" placeholder="OTP">
                <input type="submit" name="verify_otp" value="Verify" class="btn deliver_btn pull-right" id="verify_otp">

                 <div class="clearfix"></div>
                 <div class="otp_agin"><a href="javascript:;" class="send_otp_agin">Didn't get OTP?</a></div>
                 <div class="clearfix"></div>
                 </div>
                 <input type="hidden" name="reg_telephone" label="" value="">
                 <input type="hidden" name="country_code" label="" value="">
                 <input type="hidden" name="country_iso_code" label="" value="">
              </form>
              </div>
            </div>
          </div>
        </div>
   </div>


<script type="text/javascript">
function gst_number_valid()
{
  var gst_number = $("#input-gst_number").val();
   gst_number = gst_number.toUpperCase();
   $("#input-gst_number").val(gst_number);

}

$('#input-gst_number').on('blur', function() {
    var gst_number = $("#input-gst_number").val();
    var result_gst_number = validateGSTNumber(gst_number);
    var entered_checksum_character = gst_number.substr(-1);
    if(result_gst_number == false || (entered_checksum_character != result_gst_number)) {
        $("#gst_error").html('<div class="text-danger">Invalid GST Number !!!</div>');
        $("#edit_profile_form input[type=submit]").val('Continue').prop("disabled", true);
        return false;
    } else {
        $("#gst_error").html('');
        $("#edit_profile_form input[type=submit]").val('Continue').prop("disabled", false);
    }
});

function validateGSTNumber(gst_number) {
    var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/;
    if (reggstin.test(gst_number) == false) {
        return false;
    }
    
    var factor_even = 1;
    var factor_odd = 2;
    var sum = 0;
    var gst_number_array = gst_number.split("");
    var checksum_weight_array = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split("");
    var checksum_mod = checksum_weight_array.length;
    var factor = factor_even;
    
    if(gst_number_array.length == 15) {
        gst_number_array.pop();
    }
    
    for(index = 0; index < gst_number_array.length; ++index) {
        var current_letter_weight = checksum_weight_array.indexOf(gst_number_array[index]);
        var current_checksum_digit = 0;
        if(current_letter_weight != -1) {
            current_checksum_digit = current_letter_weight * factor;
            current_checksum_digit = parseInt((current_checksum_digit / checksum_mod) + (current_checksum_digit % checksum_mod));
            sum += current_checksum_digit;
        }
        factor = (factor == factor_even) ? factor_odd : factor_even;
    }
    
    var calculated_checksum_weight = (checksum_mod - (sum % checksum_mod)) % checksum_mod;
    var calculated_checksum_letter = (checksum_weight_array[calculated_checksum_weight])
                                    ? checksum_weight_array[calculated_checksum_weight] 
                                    : false;
    return calculated_checksum_letter;
}
<!--
// Sort the custom fields
$('.form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('.form-group').length) {
		$('.form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('.form-group').length) {
		$('.form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('.form-group').length) {
		$('.form-group:first').before(this);
	}
});
//--></script>
<script type="text/javascript"><!--
$('button[id^=\'button-custom-field\']').on('click', function() {
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
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').attr('value', json['code']);
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

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});
//--></script>
<?php echo $footer; ?>

<script type="text/javascript">
  <?php /* if (isset($international_store)) { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } else { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } */ ?>


 var ajax = null;
 var INTERNATIONAL_STORE = '<?php echo $international_store; ?>';
  if(INTERNATIONAL_STORE == 1)
   {
    cc = $.parseJSON('<?php echo json_encode($this->mobile_country_code["CO"]); ?>');
    $('#select_update_country').attr('data-selected-country','CA');
   }
   else
    {
     cc = $.parseJSON('<?php echo json_encode($this->mobile_country_code["IN"]); ?>');
     $('#select_update_country').attr('data-selected-country','IN');
    }

    $('#select_update_country').flagStrap({
        countries:cc
    });


    $("#update_number_form").submit(function(e) {
         e.preventDefault();
         $("#update_number_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl         = './api/account/update_number_otp';
         var country_code_text = $("#country_code option:selected" ).text();
         var country_code_val  = $("#country_code" ).val();
         var form_data         = $("#update_number_form").serialize();
             form_data         = form_data.replace("country_code="+country_code_val, "country_code="+country_code_text+"&country_iso_code="+country_code_val);

        ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response) {
                  var data = response.data;
                  if(data['error'])
                    {
                      $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                      $('.account_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                      $('.account_mobile_error').show();
                      $('.account_mobile_success').hide();
                      $("#collapseverify").css('visibility','hidden').hide();
                    }
                  else
                    {
                      $(".account_mobile_nmr").html(data['country_code']+' '+data['reg_telephone']);
                      $("#verify_otp_form input[name=reg_telephone]").val(data['reg_telephone']);
                      $("#verify_otp_form input[name=country_code]").val(data['country_code']);
                      $("#verify_otp_form input[name=country_iso_code]").val(data['country_iso_code']);
                      if(data['country_iso_code'] != '')
                           {
                             $(".country-flag").removeClass().addClass('country-flag flagstrap-icon flagstrap-'+data['country_iso_code'].toLowerCase());
                             $(".flag_area").show();
                           }
                           else
                           {
                              $(".flag_area").hide();
                           }
                      $('.account_mobile_error').hide();
                      $('.account_mobile_success').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                      $('.account_mobile_success').show();
                      $("#save_account_telephone, #send_otp_title").hide();
                      
                      $("#edit_telephone").show();
                      $("#collapseverify").css('visibility','visible').show();
                    }  
                },
               complete: function(data){
                var ajax = null;
               }
         });
          e.stopImmediatePropagation();
          return false;
  });


       $("#verify_otp_form").submit(function(e) {
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = './api/account/verify_otp';
         var form_data = $("#verify_otp_form").serialize();
       ajax =  $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response)
                {
                    var data = response.data;
                    if(data['error'])
                    {
                      $("#verify_otp_form :input[name=otp]").val('');
                      $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
                      $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
                      $('.account_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                      $('.account_mobile_error').show();
                      $('.account_mobile_success').hide();
                    }
                    else
                    {
                      document.location.reload();  
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
         e.stopImmediatePropagation();
         return false;
       });


 $(document).delegate('.send_otp_agin', 'click', function(e){
           $('.account_mobile_success, .account_mobile_error').hide();
           $("#verify_otp_form input[name=otp]").val('');
           $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px');
           e.preventDefault();
           $('#update_number_form').submit();
 });

function edit_register_no()
    {
        $("#edit_telephone").hide();
        $("#save_account_telephone, #send_otp_title").show();
        $('.account_mobile_success, .account_mobile_error').hide(500);
        $("#verify_otp_form :input[name=otp]").val('');
        $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
        $("#collapseverify").hide();
        check_reg_telephone();
    }

function check_reg_telephone()
  {
    var mobile       = $('#update_telephone').val();
    var country_code = $('#country_code').val();
    var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    
    $(".flagstrap").hide();

    if(INTERNATIONAL_STORE == 1)
      {   
          if (mobile != '' && /\D/g.test(mobile))
                 {
                   $('#reg_telephone').prev("label").html("Email");
                   $(".flagstrap").hide();
                 }

                 if (email_pattern.test(mobile))
                 {
                   $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                 }
                 else
                 {
                   $("#update_number_form input[type=submit]").val('continue').prop("disabled", true);
                 }

        }
        else
        {
                if(country_code == 'IN')
                {
                   var mobile_pattern = new RegExp(/^\d{10}$/);
                }
                else
                {
                   var mobile_pattern = new RegExp(/^\d{7,10}$/);
                } 

                if (mobile != '' && !/\D/g.test(mobile))
                 {
                   $('#reg_telephone').prev("label").html("Mobile");
                   $(".flagstrap").show();
                 }

                if (mobile != '' && /\D/g.test(mobile))
                 {
                   $('#reg_telephone').prev("label").html("Email");
                   $(".flagstrap").hide();
                 }

                if (email_pattern.test(mobile))
                 {
                   $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                 }
                else if (mobile_pattern.test(mobile))
                 {
                   if (mobile.charAt(0) != 0 && country_code == 'IN')
                    {
                       $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                    }
                   if (country_code != 'IN')
                    {
                      $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                    }

                  }
                 else
                  {
                     $("#update_number_form input[type=submit]").val('continue').prop("disabled", true);
                  }
            }
    }

$('#update_telephone').keyup(function(e)
  {
    check_reg_telephone();
  });

$('#update_telephone').change(function(e)
 {
    check_reg_telephone();
 });  

</script>