import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import Api from '../../api/Api'

import $ from 'jquery'
import custom from '../../custom/custom'
import  './index.css'
import { opt_formData, verify_otp_formData, OptPopupOpen, LoginPopupOpen, RegisterPopupOpen, SuccessPopupOpen} from '../../actions/LoginAction';
import { cart_add, cart_add_with_option } from '../../actions/CartAction'

import ReactFlagsSelect from 'react-flags-select';
import 'react-flags-select/css/react-flags-select.css';

class Otpform  extends Component {

  constructor(props)
  {
     super(props);
      this.opt_popup_close     = this.opt_popup_close.bind(this);
  }

  opt_popup_close()
  {
    this.props.dispatch(OptPopupOpen(0));     
  }

render() {

        let self = this;
        let international_store = this.props.international_store;

     function opt_form(e) {
         $("#otp_form input[type=submit]").val('loading...').prop("disabled", true);
         e.preventDefault();
         var form_data         = $("#otp_form").serialize();
         var customer_mobile   = custom.getCookie("customer_mobile");
         var reg_telephone     = $('#otp_form').find('input[name="reg_telephone"]').val(); 
         var country_code      = $(".selected--flag--option").children("span").children("span").html();
         var country_iso_code  = $(".selected--flag--option").children("span").children("img").attr('src');
         country_iso_code      = country_iso_code.split("/");
         country_iso_code      = country_iso_code[country_iso_code.length-1];
          var user_verify      = 0;
         if(reg_telephone === customer_mobile) {  user_verify = 1; } else {  user_verify = 0; } 
         form_data    = form_data+'&country_code='+country_code+'&country_iso_code='+country_iso_code+'&user_verify='+user_verify;
       
         var response =self.props.dispatch(opt_formData(form_data));
         response.then(function(data) {
              if(data.error)
                    {
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                     $("#collapseverify").css('visibility','hidden').hide();
                     $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                    }
              else
                    {
                       var redirect_cart = $("#redirect_cart").val();
                      if(data.new_user)
                         {
                           $("#verify_otp_form input[name=reg_telephone]").val(data.reg_telephone);
                           $("#verify_otp_form input[name=country_code]").val(data.country_code);
                           $("#verify_otp_form input[name=country_iso_code]").val(data.country_iso_code);

                           if($.isNumeric( data.reg_telephone ))
                          {
                           $(".login_mobile_nmr").html(data.country_code+' '+data.reg_telephone);
                           $(".flag_area").html('<img src='+Api.cdn_url+'"static/media/'+country_iso_code+'" />');
                           $(".flag_area").show();
                          }
                          else
                          {
                            $(".login_mobile_nmr").html(data.reg_telephone);
                          }

                           $('.login_mobile_error').hide();
                           if(!data.user_verify)
                           {
                             $('.login_mobile_success').html('<i class="fa fa-check-circle"></i> '+data.msg);
                             $('.login_mobile_success').show();
                             $("#save_reg_telephone, #send_otp_title").hide();
                             $("#edit_reg_telephone").show();
                             $("#sign_in_title").hide();
                             $("#collapseverify").css('visibility','visible').show();
                           }
                           else
                           {
                             $("#collapseverify").css('visibility','hidden').hide();
                             self.props.dispatch(OptPopupOpen(0));
                             self.props.dispatch(RegisterPopupOpen(1));
                             if($.isNumeric( data.reg_telephone ))
                              { 
                                 $("#reg_telephone_field").addClass("hide"); 
                                 $("#reg_email_field").removeClass("hide"); 
                                 $("#reg_telephone_signup").val(data.reg_telephone);
                                 $(".login_mobile_nmr").html(data.country_code+' '+data.reg_telephone);
                                 $(".flag_area").html('<img src="'+Api.cdn_url+'static/media/'+data.country_iso_code+'" />');
                                 $(".flag_area").show(); 
                              }
                            else 
                              { 
                                 $("#reg_telephone_field").removeClass("hide"); 
                                 $("#reg_email_field").addClass("hide");
                                 $("#reg_email_signup").val(data.reg_telephone);
                                 $(".login_mobile_nmr").html(data.reg_telephone);
                              }
                               setTimeout(function()
                                 {$("#register_redirect_cart").val(redirect_cart); }, 1000);
                           }
                         }
                         else
                         {
                          
                           self.props.dispatch(OptPopupOpen(0));
                           
                           custom.createCookie("customer_mobile", data.reg_telephone, 7);
                           custom.createCookie("register_user", 1, 7);
                           custom.createCookie("country_code", data.country_code, 7);
                           custom.createCookie("country_iso_code", country_iso_code, 7); 
                          
                          self.props.dispatch(LoginPopupOpen(1)); 
                           if($.isNumeric( data.reg_telephone ))
                              {
                               $(".login_mobile_nmr").html(data.country_code+' '+data.reg_telephone);
                               $(".flag_area").html('<img src="'+Api.cdn_url+'static/media/'+country_iso_code+'" />');
                               $(".flag_area").show();
                               }
                              else
                               {
                               $(".login_mobile_nmr").html(data.reg_telephone);
                                }
                          setTimeout(function()
                            {$("#redirect_cart1, #login_redirect_cart").val(redirect_cart);}, 1000);
                         }
                    }

         })
        .catch(function() {
          console.log('data fetch error');
         });
       }

      function send_otp_agin(e) {
           $('.login_mobile_success, .login_mobile_error').hide();
           $("#verify_otp_form input[name=otp]").val('');
           $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px');
           e.preventDefault();
           opt_form(e);
      };

        function verify_otp_form(e) {
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
         var form_data = $("#verify_otp_form").serialize();

          self.props.dispatch(verify_otp_formData(form_data)).then(function(response) {
                    var data = response;
                    console.log(data);
                    $("#verify_otp_form :input[name=otp]").val('');
                    if(data.error)
                    {
                     $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                    }
                    else
                    {
                        custom.createCookie("customer_mobile", data.reg_telephone, 7);
                        custom.createCookie("register_user", 0, 7);
                        custom.createCookie("country_code", data.country_code, 7);
                        custom.createCookie("country_iso_code", data.country_iso_code, 7);

                       if(data.redirect_cart=== "0" || data.redirect_cart=== "credit")
                       {
                        $('.login_mobile_success, .login_mobile_error').hide();
                        $("#collapseverify").css('visibility','hidden').hide();
                        self.props.dispatch(OptPopupOpen(0));
                        self.props.dispatch(LoginPopupOpen(0));
                        self.props.dispatch(RegisterPopupOpen(1));

                        setTimeout(function()
                            {$("#register_redirect_cart").val(data.redirect_cart); }, 1000);

                          if($.isNumeric( data.reg_telephone ))
                              { 
                                 $("#reg_telephone_field").addClass("hide"); 
                                 $("#reg_email_field").removeClass("hide");
                                 $("#reg_telephone_signup").val(data.reg_telephone); 
                                 $(".login_mobile_nmr").html(data.country_code+' '+data.reg_telephone);
                                 $(".flag_area").html('<img src="'+Api.cdn_url+'static/media/'+data.country_iso_code+'" />');
                                 $(".flag_area").show();
                              }
                            else 
                              { 
                                 $("#reg_telephone_field").removeClass("hide");
                                 $("#reg_email_field").addClass("hide");
                                 $("#reg_email_signup").val(data.reg_telephone);
                                 $(".login_mobile_nmr").html(data.reg_telephone);
                              }
                      }
                      else if(data.redirect_cart === "2")
                      { 
                          self.props.dispatch(OptPopupOpen(0));
                          $('#want_designe_popup').modal("show"); 
                      }
                      else
                      {
                        self.props.dispatch(OptPopupOpen(0));

                        if(data.redirect_cart === "1")
                        {
                          $("#for_detail_page").val('1'); 
                          self.props.dispatch(cart_add_with_option());
                          self.props.dispatch(SuccessPopupOpen(1));
                          $('.cart_shopping_success').html(data.msg);
                          $('.cart_shopping_success').show();
                        }
                        else
                        {
                          $("#for_detail_page").val('1');
                          var redirect_cart =  data.redirect_cart.split("-");
                          self.props.dispatch(cart_add(redirect_cart[0], redirect_cart[1], 1));
                          self.props.dispatch(SuccessPopupOpen(1));
                          $('.cart_shopping_success').html(data.msg);
                          $('.cart_shopping_success').show();
                        }

                      }
                    }

           })
        .catch(function() {
          console.log('data fetch error');
         });
       }


     function check_reg_telephone()
      {
          var mobile       = $('#reg_telephone').val();
          if(international_store === 1)
          {
               var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
               if (email_pattern.test(mobile))
                {
                  $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                }
                else
                {
                   $("#otp_form input[type=submit]").val('continue').prop("disabled", true);
                }
          }
          else
          {
               var mobile_pattern = new RegExp(/^\d{10}$/);
               if (mobile_pattern.test(mobile))
                {
                    if (mobile.charAt(0) !== 0)
                    {
                       $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                    }
                }    
               else
                {
                   $("#otp_form input[type=submit]").val('continue').prop("disabled", true);
                }
            }    
        }
 
    function edit_register_no()
    {
        $("#edit_reg_telephone").hide();
        $("#sign_in_title").show();
        $("#save_reg_telephone, #send_otp_title").show();
        $('.login_mobile_success, .login_mobile_error').hide(500);
        $("#verify_otp_form :input[name=otp]").val('');
        $("#collapseverify").hide();
        check_reg_telephone();
    }       

     $(document).delegate('#login_verify_popup_open, #login_link_header, #footer-login, #button-cart, #dropshipper_login_link, #wishlist-total, .addtocart_bottem, .addtocart_new, .button-cart-list, .productInfoAjax, #place_order_button, #credit_banner', 'click', function(e)
     {       
           if($(this).attr('id') === 'dropshipper_login_link') { $('#input-is_dropshipper').val(2); } 
           else { $('#input-is_dropshipper').val(0); } 
           //$('#select_country').flagStrap({countries:cc}); 
           $('.login_mobile_success, .login_mobile_error').hide();
           $('.login_password_success, .login_password_error').hide();
           $('.login_register_success, .login_register_error').hide();
           $("#collapseverify").css('visibility','hidden').hide();
           $("#edit_reg_telephone").hide();
           $("#sign_in_title").show();
           $("#save_reg_telephone, #send_otp_title").show(3);
           $("#register_form input[type=submit]").val('continue').prop("disabled", false);
           $("#login_form input[type=submit]").val('continue').prop("disabled", false);
           $("#verify_otp_forgot_form input[type=submit]").val('verify').prop("disabled", false);
           $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
           $('#register_form').trigger("reset");
           $("label.error").remove();
           $("span.error").html("");
           if(custom.getCookie("customer_mobile") !== '')
           {
              $(".login_mobile_nmr").html(custom.getCookie("country_code")+' '+custom.getCookie("customer_mobile"));
              $("#reg_telephone").val(custom.getCookie("customer_mobile"));
           }
          check_reg_telephone();
      });


		return (
           <Dialog
             fullScreen={true}
             open={this.props.OptPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" data-dismiss="modal" onClick={this.opt_popup_close} id="login_verify_popup_close">&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              SIGN IN
            </DialogTitle>

            <DialogContent className="modal-body">
            <div className="success_msg login_mobile_success"></div>
             <div className="danger_msg login_mobile_error"></div>
             <div className="otp_model">
            <form name="otp_form"  id="otp_form" onSubmit={opt_form}>
              {this.props.international_store ?
               <h4 id="sign_in_title">Please enter your email address</h4>
               :
               <h4 id="sign_in_title">Please enter your mobile number</h4>
              }
               <div className="mobile_details_panel" id="edit_reg_telephone">
               <span className="flag_area"></span>
                 <div className="login_mobile_nmr"> </div> &nbsp;
                   <span onClick={() => edit_register_no() }><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</span>
               </div>

                <div className="mobile_details_panel" id="save_reg_telephone">
                  
                  {this.props.international_store ?
                    ''
                    :
                    <ReactFlagsSelect
                       defaultCountry="IN"
                       countries={["IN"]}
                       customLabels={{"IN":"+91"}}
                        />
                   }     
                   
                    {this.props.international_store ?
                     <input type="text" onKeyUp={() => check_reg_telephone() } onChange={() => check_reg_telephone() } name="reg_telephone" className="otp_input" placeholder="Email" alt="Email" id="reg_telephone" autoComplete="off" autoFocus="autofocus" /> 
                     : 
                    <input type="text" onKeyUp={() => check_reg_telephone() } onChange={() => check_reg_telephone() } name="reg_telephone" className="mobile_input" placeholder="Mobile" alt="Mobile" id="reg_telephone" autoComplete="off" autoFocus="autofocus" /> 
                     }
                    
                    <input type="submit" name="send_otp" value="Continue" className="btn deliver_btn pull-right" id="send_otp" disabled="disabled" />
                    <div className="clearfix"></div>
                     
                     

                    {!this.props.international_store ?
                      <div className="seller_login">
                      <p className="middle_title"><span>OR</span></p>
                      <Link to={Api.folder_path+"i/seller_login"} onClick={this.opt_popup_close}>Manufacturer ? Register here</Link>
                      </div>
                     : ''} 
                   
                </div>
            </form>
            
              <form name="verify_otp_form"  id="verify_otp_form" onSubmit={verify_otp_form}>                
              <div id="collapseverify" className="panel-collapse collapse">
              <input type="text" name="otp" className="password_box" placeholder="Please Enter OTP" maxLength="6" alt="OTP" />                 
              <div className="clearfix"></div>
              <br />
              <input type="submit" name="verify_otp" value="Verify" className="btn deliver_btn pull-right" id="verify_otp1" />               
              <div className="otp_agin"><span onClick={send_otp_agin} className="send_otp_agin">Didn't get OTP?</span></div>
              </div>
              <input type="hidden" name="otp_page" value="sign_up" label="" />           
              <input type="hidden" name="reg_telephone" label="" />            
              <input type="hidden" name="country_code" label="" />            
              <input type="hidden" name="country_iso_code" label="" />            
              <input type="hidden" name="redirect_cart" value="0" id="redirect_cart" />          
               </form>          
             </div>

          </DialogContent>
        </Dialog>
		)
	}
}


function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    OptPopup: state.loginReducer.OptPopup,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(opt_formData, verify_otp_formData, cart_add, OptPopupOpen, LoginPopupOpen, RegisterPopupOpen, SuccessPopupOpen, cart_add_with_option)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(Otpform));