<!DOCTYPE html  PUBLIC>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $title; ?></title>
    <base href="<?php echo $base; ?>" />
    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if ($keywords) { ?>
    <meta name="keywords" content= "<?php echo $keywords; ?>" />
    <?php } ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>
    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo $co_store.$request_uri;?>" />
    <link rel="alternate" hreflang="en-in" href="<?php echo $in_store.$request_uri;?>" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com"/>
    <link rel="dns-prefetch" href="//www.googletagmanager.com"/>

    <link href="//fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
    <link href="<?php echo LOCAL_CDN_URL_SSL.COMMON_CSS; ?>" rel="stylesheet">
<style type="text/css">
.affix {
     top: 0;
     width: 100%;
 }
.affix + .container-fluid {
     padding-top: 70px;
 } 
.bg_gray {background: #233c98;}
.navbar {min-height: 50px;}
.slider_navgation {display: block;float: left;width: 250px;}
.carousel-inner {overflow: hidden;position: relative;width: 100%;}
.b2bVideo {display: inline-block;float: right;padding: 7px 7px 20px;}
.offerBox {display: block;min-height: 130px;width: 180px;}
.home-product-list {width: 16.6%;}
.c-product-card__gallery{width: 42px;}
.steps .procedure {width: 77%;}
.steps .procedure .procedure_img_box {height: 90px;width: 90px;}
.tab_list li a h4 {font-size: 1.2em;}
.stats-bar ul li a { font-size: 24px;padding: 25px 30px;}
.cart_page {margin: 6% 0;}
.cart_statement_pree_load{width: 25%;min-height: 300px;}
.cart_full_box .cart_box .cart_main_title{border-radius: 0; padding: 17px 8px; margin-top:7px;}
.panel-body .cart_table .cart_table_height{ min-height: 400px; }
.li_position{position: absolute; list-style: none;}
.dropdown-toggle .menu_bar {left: -1px;min-width: 254px;top: 57px; padding: 0px;}
.sticky_menu_top_btn .menu_bar {left: -1px;min-width: 254px;top: 57px; height: 70px; }
.header_navigation li .nav_icon_bar{display: none;left: 0px;position: relative;list-style: none; height: 50px; width: 50px; padding: 10px;
    padding-bottom: 0px;}
.header_navigation li .nav_icon_bar:hover{background-color: transparent;}
.header_navigation li .nav_icon_bar .icon-bar{background-color: #17319f;border-radius: 1px;display: block;height: 4px; width: 28px;}
.header_navigation li .nav_icon_bar .icon-bar + .icon-bar {margin-top: 4px;}
.sticky_menu_top_btn {left: 23px;width: 50px;padding:0px; position: fixed; top: 13px; z-index: 999;}
.inner_page_menu_bar{ position: absolute; top: 33px; z-index: 999;display: block!important;left: 23px;width: 50px;padding: 0px; }
.inner_page_menu_bar .nav_icon_bar{display: block!important;}
  .suborder_table table tr td {
    padding: 3px !important;
  }
</style>

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MBB945');</script>
    <!-- End Google Tag Manager -->
<?php } else { ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TRP9H5');</script>
<!-- End Google Tag Manager -->
<?php } ?>

</head>

<body class="loaded">

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MBB945"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php } else { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TRP9H5"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript)    -->
<?php } ?>


<div id="myProgress">
  <div id="myBar"></div>
</div>
<section id="header_box"></section>


<div class="width_fix account_page_bg">
<div class="clearfix"></div>
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
    <?php echo $column_right; ?>
    <div id="content" class="<?php echo $class; ?> box_shadow_none">
      <ul class="breadcrumb_new">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
      </ul>
    <?php echo $content_top; ?>
      <h1 class="account_title"><?php echo $heading_title; ?></h1>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="edit_profile_form">
        <fieldset>
          <legend><?php echo $text_your_details; ?></legend>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?> </label>
            <div class="col-sm-9">
              <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
              <?php if ($error_firstname) { ?>
              <div class="text-danger"><?php echo $error_firstname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
            <div class="col-sm-9">
              <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
              <?php if ($error_lastname) { ?>
              <div class="text-danger"><?php echo $error_lastname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-9">
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
            <div class="col-sm-9">
              <?php if(!empty($telephone) && $international_store == 0) { ?>
              <label><?php echo $telephone; ?></label> &nbsp; &nbsp;
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
        <div class="col-sm-2"></div>
        <div class="col-sm-9 form_buttons_width">
          <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
          <div class="pull-right">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-continue" />
          </div>
        </div>
        </div>
      </form>
      <?php echo $content_bottom; ?></div>
    </div>
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
                 <input type="hidden" name="customer_id" label="" value="">
                 <input type="hidden" name="customer_access_token" label="" value="">
              </form>
              </div>
            </div>
          </div>
        </div>
   </div>


<section id="footer_box"></section>
<section id="login_box"></section>
<section id="opt_box"></section>
<section id="register_box"></section>
<section id="success_box"></section>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>

<!-- start chatbox -->
<div class="custom_chat_box">
    <div class="chat_box_heading_div">
        <p class="chat_box_heading">Online <i class="fa fa-angle-up"></i></p>
    </div> 
    <div class="chat_box_content">
        <p><?php echo $text_chatbox_message; ?></p>
        <ul class="list-unstyled1 components">
            <li class="dropdown">
                <a href="#whatsapp" data-toggle="collapse" aria-expanded="false" class="dropdown-link dropdown-toggle"><i class="fa fa-whatsapp"></i> <div class="dropdown_arrow"><i class="fa fa-angle-right"></i></div><?php echo $text_whatsApp ?></a>
                <ul class="collapse list-unstyled" id="whatsapp">
                    <li>
                        <div class="whatsaap_web_title">
                            <a href="https://web.whatsapp.com/send?phone=<?php echo $text_whatsappw_no ?>&text=<?php echo $text_whatsappw_no_msg_text ?>" target="_blank"><i class="fa fa-comments-o"></i> <?php echo $text_chat_on_whatsappw_web ?></a>
                        </div>
                        <p class="or_outer"><span class="or">or</span></p>
                        <p class="whatsapp_on">Whatsapp on <?php echo $text_whatsappw_no ?></p>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#callus" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle dropdown-link"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $text_callus ?> <div class="dropdown_arrow"><i class="fa fa-angle-right"></i></div></a>
                <ul class="collapse list-unstyled" id="callus">
                    <li><p class="call_us">Call us on <?php echo $text_callus_no ?></p></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#call_back_request" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle dropdown-link"><i class="fa fa-user"></i> <?php echo $text_call_back_request ?> <div class="dropdown_arrow"><i class="fa fa-angle-right"></i></div></a>
                <ul class="collapse list-unstyled" id="call_back_request">
                    <li>
                        <p><?php echo $text_call_back_request_messgae; ?></p>
                        <input class="callback_request_input" type="text" maxlength="10" onkeypress="return isNumberKey(event)">
                        <input class="callback_request_button" type="submit" value="Submit">
                        <div class="loading"></div>
                        <div class="error_msg"></div>
                        <div class="success_mail">Thank you! We shall call you shortly.</div>
                    </li>
                </ul>
            </li>

            <li class="chat_here">
                <a href="javascript:void(Tawk_API.toggle())"><i class="fa fa-comments"></i> <?php echo $text_chat_here ?></a>
            </li>
            
        </ul>
    </div> 
</div>
<!-- End chatbox -->


<!-- start chatbox css -->
<style>
    .custom_chat_box{
        width: 300px;
        position: fixed;
        z-index: 99;
        right: 12px;
        bottom: 0;
        font-size: 15px;
    }
    .chat_box_heading_div{
        background-color: #17319f;
        color: #fff;
        border-radius: 10px 9px 0 0;
        padding: 6px 10px;
        cursor: pointer;
    }
    .chat_box_heading_div i {
        float: right;
        font-weight: bold;
        margin: 4px;
    }
    .chat_box_heading{
        padding: 0px;
        font-size: 22px;
        margin: 0;
    }
    .call_us{
        margin: 0;
    }
    .chat_box_content{
        padding: 10px 10px 0 10px;
        border: 1px solid #ddd;
        display: none;
        background: #fff;
        /*height: 230px;
        overflow: scroll;*/
    }
    ul.list-unstyled1.components {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    ul.list-unstyled1 li.inactive{
        background: none;
    }
    ul.list-unstyled1 li.active{
        background: #edf2fa;
        color: #636363;
        border-radius: 7px 7px 0 0;
        
    }
    
    ul.list-unstyled1 li.active a.dropdown-toggle:hover{
        border-radius: 7px 7px 0 0;
        
    }
    ul.list-unstyled1 li.active a.dropdown-toggle{
        color: #636363;
    }
    
    .custom_chat_box ul li{
        border-bottom: 1px solid #ccc;
    }
    li.chat_here{
        border:none !important;
    }
    .custom_chat_box ul li a{
        padding: 5px 5px;
        display: block;
    }
    .custom_chat_box ul li a.dropdown-toggle:hover{
        background: #edf2fa;
        color: #636363;
    }
    .custom_chat_box ul li i{
        font-size: 20px;
        margin-right: 5px;
    }
    .custom_chat_box .dropdown_arrow{
        float: right;
    }
    .dropdown_arrow i {
        font-size: 15px !important;
    }
    .custom_chat_box ul li ul{
        padding: 5px 15px;
        background : #f9f9f9;
    }
    .custom_chat_box ul li ul li{
        border-bottom: 0;
    }
    .fa-whatsapp{
        color: #25D366;
    }
    .callback_request_input{
        height: 30px;
    }
    .callback_request_button{
        height: 30px;
        color: #fff;
        background-color: #17319f;
        margin-left: 5px;
        padding: 0 15px;
        border: none;
    }
    .error_msg {
        color: #FF0000;
    }
    .success_mail {
        color: #228B22;
        display: none;
    }
    .whatsaap_web_title a{
        padding: 5px 10px !important;
        border-radius: 7px;
        border: 1px solid #636363;
        color: #636363;
        display: table !important;
        margin: 0 auto !important;
    }
    .whatsaap_web_title a:hover{
        color: #636363;
    }
    .whatsaap_web_title p{
        margin: 0;
    }
    
    .or_outer{
        text-align: center;
        margin: 10px 0 !important;
        padding: 0;
    }
    .whatsapp_on{
        margin: 0;
        text-align: center;
    }
    .or{
        background: #f0f0f0;
        padding: 1px 4px;
        border-radius: 9px 9px 9px 9px;
    }
    .custom_chat_box input[type=number]::-webkit-inner-spin-button, 
    .custom_chat_box input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
    
</style>
<!-- End chatbox css -->

<!-- jQuery -->
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_REACT_JS; ?>"></script>

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
/*$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});



*/


</script>


<script>

  var header_language     = <?php echo $header_language; ?>;
  var footer_language     = <?php echo $footer_language; ?>;
  var login_language      = <?php echo $login_language ; ?>;
  var international_store = <?php echo $international_store; ?>;

<?php if (isset($this->session->data['custom_store'])) {
        if ($this->session->data['custom_store'] == 'single') { ?>
var custom_store        = header_language.text_wholesale_store;
<?php  } else { ?>
var custom_store        = header_language.text_singles_store;
<?php  } }else { ?>
var custom_store        = header_language.text_singles_store;
<?php } ?>

ReactDOM.render(React.createElement(Header, {custom_store:custom_store, language:header_language, international_store:international_store},null ), document.getElementById('header_box'));
ReactDOM.render(React.createElement(Footer, {language:footer_language, international_store:international_store},null ), document.getElementById('footer_box'));
ReactDOM.render(React.createElement(Otpform, {language:login_language, international_store:international_store},null ), document.getElementById('opt_box'));
ReactDOM.render(React.createElement(Login, {language:login_language, international_store:international_store},null ), document.getElementById('login_box'));
ReactDOM.render(React.createElement(Register, {language:login_language, international_store:international_store},null ), document.getElementById('register_box'));
ReactDOM.render(React.createElement(Success, {language:login_language, international_store:international_store},null ), document.getElementById('success_box'));

</script>

<script type="text/javascript">
   $(document).ready(function() {
    $('.toggle_icon').click(function(){
     $(this).find('i').toggleClass('fa-plus-square-o fa-minus-square-o');
    });

    $('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();
   // navcation Icons(header)
      $(window).on('scroll',function(){
          if($('.full_header').hasClass('affix')){
            $('.header_navigation').find('.nav_icon_bar_bottem').hide();
            $('.header_navigation').find('li').eq(0).removeClass('inner_menu_icon');
          }else{
            $('.header_navigation').find('.nav_icon_bar_bottem').show();
            $('.header_navigation').find('li').eq(0).addClass('inner_menu_icon');
          }
        });
       $("header").affix({offset: {top: $(".sub_navbar").outerHeight(true)} });

 });
</script>

<script type="text/javascript">
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
         var customer_id = getCookie('customer_id');
         var customer_access_token = getCookie('customer_access_token');
         var form_data         = $("#update_number_form").serialize();
         
             form_data         = form_data.replace("country_code="+country_code_val, "country_code="+country_code_text+"&country_iso_code="+country_code_val+"&customer_id="+customer_id+"&customer_access_token="+customer_access_token);

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

         var customer_id = getCookie('customer_id');
         var customer_access_token = getCookie('customer_access_token');

         $("#verify_otp_form input[name=customer_id]").val(customer_id);
         $("#verify_otp_form input[name=customer_access_token]").val(customer_access_token);
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
<script type="text/javascript">

<?php if ($international_store == 1) { ?>

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

<?php } ?>

/* Start chatbox toggle */
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 48 || charCode > 57))
        return false;

    return true;
}

$('.chat_box_content li.dropdown').each(function() {
    var $dropdown = $(this);
    
    //$(".chat_box_content li.dropdown").css("border-bottom: 1px solid #ccc;");

    $("a.dropdown-link", $dropdown).click(function(e) {
        e.preventDefault();
        
        if($dropdown.hasClass("active")){ 
            $(".chat_box_content li.dropdown").removeClass("active");
            $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
            
            //toggle arraows
            $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down");
            $(".dropdown_arrow i", this).addClass('fa-angle-right', 200);
            //end toggle arraows
            
        }else{ 
            $(".chat_box_content li.dropdown").removeClass("active");
            $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
            
            $dropdown.addClass("active", 200);
            $dropdown.prev().css("border-bottom", "none");
            
            //toggle arraows
            $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down");
            $(".dropdown_arrow i", this).toggleClass('fa-angle-down', 200);
            //end toggle arraows
        }
        
        $div = $("ul.list-unstyled", $dropdown);
        $div.slideToggle();
        
        $("ul.list-unstyled").not($div).slideUp();
        return false;
    });

});
/* End chatbox toggle */

$(".chat_box_heading_div").click(function(){
    $(".chat_box_content").toggle();
    $("i", this).toggleClass("fa-angle-up fa-angle-down");
    $(".chat_box_content ul.list-unstyled").hide();
    $(".chat_box_content li.dropdown").removeClass("active");
    $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
    $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down"); 
    $('.callback_request_input').val('');
    $('.chat_box_content .error_msg').html(''); 
});
$(".chat_here").click(function(){
    $(".chat_box_content").toggle();
});

</script>
<script type="text/javascript">
    $( ".callback_request_button" ).click(function() {
            
            var mobile = $(this).closest("ul.list-unstyled").find("input").val();
            
            var status = true;
            var msg = '';   
            
            if( mobile == ''){
                msg = "<?php echo $text_error_mobile_no ?>";
                status = false;
            }else
            if(isNaN(mobile)||mobile.indexOf(" ")!=-1){
                msg = "<?php echo $text_error_mobile_no ?>";
                status = false;
            }else
            if (mobile.length!=10){
                msg = "<?php echo $text_error_mobile_no ?>";
                status = false;
            }else
            if (mobile.charAt(0)=="0"){
                msg = "<?php echo $text_error_mobile_not_start_zero ?>";
                status = false;
            }
            
            if(status == false){
                $(this).closest("ul.list-unstyled").find('.error_msg').html(msg);
                return false
            }else{
                var ajax_url = 'index.php?route=common/home/sendMailForCallBackRequest&mobile='+mobile
                $.ajax({
                    url: ajax_url,
                    beforeSend: function() {
                        $(this).closest("ul.list-unstyled").find('.loading').html('<img src="http://cdnimages.net/loader.gif" />');
                    },
                    success: function( data ){
                        $('.error_msg').html('');
                        $('.loading').html('');
                        $(".success_mail").fadeTo(2000, 500).slideUp(500, function(){
                            $(this).closest("ul.list-unstyled").find(".success_mail").slideUp(500);
                        });
                    }
                }); 
                
            }
            
        }); 
</script>
