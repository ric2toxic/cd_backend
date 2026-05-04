import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import Api from '../../api/Api'
import { Link } from 'react-router-dom'
import Dialog from '@material-ui/core/Dialog'
import DialogContent from '@material-ui/core/DialogContent'
import DialogTitle from '@material-ui/core/DialogTitle'
import withMobileDialog from '@material-ui/core/withMobileDialog'

import $ from 'jquery'
import custom from '../../custom/custom'
import webengage from '../../custom/webengage'
import  './index.css'
import { password_otp_formData, verify_otp_formData, loginData, LoginPopupOpen, SuccessPopupOpen} from '../../actions/LoginAction';
import { userloginData, cartData, wishlistData } from '../../actions/HeaderAction';
import { getCartData,cart_add, cart_add_with_option } from '../../actions/CartAction'

class LoginForm  extends Component {

  constructor(props)
  {
     super(props);
      this.login_popup_close     = this.login_popup_close.bind(this);
  }

  login_popup_close()
  {
    this.props.dispatch(LoginPopupOpen(0));     
  }

	render() {
        let self = this;
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

          var reg_telephone    = custom.getCookie("customer_mobile");
          var country_code     = custom.getCookie("country_code");
          var country_iso_code = custom.getCookie("country_iso_code");

          var form_data = $("#verify_otp_forgot_form").serialize();
           form_data    = form_data+'&reg_telephone='+reg_telephone+'&country_code='+country_code+'&country_iso_code='+country_iso_code;

          var response =self.props.dispatch(verify_otp_formData(form_data));
          response.then(function(data){
               
                    $("#verify_otp_forgot_form :input[name=otp]").val('');
                    $("#verify_otp_forgot_form :input[name=otp]").prev('label').css('margin-top', '20px');
                    if(data.error)
                    {
                     $("#verify_otp_forgot_form input[type=submit]").val('verify').prop("disabled", false);
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                    }
                    else
                    {
                      webengage.login(data, international_store);
                      custom.createCookie("customer_id", data.customer_id, 7);
                      custom.createCookie("customer_access_token", data.customer_access_token, 7);
                     if(data.redirect_cart=== "0")
                      {
                        self.props.dispatch(LoginPopupOpen(0));
                        self.props.dispatch(cartData());
                      }
                      else
                      {
                         self.props.dispatch(LoginPopupOpen(0));
                        $('.cart_shopping_success').html(data.msg);
                        if(data.redirect_cart === "credit")
                        {
                          window.location.href = data.credit_application
                        }
                        else if(data.redirect_cart === "1")
                        {
                          $("#for_detail_page").val('1'); 
                          self.props.dispatch(cart_add_with_option());
                          self.props.dispatch(SuccessPopupOpen(1));
                        }
                         else if(data.redirect_cart === "2")
                        { 
                          $('#want_designe_popup').modal("show"); 
                          self.props.dispatch(cartData());
                        }
                        else
                        {
                          $("#for_detail_page").val('1');
                          var redirect_cart =  data.redirect_cart.split("-");
                          self.props.dispatch(cart_add(redirect_cart[0], redirect_cart[1], 1));
                          self.props.dispatch(SuccessPopupOpen(1));
                        }
                      }

                      self.props.dispatch(userloginData());
                      self.props.dispatch(wishlistData());
                      self.props.dispatch(getCartData());
                      
                    }
            })
        .catch(function() {
          console.log('data fetch error');
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
        $("#login_form :input").prop("disabled", true);

        var reg_telephone     = custom.getCookie("customer_mobile");
        var country_code      = custom.getCookie("country_code");
        var cart_session_id   = custom.getCookie('cart_session_id');
        var wishlist_session_id  = custom.getCookie('wishlist_session_id');
        var country_iso_code  = custom.getCookie('country_iso_code');
 
        $("#login_form input[name=reg_telephone]").val(reg_telephone);
        $("#login_form input[name=country_code]").val(country_code);
        $("#login_form input[name=cart_session_id]").val(cart_session_id);
        $("#login_form input[name=wishlist_session_id]").val(wishlist_session_id);
        $("#login_form input[name=country_iso_code]").val(country_iso_code);

         var response =self.props.dispatch(loginData());
           response.then(function(data){

                    $("#login_form :input").prop("disabled", false);
                    if(data.error)
                    {
                     $("#login_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                    }
                    else
                    {
                      webengage.login(data, international_store);
                      self.props.dispatch(LoginPopupOpen(0));
                      custom.createCookie("customer_id", data.customer_id, 7);
                      custom.createCookie("customer_access_token", data.customer_access_token, 7);

                      if(data.redirect_cart === "0")
                      { 
                        self.props.dispatch(LoginPopupOpen(0));
                        self.props.dispatch(cartData()); 
                      }
                      else
                      {
                        if(data.redirect_cart === "credit")
                        {
                          window.location.href = data.credit_application
                        }
                        else if(data.redirect_cart === "1")
                        {
                          $("#for_detail_page").val('1');
                          self.props.dispatch(cart_add_with_option());
                          self.props.dispatch(SuccessPopupOpen(1));
                          $('.cart_shopping_success').html(data.msg);
                        }
                        else if(data.redirect_cart === "2")
                        { 
                          $('#want_designe_popup').modal("show"); 
                          self.props.dispatch(cartData());
                        }
                        else
                        {   
                          var redirect_cart =  data.redirect_cart.split("-"); 
                          $("#for_detail_page").val('1');    
                          self.props.dispatch(cart_add(redirect_cart[0], redirect_cart[1], 1));
                          self.props.dispatch(SuccessPopupOpen(1));
                          $('.cart_shopping_success').html(data.msg);
                        }
                      }

                      self.props.dispatch(userloginData());
                      self.props.dispatch(wishlistData());
                      self.props.dispatch(getCartData());
                    }
                })
        .catch(function() {
          console.log('data fetch error');
         });

       }

    function send_otp()
    {
          var customer_mobile = custom.getCookie("customer_mobile");
          var country_code    = custom.getCookie("country_code");
          var form_data = '&customer_mobile='+customer_mobile+'&country_code='+country_code;

          var response =self.props.dispatch(password_otp_formData(form_data));
          response.then(function(data){

                   if(data.error)
                   {
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                   } 
                   else
                   {
                     $('.login_password_success').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_password_success').show();
                     $('.login_password_error').hide();
                     $("#collapseverify_1").css('visibility','hidden').hide();
                     $("#collapseverify_2").css('visibility','visible').show();
                     $("#login_form :input[name=password]").val('');
                     $("#login_form :input[name=password]").prev('label').css('margin-top', '20px');
                     $("#verify_otp_forgot_form input[name=otp]").val('');
                     $("#verify_otp_forgot_form input[name=otp]").prev('label').css('margin-top', '20px');
                   }
            })
        .catch(function() {
          console.log('data fetch error');
         });
    }

  function login_with_password()
   {
     $('.login_passworderror, .login_password_success').hide();
     $('#collapseverify_2').css('visibility','hidden').hide();
     $('#collapseverify_1').css('visibility','visible').show();
     $("#verify_otp_forgot_form :input[name=otp]").val('');
     $("#verify_otp_forgot_form :input[name=otp]").prev('label').css('margin-top', '20px');
   }


		return (
       <Dialog
             fullScreen={true}
             open={this.props.LoginPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
             <button type="button" className="popup_close close" onClick={this.login_popup_close}>&times;</button>
            
             <DialogTitle className="address_popup_head popup-title">
             SIGN IN
             </DialogTitle>

             <DialogContent className="modal-body">
                <div className="success_msg login_password_success"></div>
                <div className="danger_msg login_password_error"></div>
                 <div className="mobile_details_panel">
                  {$.isNumeric(custom.getCookie('customer_mobile')) ? 
                   <div>
                     <span className="flag_area"><img src={Api.cdn_url+"static/media/"+custom.getCookie('country_iso_code')} alt="country icon" /></span>
                     <div className="login_mobile_nmr">{custom.getCookie('country_code')+custom.getCookie('customer_mobile')}</div>
                    </div>
                   :
                   <div>
                     <span className="flag_area"></span>
                     <div className="login_mobile_nmr">{custom.getCookie('customer_mobile')} </div>
                    </div>
                  } 

                  <div className="clearfix"></div>
                  <div id="collapseverify_1">
                  <form name="login_form"  id="login_form" onSubmit={login_form}>
                  <input type="password" name="password" className="password_box" placeholder="Password" alt="Password" autoComplete="new-password" />
                  <input type="checkbox" name="login_terms" className="login_terms" id="login_terms" value="1" checked="checked" /> 
                   I agree to the <Link to={Api.folder_path+"i/terms"} target="_blank">terms of service.</Link>
                  <div className="clearfix"></div>
                   <div className="otp_agin pull-left"><span onClick={send_otp}>Login with otp</span></div>
                   <input type="hidden" name="referrers" value=""/>
                   <input type="hidden" name="pre_order" value="0" id="pre_order" /> 
                   <input type="hidden" name="redirect_cart" id="login_redirect_cart" value="0" />
                   
                   <input type="hidden" name="reg_telephone" value=""/>
                   <input type="hidden" name="cart_session_id" value=""/>
                   <input type="hidden" name="wishlist_session_id" value=""/>
                   <input type="hidden" name="country_code" value=""/>
                   <input type="hidden" name="country_iso_code" value=""/>

                   <input type="submit" name="login" value="Continue" className="btn deliver_btn pull-right" id="login" />
                   </form>
                    </div>

                   <div id="collapseverify_2" style={{display: 'none'}}>
                   <form name="verify_otp_forgot_form"  id="verify_otp_forgot_form" onSubmit={verify_otp_forgot_form}>
                   <input type="text" name="otp" className="password_box" placeholder="Please Enter OTP" maxLength="6" alt="OTP" />
                   <input type="checkbox" name="login_terms_otp" className="login_terms" id="login_terms_otp" value="1" checked="checked" />  
                     I agree to the <Link to={Api.folder_path+"i/terms"} target="_blank">terms of service.</Link>

                   <div className="clearfix"></div>
                   <div className="otp_agin pull-left"><a onClick={login_with_password}>Login with password</a>
                    OR
                    <span onClick={send_otp}>Didn't get OTP?</span>
                   </div>
                   <input type="submit" name="verify_otp" value="Verify" className="btn deliver_btn pull-right" id="verify_otp" />
                   <input type="hidden" name="otp_page" value="forgot_password" label="" />
                   <input type="hidden" name="referrers" value="" />
                   <input type="hidden" name="pre_order" value="0" id="pre_order1" /> 
                   <input type="hidden" name="redirect_cart" value="0" id="redirect_cart1" />
                   </form>
                    <div className="clearfix"></div>
                    </div>
                </div>
             </DialogContent>
        </Dialog>
		  )
	 }
 }




function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    LoginPopup: state.loginReducer.LoginPopup,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(password_otp_formData, verify_otp_formData, loginData, userloginData, cartData, wishlistData, getCartData, cart_add, LoginPopupOpen, SuccessPopupOpen, cart_add_with_option)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(LoginForm));