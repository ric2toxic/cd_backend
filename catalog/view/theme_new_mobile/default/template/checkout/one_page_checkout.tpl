<?php echo $header;  ?>
<style>
    .bootstrap-tagsinput
    {
      box-shadow:none !important;
      border-radius:0px !important;
      width: 100%;  
      padding: 10px !important;
    }
    .bootstrap-tagsinput .tag
    {
      font-size: 14px;
      line-height: unset;
      border-radius: unset;
      padding: 0px 5px 0px 5px;
    }
    .bootstrap-tagsinput input {padding:0px !important; border:none !important;}
</style>
<input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken" />

<div class="container-fluid white_bg">
	<?php if(isset($prev_error)) { ?>
	<div class="prev_checkout_error">
		<p>
			<strong><?php echo $prev_error; ?></strong>
		</p>
	</div>
	<?php } ?>
	<div class="row">
		<div class="cart_full_box" id="cart_full_box" >
			<div class="col-xs-12 skeleton" style="background: white;" >
				<div class="shopping_cart"></div>
				<div class="pickup_city" ></div>
				<div class="item" ></div>
			</div>
		</div>
	</div>
</div>

<!--notifications -->
<div  class="alert alert-success" style="display: none;"></div>
<div class="col-xs-6 col-md-6" id="notification" style="display: none;">
</div>

<!-- Add new address popup  -->
<div class="modal fade add_new_address" id="add_new_address" role="dialog">
	<div class="modal-dialog" style="z-index: 1050;">
		<div class="modal-content">
			<div class="modal-header address_popup_head" style="margin-bottom: -30px;">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="address_popup_header"><?php echo $text_new_address; ?></h4>
			</div>
			<div class="modal-body" id="address_popup">

			</div>
		</div>
	</div>
</div>

<!-- I want this design popup -->
<div class="modal fade want_design" id="want_design" role="dialog">
	<div class="modal-dialog" style="top:20%;z-index: 1050;">
		<div class="modal-content">
			<div class="panel-body padding_top_bottem">
				<input type="hidden" id="want_design_product_id" value=""/>
				<input type="hidden" id="want_design_product_status" value=""/>
				<button type="button" class="close" data-dismiss="modal" style="size: 15px;">&times;</button>
				<br/>
				<p><strong>Hi <?php echo $this->customer->getFirstName(); ?></strong></p>
				<?php echo $comment_popup_heading; ?>
				<textarea rows="3" style="width:100%;" id="want_design_comment" maxlength="300" ></textarea>
				<button class="btn deliver_btn pull-right" id="submit_want_design"><?php echo $comment_popup_send; ?></button>
			</div>
			<div class="modal-footer popup_footer_padding_none alert alert-danger mobile_modal_footer">
				<div id="want_design_warning"></div>
			</div>
		</div>
	</div>
</div>

<!-- Add telephone  -->
<div class="modal fade add_new_address" id="add_telephone" role="dialog">
	<div class="modal-dialog" style="z-index: 1050;width: 95%">
		<div class="modal-content">
			<div class="modal-header address_popup_head">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo $error_mobile; ?></h4>
			</div>
			<div class="modal-body" id="address_popup" style="min-height: 100px;">
				<div class="col-sm-2 selectdiv" style="width: 50%;">
					<select id="select_country_code" class="select_set_box"></select>
				</div>
				<div class="col-sm-6">
					<input type="text" class="select_set_box" placeholder="Mobile number" style="padding: 5px;margin-top: 10px;" minlength="6" maxlength="32" id="input_telephone" />
					<div id="error_telephone" style="color: red;margin-bottom: 10px;"></div>
				</div>
				<div class="col-sm-4">
					<button class="btn deliver_btn" style="margin-top: 0px;" id="submit_telephone"><?php echo $button_submit; ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Confirm action(remove,move to wishlist,clear cart etc) model -->
<div id="confirm_popup" class="modal fade" role="dialog">
	<div class="modal-dialog" style="top:20%;z-index: 1050;">
		<div class="modal-content alert alert-info">
			<div class="modal-body" style="font-size: 15px;" id="confirm_body"></div>
			<div class="modal-footer popup_footer_padding_none mobile_modal_footer" id="confirm_footer"></div>
		</div>
	</div>
</div>

<!-- alert model -->
<div id="alert_popup" class="modal fade" role="dialog">
	<div class="modal-dialog" style="top:20%;z-index: 1050;">
		<div class="modal-content alert alert-warning">
			<div class="modal-body" style="font-size: 15px;">
				<button type="button" class="close" data-dismiss="modal" style="size: 15px;">&times;</button>
				<div id="alert_body"></div>
			</div>
			<div class="modal-footer popup_footer_padding_none mobile_modal_footer">
				<button type="button" data-dismiss="modal" class="btn popup_close_btn pull-right"><?php echo $button_ok; ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Help popup -->
<div class="modal fade" id="help_popup_section" role="dialog">
	<div class="modal-dialog" style="top:20%;z-index: 1050;">
		<div class="modal-content">
			<div class="modal-header help_popup_head">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo $text_for_help; ?></h4>
			</div>
			<div class="modal-body help_popup_body">
				<div class="form-group help_popup_detail">
					<label><i class="fa fa-phone"></i> <?php echo $text_contact; ?> :</label>
					<span><a href="tel:+911414049163">(+91) 141 - 4049163</a></span>
				</div>
				<div class="form-group help_popup_detail">
					<label><i class="fa fa-whatsapp"></i> <?php echo $text_whatsapp; ?>:</label>
					<span><a href="https://api.whatsapp.com/send?phone=918696491521&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products.">(+91) 8696491521</a></span>
				</div>
				<div class="form-group help_popup_request">
					<a  href="#call_back_input" data-toggle="collapse"><?php echo $text_call_request; ?></a>
					<div class="call_back_input collapse" id="call_back_input">
						<input id="phone_number" class="input_phone_number" placeholder="Phone Number" type="text" data-role="none">
						<button type="button" class="btn deliver_btn btn_call_me" id="btn_call_me" data-role="none"><?php echo $text_call_me; ?></button>
						<div class="error_phone_number" id="error_phone_number"></div>
					</div>
					<div class="clearfix"></div>
				</div>

			</div>
			<div class="modal-footer help_popup_footer">
				<button type="button" class="btn close_btn" data-dismiss="modal"><?php echo $button_close; ?></button>
			</div>
		</div>

	</div>
</div>

<!-- UPI help model -->
<div id="upi_help_popup" class="modal fade" role="dialog">
	<div class="modal-dialog" style="top:5%;z-index: 1050;">
		<div class="modal-header help_popup_head" style="border-bottom: 0px;">
			<button type="button" class="close close_btn_payment_info" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
		</div>
		<div class="modal-content">
			<img class="img-responsive" src="<?php echo STATIC_CONTENT_URL_SSL ?>upi_help_mobile.jpg"/>
		</div>
	</div>
</div>

<img src="<?php echo STATIC_CONTENT_URL_SSL ?>loader.gif" id="loading-indicator" style="display:none" />
<style>
	#loading-indicator {
		position: fixed;
		left: 45%;
		top: 45%;
		z-index:1060;
	}
</style>

<script type="text/javascript">
    $(document).ready(function(){
        $("#showWhatsAppNumber").click(function(){
            $("#showWhatsAppNumberBox").toggle();
        });
    });

</script>


<script>
    function numberWithCommas(x) {
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
<script>
    $('#pincode').keyup(function () {
        var char_count = $(this).val().length;
        if (char_count > 5) {
            var pincode = $(this).val();

            if ($("#ctoken").get(0)) {
                var ctoken = $("#ctoken").val();
            } else {
                var ctoken = 0;
            }

            $.ajax({
                url: "index.php?route=account/address/autoPopulateAddress&ctoken=" + ctoken,
                type: "post",
                dataType: "json",
                data: "pincode=" + pincode,
                beforeSend:function(){
                    $('#city').val('...');
                    $('#state').val('...');
                    $('#country').val('...');
                },
                success: function (json) {
                    $('#city').val(json.city);
                    $('#state').val(json.state);
                    $('#country').val(json.country);
                }
            });
        }
    });

    $('#btn_call_me').click(function() {
        $('#error_phone_number').html("");
        var phone_number = $('#phone_number').val();
		if(!validateMobileNumber(phone_number)){
		    $('#error_phone_number').html("Please enter the valid mobile number (e.g. 8696491521)");
		    return;
        }

        $.ajax({
            url: "api/checkout/callMeBackRequest",
            type: "post",
            dataType: "json",
            data: "phone_number=" + phone_number,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete: function (json) {
                $('#loading-indicator').hide();
                $('#help_popup_section').modal('hide');
            }
		}).promise()
		.then(function (json) {
                if(json['error']){
                    $('#alert_body').html(json['error']['warning']);
                    $('#alert_popup').modal();
                }
                else{
                    $('#alert_body').html("<h3 style='color: green;display: block;text-align: center;'>Success!</h3><div style='color: #00a758 ;display: block;text-align: center;font-size: 14px;'> We have recieved your request, we will call you shortly.</div>");
                    $('#alert_popup').modal();
                }
			},
			function (xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('#alert_body').html("Oops, Something went wrong.<br/>Please try again.");
				$('#alert_popup').modal();
			}
		);
    });
</script>

<script type="text/javascript">
    $('#submit_want_design').click(function() {
        var product_id = $('#want_design_product_id').val();
        var product_status = $('#want_design_product_status').val();
        var popup_comment = $('#want_design_comment').val();
        if(popup_comment.length < 2){
            $('#want_design_warning').html("Comment should have atleast two characters.");
            return;
        }
        $.ajax({
            type : "POST",
            url  : 'index.php?route=product/search/user_comment',
            data : "product_id="+product_id+"&product_status="+product_status+"&popup_comment="+popup_comment,
            dataType: 'json',
            beforeSend: function() {
                $('#submit_want_design').button('loading');
            },
            complete: function() {
                $('#submit_want_design').button('reset');
                $('#want_design').modal('hide');
            },
            success: function(data){
                $('#want_design').modal('hide');
                $("#notification").fadeIn("slow").html('<?php echo $text_want_design_sucess; ?>');
                $("#notification").fadeOut(3000);
            }
        });
        $('#want_design_comment').val("");
    });
</script>

<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js"></script>

<!-- Bootstrap Core CSS -->
<link href="catalog/view/theme_new_mobile/default/css/bootstrap.min.css" rel="stylesheet"/>
<link href="catalog/view/theme_new_mobile/default/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="catalog/view/theme_new_mobile/default/css/style_mobile.css?v=1.2" rel="stylesheet"/>
<link href="catalog/view/theme_new_mobile/default/css/flags.css" rel="stylesheet"/>
<link href="catalog/view/theme_new_mobile/default/javascript/touchspin/jquery.bootstrap-touchspin.css" type="text/css" rel="stylesheet" media="screen" />
<script src="catalog/view/theme_new_mobile/default/javascript/jquery.placeholder.label.js"></script>

<script src="catalog/view/theme_new_mobile/default/javascript/bootstrap-tagsinput.min.js"></script>

<link href="catalog/view/theme_new_mobile/default/css/skeleton.css" rel="stylesheet"/>
<link href="catalog/view/theme_new_mobile/default/css/bootstrap-tagsinput.css" rel="stylesheet"/>
<script src="catalog/view/javascript/common.js"></script>
<script src="catalog/view/javascript/reactjs/react.js"></script>
<script src="catalog/view/javascript/reactjs/react-dom.js"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/cart.js?v=40.18"></script>
<script>


    var items = <?php echo ( ( isset($product_json) )?$product_json:"[]");  ?> ;
    var total_sets = <?php echo ( ( isset($total_sets) )?$total_sets:'0'); ?>;
    var total_pieces = <?php echo ( ( isset($total_pieces) )?$total_pieces:'0'); ?>;
    var totals = <?php echo ( ( isset($totalJson) )?$totalJson:"[]"); ?>;
    var clear_cart = <?php echo ( ( isset($clear_cart) )?$clear_cart:"[]"); ?>;
    var tax_refund = <?php echo "'".( ( isset($tax_refund) )?$tax_refund:"")."'"; ?>;
    var shipping_addresses = <?php echo ( ( isset($shipping_addresses) )?$shipping_addresses:"[]"); ?>;
    var payment_data = <?php echo ( ( isset($payment_data) )?$payment_data:"[]"); ?>;
    var language = <?php echo $language; ?>;
    var international_store = <?php echo ( ( isset($international_store) )?$international_store:0); ?>;
    var countries = <?php print_r ( ( isset($countries) )?$countries:array()); ?>;
    var is_app = <?php echo ( ( isset($is_app) )?$is_app:0); ?>;
    var customer_id = getCookie('customer_id');
    var cart_data = localStorage.getItem(customer_id+'_cart_data');
		var neo_credit_user_credit_limit = <?php print_r ( ( isset($neo_credit_user_credit_limit) )?$neo_credit_user_credit_limit:0); ?>;
    
        var rbl_credit_user_credit_limit = '<?php print_r ( ( isset($rbl_credit_user_credit_limit) )?$rbl_credit_user_credit_limit:0); ?>';
        var text_rbl_limit_error = '<?php print_r ( ( isset($text_rbl_limit_error) )?$text_rbl_limit_error:''); ?>';
        var RBL_ORDER_LIMIT = <?php echo RBL_ORDER_LIMIT; ?>;
        
	var tab = 'delivery';
	if(window.location.hash){
		tab = window.location.hash.substr(1);
	}
    cart_data = null;
    if(cart_data == null){
        cart_data = {"cart_data":"","cart_summary":"","shipping":{"shipping_methods":""},"customer":"","tab":"delivery","have_gst_tab":0};
	}
	else{
    	cart_data = JSON.parse(cart_data);
	}
    var app_language = <?php echo $app_language; ?>;

    var cod_available_limit       = '<?php echo COD_AVAILABLE_LIMIT; ?>';
    var cod_advance_amount_limit  = '<?php echo COD_ADVANCE_AMOUNT_LIMIT; ?>';
    var cdn_url = '<?php echo STATIC_CONTENT_URL_SSL; ?>';

    ReactDOM.render(React.createElement(Checkout, {cart:cart_data, shipping_addresses:shipping_addresses, payment_data:payment_data, language:language, international_store:international_store,countries:countries,is_app:is_app,tab:tab,neo_credit_user_credit_limit:neo_credit_user_credit_limit,rbl_credit_user_credit_limit:rbl_credit_user_credit_limit,text_rbl_limit_error:text_rbl_limit_error, RBL_ORDER_LIMIT:RBL_ORDER_LIMIT, cod_available_limit:cod_available_limit,cod_advance_amount_limit:cod_advance_amount_limit},null ), document.getElementById('cart_full_box'));

</script>

<script type="text/javascript">
    $('#name').bind('keyup blur',function(){
        var node = $(this);
        node.val(node.val().replace(/[^a-z A-Z]/g,'') ); }
    );
</script>

<script>
    function validateGSTNumber(gst_number){
        var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/;
        var code = /([C,P,H,F,A,T,B,L,J,G,E])/;
        var code_chk = gst_number.substring(5,6).toUpperCase();
        if (reggstin.test(gst_number) == false) {
            return false;
        }
        if (code.test(code_chk) == false) {
            return false;
        }
        return true;
    }

    function validateMobileNumber(mobile_number){
        var reggstin = /^([1-9]){1}([0-9]){9}$/;
        if (reggstin.test(mobile_number) == false)
            return false;
        else
        	return true;
    }
</script>
<script>
    var triggerElementID = null; // this variable is used to identity the triggering element
    var fingerCount = 0;
    var startX = 0;
    var startY = 0;
    var curX = 0;
    var curY = 0;
    var deltaX = 0;
    var deltaY = 0;
    var horzDiff = 0;
    var vertDiff = 0;
    var minLength = 72; // the shortest distance the user may swipe
    var swipeLength = 0;
    var swipeAngle = null;
    var swipeDirection = null;

    // The 4 Touch Event Handlers

    // NOTE: the touchStart handler should also receive the ID of the triggering element
    // make sure its ID is passed in the event call placed in the element declaration, like:
    // <div id="picture-frame" ontouchstart="touchStart(event,'picture-frame');"  ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

    function touchStart(event,passedName) {
        // disable the standard ability to select the touched object
        //event.preventDefault();
        // get the total number of fingers touching the screen
        fingerCount = event.touches.length;
        // since we're looking for a swipe (single finger) and not a gesture (multiple fingers),
        // check that only one finger was used
        if ( fingerCount == 1 ) {
            // get the coordinates of the touch
            startX = event.touches[0].pageX;
            startY = event.touches[0].pageY;
            // store the triggering element ID
            triggerElementID = passedName;
        } else {
            // more than one finger touched so cancel
            touchCancel(event);
        }
    }

    function touchMove(event) {
        //event.preventDefault();
        if ( event.touches.length == 1 ) {
            curX = event.touches[0].pageX;
            curY = event.touches[0].pageY;
        } else {
            touchCancel(event);
        }
    }

    function touchEnd(event) {
        //event.preventDefault();
        // check to see if more than one finger was used and that there is an ending coordinate
        if ( fingerCount == 1 && curX != 0 ) {
            // use the Distance Formula to determine the length of the swipe
            swipeLength = Math.round(Math.sqrt(Math.pow(curX - startX,2) + Math.pow(curY - startY,2)));
            // if the user swiped more than the minimum length, perform the appropriate action
            if ( swipeLength >= minLength ) {
                caluculateAngle();
                determineSwipeDirection();
                processingRoutine();
                touchCancel(event); // reset the variables
            } else {
                touchCancel(event);
            }
        } else {
            touchCancel(event);
        }
    }

    function touchCancel(event) {
        // reset the variables back to default values
        fingerCount = 0;
        startX = 0;
        startY = 0;
        curX = 0;
        curY = 0;
        deltaX = 0;
        deltaY = 0;
        horzDiff = 0;
        vertDiff = 0;
        swipeLength = 0;
        swipeAngle = null;
        swipeDirection = null;
        triggerElementID = null;
    }

    function caluculateAngle() {
        var X = startX-curX;
        var Y = curY-startY;
        var Z = Math.round(Math.sqrt(Math.pow(X,2)+Math.pow(Y,2))); //the distance - rounded - in pixels
        var r = Math.atan2(Y,X); //angle in radians (Cartesian system)
        swipeAngle = Math.round(r*180/Math.PI); //angle in degrees
        if ( swipeAngle < 0 ) { swipeAngle =  360 - Math.abs(swipeAngle); }
    }

    function determineSwipeDirection() {

        if ( (swipeAngle <= 45) && (swipeAngle >= 0) ) {
            swipeDirection = 'left';
        } else if ( (swipeAngle <= 360) && (swipeAngle >= 315) ) {
            swipeDirection = 'left';
        } else if ( (swipeAngle >= 135) && (swipeAngle <= 225) ) {
            swipeDirection = 'right';
        } else if ( (swipeAngle > 45) && (swipeAngle < 135) ) {
            swipeDirection = 'down';
        } else {
            swipeDirection = 'up';
        }
    }

    function processingRoutine() {
        var swipedElement = document.getElementById(triggerElementID);
        if ( swipeDirection == 'left' ) {
            $('.carousel_btn_box .right').trigger('click');
        } else if ( swipeDirection == 'right' ) {
            $('.carousel_btn_box .left').trigger('click');
        } else if ( swipeDirection == 'up' ) {
            // do nothing
        } else if ( swipeDirection == 'down' ) {
            // do nothing
        }
    }
</script>

<script>
    //Get CC phone
    function getccPhone(){
        var countries = [
            {country_code:'AE',mobile_code:'+971'},
            {country_code:'AF',mobile_code:'+93'},
            {country_code:'AL',mobile_code:'+355'},
            {country_code:'AS',mobile_code:'+376'},
            {country_code:'AD',mobile_code:'+376'},
            {country_code:'AO',mobile_code:'+244'},
            {country_code:'AG',mobile_code:'+1-268'},
            {country_code:'AR',mobile_code:'+54'},
            {country_code:'AM',mobile_code:'+374'},
            {country_code:'AU',mobile_code:'+61'},
            {country_code:'AT',mobile_code:'+43'},
            {country_code:'AZ',mobile_code:'+994'},
            {country_code:'BS',mobile_code:'+1-242'},
            {country_code:'BH',mobile_code:'+973'},
            {country_code:'BD',mobile_code:'+880'},
            {country_code:'BB',mobile_code:'1-246'},
            {country_code:'BY',mobile_code:'+375'},
            {country_code:'BE',mobile_code:'+32'},
            {country_code:'BZ',mobile_code:'+501'},
            {country_code:'BJ',mobile_code:'+229'},
            {country_code:'BT',mobile_code:'+975'},
            {country_code:'BO',mobile_code:'+591'},
            {country_code:'BA',mobile_code:'+387'},
            {country_code:'BW',mobile_code:'+267'},
            {country_code:'BR',mobile_code:'+55'},
            {country_code:'BN',mobile_code:'+673'},
            {country_code:'BG',mobile_code:'+359'},
            {country_code:'BF',mobile_code:'+226'},
            {country_code:'BI',mobile_code:'+257'},
            {country_code:'GB',mobile_code:'+44'},
            {country_code:'KH',mobile_code:'+855'},
            {country_code:'CA',mobile_code:'+1'},
            {country_code:'CV',mobile_code:'+238'},
            {country_code:'CF',mobile_code:'+236'},
            {country_code:'CM',mobile_code:'+237'},
            {country_code:'TD',mobile_code:'+235'},
            {country_code:'CL',mobile_code:'+56'},
            {country_code:'CN',mobile_code:'+86'},
            {country_code:'CO',mobile_code:'+57'},
            {country_code:'KM',mobile_code:'+269'},
            {country_code:'CG',mobile_code:'+242'},
            {country_code:'CR',mobile_code:'+506'},
            {country_code:'CI',mobile_code:'+225'},
            {country_code:'CU',mobile_code:'+53'},
            {country_code:'CY',mobile_code:'+357'},
            {country_code:'CZ',mobile_code:'+420'},
            {country_code:'DK',mobile_code:'+45'},
            {country_code:'DJ',mobile_code:'+253'},
            {country_code:'DM',mobile_code:'+1-767'},
            {country_code:'DO',mobile_code:'+1-809'},
            {country_code:'DZ',mobile_code:'+213'},
            {country_code:'EC',mobile_code:'+593'},
            {country_code:'EG',mobile_code:'+20'},
            {country_code:'SV',mobile_code:'+503'},
            {country_code:'GQ',mobile_code:'+240'},
            {country_code:'ER',mobile_code:'+291'},
            {country_code:'EE',mobile_code:'+372'},
            {country_code:'ET',mobile_code:'+251'},
            {country_code:'FJ',mobile_code:'+679'},
            {country_code:'FI',mobile_code:'+358'},
            {country_code:'FR',mobile_code:'+33'},
            {country_code:'GA',mobile_code:'+241'},
            {country_code:'GM',mobile_code:'+220'},
            {country_code:'GE',mobile_code:'+995'},
            {country_code:'DE',mobile_code:'+49'},
            {country_code:'GH',mobile_code:'+233'},
            {country_code:'GR',mobile_code:'+30'},
            {country_code:'GD',mobile_code:'+1-473'},
            {country_code:'GT',mobile_code:'+502'},
            {country_code:'GN',mobile_code:'+224'},
            {country_code:'GW',mobile_code:'+245'},
            {country_code:'GY',mobile_code:'+592'},
            {country_code:'HT',mobile_code:'+509'},
            {country_code:'HN',mobile_code:'+504'},
            {country_code:'HK',mobile_code:'+852'},
            {country_code:'HU',mobile_code:'+36'},
            {country_code:'HR',mobile_code:'+385'},
            {country_code:'IS',mobile_code:'+354'},
            {country_code:'IN',mobile_code:'+91'},
            {country_code:'ID',mobile_code:'+62'},
            {country_code:'IR',mobile_code:'+98'},
            {country_code:'IQ',mobile_code:'+964'},
            {country_code:'IE',mobile_code:'+353'},
            {country_code:'IL',mobile_code:'+972'},
            {country_code:'IT',mobile_code:'+39'},
            {country_code:'JM',mobile_code:'+1-876'},
            {country_code:'JP',mobile_code:'+81'},
            {country_code:'JO',mobile_code:'+962'},
            {country_code:'KZ',mobile_code:'+7'},
            {country_code:'KE',mobile_code:'+254'},
            {country_code:'KI',mobile_code:'+686'},
            {country_code:'KP',mobile_code:'+850'},
            {country_code:'KR',mobile_code:'+82'},
            {country_code:'KW',mobile_code:'+965'},
            {country_code:'KG',mobile_code:'+996'},
            {country_code:'LA',mobile_code:'+856'},
            {country_code:'LV',mobile_code:'+371'},
            {country_code:'LB',mobile_code:'+961'},
            {country_code:'LS',mobile_code:'+266'},
            {country_code:'LR',mobile_code:'+231'},
            {country_code:'LY',mobile_code:'+218'},
            {country_code:'LI',mobile_code:'+423'},
            {country_code:'LU',mobile_code:'+352'},
            {country_code:'MO',mobile_code:'+389'},
            {country_code:'MG',mobile_code:'+261'},
            {country_code:'MW',mobile_code:'+265'},
            {country_code:'MY',mobile_code:'+60'},
            {country_code:'MX',mobile_code:'+52'},
            {country_code:'MC',mobile_code:'+377'},
            {country_code:'MA',mobile_code:'+212'},
            {country_code:'NP',mobile_code:'+977'},
            {country_code:'NL',mobile_code:'+31'},
            {country_code:'NZ',mobile_code:'+64'},
            {country_code:'NI',mobile_code:'+505'},
            {country_code:'NE',mobile_code:'+227'},
            {country_code:'NG',mobile_code:'+234'},
            {country_code:'NO',mobile_code:'+47'},
            {country_code:'OM',mobile_code:'+968'},
            {country_code:'PK',mobile_code:'+92'},
            {country_code:'PA',mobile_code:'+507'},
            {country_code:'PG',mobile_code:'+675'},
            {country_code:'PY',mobile_code:'+595'},
            {country_code:'PE',mobile_code:'+51'},
            {country_code:'PH',mobile_code:'+63'},
            {country_code:'PL',mobile_code:'48'},
            {country_code:'PT',mobile_code:'+351'},
            {country_code:'QA',mobile_code:'+974'},
            {country_code:'RU',mobile_code:'+7'},
            {country_code:'RW',mobile_code:'+250'},
            {country_code:'SA',mobile_code:'+966'},
            {country_code:'SN',mobile_code:'+221'},
            {country_code:'SG',mobile_code:'+65'},
            {country_code:'SK',mobile_code:'+421'},
            {country_code:'SI',mobile_code:'+386'},
            {country_code:'ZA',mobile_code:'+27'},
            {country_code:'ES',mobile_code:'+34'},
            {country_code:'LK',mobile_code:'+94'},
            {country_code:'SD',mobile_code:'+249'},
            {country_code:'SZ',mobile_code:'+268'},
            {country_code:'SE',mobile_code:'+46'},
            {country_code:'CH',mobile_code:'+41'},
            {country_code:'SY',mobile_code:'+963'},
            {country_code:'TZ',mobile_code:'+255'},
            {country_code:'TH',mobile_code:'+66'},
            {country_code:'TG',mobile_code:'+228'},
            {country_code:'TO',mobile_code:'+676'},
            {country_code:'TN',mobile_code:'+216'},
            {country_code:'TR',mobile_code:'+90'},
            {country_code:'TM',mobile_code:'+993'},
            {country_code:'UA',mobile_code:'+380'},
            {country_code:'US',mobile_code:'+1'}

        ];
        return countries;
    }

</script>

<!--Start of Tawk.to Script-->
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

<?php }*/ ?>
</script>
<!--End of Tawk.to Script-->
