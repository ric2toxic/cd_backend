class Login extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

	render() {
     let international_store = this.props.international_store;
        function verify_otp_forgot_form(e) 
        {        
         e.preventDefault();
         var login_terms = $("#login_terms_otp").prop('checked');
         if(!login_terms)
         {
           alert("please accept the terms of service.");
           return false;
         }
         $("#verify_otp_forgot_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = './api/login/verify_otp';
         var form_data = $("#verify_otp_forgot_form").serialize();
         var ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                {
                    var data = response.data;
                    $("#verify_otp_forgot_form :input[name=otp]").val('');
                    $("#verify_otp_forgot_form :input[name=otp]").prev('label').css('margin-top', '20px');
                    if(data['error'])
                    {
                     $("#verify_otp_forgot_form input[type=submit]").val('verify').prop("disabled", false);
                     if(!data['msg']) { $('.login_password_error').html('<i class="fa fa-check-circle"></i> Please enter OTP'); }
                     else { $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data['msg']); }
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                    }
                    else
                    {
                      var gst = 0;
                      var dropshipper = false;
                      if(data['gst_number'] && data['gst_number'] != '') { gst = 1; }
                      if(data['is_dropshipper'] && parseInt(data['is_dropshipper'], 0) > 0) { dropshipper = true; }  

                      dataLayer.push({'customer_id': data['customer_id'], 
                                      'first_name': data['first_name'], 
                                      'last_name': data['last_name'], 
                                      'email': data['email'], 
                                      'phone': data['telephone'],
                                      'customer_type': data['customer_type'],
                                      'store_id': international_store,
                                      'user_city': data['user_city'],
                                      'gst': gst,
                                      'dropshipper': dropshipper,
                                      'has_website': parseInt(data['has_website'], 0),
                                      'self_order': data['self_order'].toString(),
                                      'pincode': parseInt(data['pincode'], 10),
                                      'membership': data['membership']['membership'].toString(),
                                      'membership_id': data['membership']['membership_id'].toString(),
                                      'expiry_date': data['membership']['expiry_date'].toString() });

                      dataLayer.push({'event': 'we-custom-login'});
                     if(data['redirect_cart']==0)
                      {
                        setTimeout(function(){ window.location.assign(data['url']); }, 1000);
                      }
                      else if(data['redirect_cart'] == 'credit')
                      { 
                        setTimeout(function(){ window.location.assign(data['credit_application']); }, 1000);
                      }
                      else
                      {

                        $('#login_popup_close').click();
                        $('#login_link_header, #footer-login, #wishlist-total').attr('href',data['url']);
                        $('#login_link_header, #footer-login, #wishlist-total').attr('data-target','');
                        $('.cart_shopping_success').html(data['msg']);

                        if(data['redirect_cart'] == 1)
                        {
                          $('#button-cart, #detail-button-cart, .button-cart-list').trigger("click");
                          //$('#cart_shopping_popup').modal("show");
                        }
                         else if(data['redirect_cart'] == 2)
                        { 
                          $('#want_designe_popup').modal("show"); 
                        }
                        else
                        {
                          var redirect_cart =  data['redirect_cart'].split("-");
                          cart_add(redirect_cart[0], redirect_cart[1], 1);
                          $('#cart_shopping_popup').modal("show");
                        }

                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
       }


        function login_form(e) 
        { 
                     
         e.preventDefault();
         var login_terms = $("#login_terms").prop('checked');
         if(!login_terms)
         {
           alert("please accept the terms of service.");
           return false;
         }
        $("#login_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = './api/login/login';
         var form_data = $("#login_form").serialize();
         $("#login_form :input").prop("disabled", true);

        var ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                {
                    var data = response.data;
                    $("#login_form :input").prop("disabled", false);
                    if(data['error'])
                    {
                     $("#login_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                    }
                    else
                    {
                      var gst = 0;
                      var dropshipper = false;
                      if(data['gst_number'] && data['gst_number'] != '') { gst = 1; }
                      if(data['is_dropshipper'] && parseInt(data['is_dropshipper'], 0) > 0) { dropshipper = true; }  

                      dataLayer.push({'customer_id': data['customer_id'], 
                                      'first_name': data['first_name'], 
                                      'last_name': data['last_name'], 
                                      'email': data['email'], 
                                      'phone': data['telephone'],
                                      'customer_type': data['customer_type'],
                                      'store_id': international_store,
                                      'user_city': data['user_city'],
                                      'gst': gst,
                                      'dropshipper': dropshipper,
                                      'has_website': parseInt(data['has_website'], 0),
                                      'self_order': data['self_order'].toString(),
                                      'pincode': parseInt(data['pincode'], 10),
                                      'membership': data['membership']['membership'].toString(),
                                      'membership_id': data['membership']['membership_id'].toString(),
                                      'expiry_date': data['membership']['expiry_date'].toString()
                                       });
                      dataLayer.push({'event': 'we-custom-login'});

                      if(data['redirect_cart']==0)
                      { 
                        setTimeout(function(){ window.location.assign(data['url']); }, 1000);
                      }
                      else if(data['redirect_cart'] == 'credit')
                      { 
                        setTimeout(function(){ window.location.assign(data['credit_application']); }, 1000);
                      }
                      else
                      {
                        $('#login_popup_close').click();
                        $('#login_link_header, #footer-login, #wishlist-total').attr('href',data['url']);
                        $('#login_link_header, #footer-login, #wishlist-total').attr('data-target','');
                        $('.cart_shopping_success').html(data['msg']);
                        
                        if(data['redirect_cart'] == 1)
                        {
                          $('#button-cart, #detail-button-cart, .button-cart-list').trigger("click");
                        }
                        else if(data['redirect_cart'] == 2)
                        { 
                          $('#want_designe_popup').modal("show"); 
                        }
                        else
                        {   
                          var redirect_cart =  data['redirect_cart'].split("-");     
                          cart_add(redirect_cart[0], redirect_cart[1], 1);
                          $('#cart_shopping_popup').modal("show");
                        }
                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
       }

    function send_otp()
    {
        var ajax = $.ajax({
                url: "./api/login/send_password_otp",
                type: 'post',
                dataType: 'json',
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                {
                   var data = response.data;
                   
                   if(data['error'])
                   {
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                   } 
                   else
                   {
                     $('.login_password_success').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_password_success').show();
                     $('.login_password_error').hide();
                     $("#collapseverify_1").css('visibility','hidden').hide();
                     $("#collapseverify_2").css('visibility','visible').show();
                     $("#login_form :input[name=password]").val('');
                     $("#login_form :input[name=password]").prev('label').css('margin-top', '20px');
                     $("#verify_otp_forgot_form input[name=otp]").val('');
                     $("#verify_otp_forgot_form input[name=otp]").focus();
                     $("#verify_otp_forgot_form input[name=otp]").prev('label').css('margin-top', '20px');
                   } 
                },
               complete: function(data){
                var ajax = null;
               }
            });
    }

  function login_with_password()
   {
     $('.login_passworderror, .login_password_success').hide();
     $('#collapseverify_2').css('visibility','hidden').hide();
     $('#collapseverify_1').css('visibility','visible').show();
     $('#collapseverify_1 #login_form .password_box').focus();
     $("#verify_otp_forgot_form :input[name=otp]").val('');
     $("#verify_otp_forgot_form :input[name=otp]").prev('label').css('margin-top', '20px');
   }


 function login_popup_close(e){
          if($('#product_popup').hasClass('in'))
          {
            $('#login_popup').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
             });
          }
    }

 function edit_login_no()
 {
    $('.login_mobile_success, .login_mobile_error').hide();
    $("#collapseverify").css('visibility','hidden').hide();
    $('#login_popup_close').click();
    $('#login_verify_popup_open').click();

    $("#edit_reg_telephone").hide();
    $("#save_reg_telephone, #send_otp_title").show();
    $('.login_mobile_success, .login_mobile_error').hide(500);
    $("#verify_otp_form :input[name=otp]").val('');
    $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
    $("#collapseverify").hide();
 }   

		return (
       <div className="modal fade add_new_address" id="login_popup" role="dialog" data-backdrop="static" data-keyboard="false">
        <div className="modal-dialog login_register_popup">
          <div className="modal-content">
          
            <div className="modal-header address_popup_head">
              <button type="button" className="close" data-dismiss="modal" onClick={login_popup_close} id="login_popup_close">&times;</button>
              <button className="hide" data-toggle="modal" data-target="#login_popup" id="login_popup_open"></button>
              <h4 className="modal-title">SIGN IN</h4>
            </div>

             <div className="modal-body">
                 <div className="success_msg login_password_success"></div>
                 <div className="danger_msg login_password_error"></div>

               <div className="mobile_details_panel">

                 <div className="mobile_details_panel">
                   <span className="flag_area"><i className="country-flag flagstrap-icon flagstrap-"></i></span>
                 <div className="login_mobile_nmr"> </div> &nbsp;
                   <a href="javascript:;" onClick={() => edit_login_no() }><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
               </div>

                  <div className="clearfix"></div>
                  <div id="collapseverify_1">
                  <form name="login_form"  id="login_form" onSubmit={login_form}>
                  <input type="password" name="password" className="password_box" placeholder="Password" alt="Password" autoComplete="new-password" />
                  <input type="checkbox" name="login_terms" className="login_terms" id="login_terms" value="1" checked="checked" /> 
                  I agree to the <a href="i/terms" target="_blank">terms of service.</a>
                  <div className="clearfix"></div>
                   <div className="otp_agin pull-left"><a href="javascript:;" onClick={send_otp}>Login with otp</a></div>
                   <input type="hidden" name="referrers" value=""/>
                   <input type="hidden" name="pre_order" value="0" id="pre_order" /> 
                   <input type="hidden" name="redirect_cart" value="0" />
                   <input type="submit" name="login" value="Continue" className="btn deliver_btn pull-right" id="login" />
                   </form>
                    </div>

                   <div id="collapseverify_2" style={{display: 'none'}}>
                   <form name="verify_otp_forgot_form"  id="verify_otp_forgot_form" onSubmit={verify_otp_forgot_form}>
                   <input type="text" name="otp" className="password_box" placeholder="Please Enter OTP" maxLength="6" alt="OTP" />
                    <input type="checkbox" name="login_terms_otp" className="login_terms" id="login_terms_otp" value="1" checked="checked" /> 
                  I agree to the <a href="i/terms" target="_blank">terms of service.</a>
                    <div className="clearfix"></div>
                   <div className="otp_agin pull-left"><a href="javascript:;" onClick={login_with_password}>Login with password</a>
                    OR
                    <a href="javascript:;" onClick={send_otp}>Didn't get OTP?</a>
                   </div>
                   <input type="submit" name="verify_otp" value="Verify" className="btn deliver_btn pull-right" />
                   <input type="hidden" name="otp_page" value="forgot_password" label="" />
                   <input type="hidden" name="referrers" value="" />
                   <input type="hidden" name="pre_order" value="0" id="verify_pre_order" /> 
                   <input type="hidden" name="redirect_cart" value="0" id="verify_redirect_cart" />
                   </form>
                    <div className="clearfix"></div>
                    </div>
                </div>
             </div>

            <div className="clearfix"></div>

          </div>
        </div>
   </div>
		)
	}
}
