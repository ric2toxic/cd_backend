import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import Api from '../../../api/Api';
import $ from 'jquery'
import  './index.css'
import ReactFlagsSelect from 'react-flags-select';
import 'react-flags-select/css/react-flags-select.css';

import { update_number_Otp_Form, update_number_Verify_Otp_Form} from '../../../actions/AccountAction';
import {UpdatePopupOpen, userDetailData } from '../../../actions/AccountAction';

class UpdatePopup  extends Component {

  constructor(props)
  {
     super(props);
      this.update_popup_close  = this.update_popup_close.bind(this);
  }

  update_popup_close()
  {
    this.props.dispatch(UpdatePopupOpen(0));     
  }

render() {

     let self = this;
     let international_store = this.props.international_store;

     function opt_form(e) {
         $("#otp_form input[type=submit]").val('loading...').prop("disabled", true);
         e.preventDefault();

         var country_code      = $(".selected--flag--option").children("span").children("span").html();
         var country_iso_code  = $(".selected--flag--option").children("span").children("img").attr('src');
         country_iso_code      = country_iso_code.split("/");
         country_iso_code      = country_iso_code[country_iso_code.length-1];

          var form    = $('#otp_form');
          var formData = new FormData();
          var params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });
          formData.append('country_iso_code', country_iso_code);
          formData.append('country_code', country_code);
          formData.append('update_telephone',  $("#otp_form input[name=reg_telephone]").val());
 
         var response =self.props.dispatch(update_number_Otp_Form(formData));
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
                      $("#verify_otp_form input[name=reg_telephone]").val(data.reg_telephone);
                      $("#verify_otp_form input[name=country_code]").val(data.country_code);
                      $("#verify_otp_form input[name=country_iso_code]").val(data.country_iso_code);

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

                        $('.login_mobile_error').hide();
                        $('.login_mobile_success').html('<i class="fa fa-check-circle"></i> '+data.msg);
                        $('.login_mobile_success').show();
                        $("#save_reg_telephone, #send_otp_title").hide();
                        $("#edit_reg_telephone").show();
                        $("#sign_in_title").hide();
                        $("#collapseverify").css('visibility','visible').show();
                    }

         })
        .catch(function() {
          console.log('data fetch error');
         });
       }

      function send_otp_agin(e) {
           $('.login_mobile_success, .login_mobile_error').hide();
           $("#verify_otp_form input[name=otp]").val('');
           e.preventDefault();
           opt_form(e);
      };

      function verify_otp_form(e) {
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
          var form    = $('#verify_otp_form');
          var formData = new FormData();
          var params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });

          self.props.dispatch(update_number_Verify_Otp_Form(formData)).then(function(response) {
                    var data = response;
                    if(data.error)
                    {
                     $("#verify_otp_form :input[name=otp]").val('');
                     $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                    }
                    else
                    {  
                       self.props.dispatch(userDetailData());
                       self.props.dispatch(UpdatePopupOpen(0));
                    }

           })
        .catch(function() {
          console.log('data fetch error');
         });
       }


     function check_reg_telephone()
      {
          var mobile        = $('#reg_telephone').val();
          var country_code  = $(".selected--flag--option").children("span").children("span").html();
          var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
          var mobile_pattern = new RegExp(/^\d{10}$/);

         $(".flagstrap").hide();
          if(international_store === 1)
          {
                if (mobile !== '' && /\D/g.test(mobile))
                 {
                   $(".flagstrap").hide();
                 }

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
                if(country_code !== '+91')
                {
                   mobile_pattern = new RegExp(/^\d{7,10}$/);
                } 

                if (mobile !== '' && !/\D/g.test(mobile))
                 {
                   $(".flagstrap").show();
                 }

                if (email_pattern.test(mobile))
                 {
                   $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                 }
                else if (mobile_pattern.test(mobile))
                 {
                   if (mobile.charAt(0) !== 0 && country_code === '+91')
                    {
                       $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                    }
                   if (country_code !== '+91')
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

		return (
           <Dialog
             fullScreen={true}
             open={this.props.UpdatePopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.update_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Update Account
            </DialogTitle>

            <DialogContent className="modal-body">
            <div className="success_msg login_mobile_success"></div>
             <div className="danger_msg login_mobile_error"></div>
             <div className="otp_model">
            <form name="otp_form"  id="otp_form" onSubmit={opt_form}>
               <h4 id="sign_in_title">Please enter your email address or mobile number</h4>
               <div className="mobile_details_panel" id="edit_reg_telephone">
               <span className="flag_area"></span>
                 <div className="login_mobile_nmr"> </div> &nbsp;
                   <span onClick={() => edit_register_no() }><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</span>
               </div>

                <div className="mobile_details_panel" id="save_reg_telephone">
                 
                  <span className="flagstrap">
                    <ReactFlagsSelect
                       defaultCountry="IN"
                       countries={["IN"]}
                       customLabels={{"IN":"+91"}}
                        />
                   </span>

                    <input type="text" onKeyUp={() => check_reg_telephone() } onChange={() => check_reg_telephone() } name="reg_telephone" className="mobile_input" placeholder="Mobile or Email Address" alt="Mobile or Email Address" id="reg_telephone" autoComplete="off" autoFocus="autofocus" /> 
                    <input type="submit" name="send_otp" value="Continue" className="btn deliver_btn pull-right" id="send_otp" disabled="disabled" />
                    <div className="clearfix"></div>
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
              <input type="hidden" name="reg_telephone" />            
              <input type="hidden" name="country_code" />            
              <input type="hidden" name="country_iso_code" />            
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
    UpdatePopup:state.accountReducer.UpdatePopup,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(UpdatePopupOpen, userDetailData)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(UpdatePopup));