import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import $ from 'jquery'
import {loginData, userloginData, sendOTP, verifyOtp, HeaderUserloginSuccess} from '../../actions/HeaderAction';
import Api from '../../api/Api'
import custom from '../../custom/custom'
import Loginmenu from './loginmenu'
import Anchor from '../../component/anchor'
import  './index.css'


class Header  extends Component {

 constructor(props)
 {
     super(props);
     this.logout = this.logout.bind(this);
 }  

 componentDidMount()
 {
     $("#scrollto_ben").click(function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $("#main_benefit").offset().top
            }, 1000);
        });

        $("#scrollto_htosell").click(function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $("#htosell").offset().top
            }, 1000);
        });
 }

logout()
 {
    custom.delete_cookie('customer_id');
    custom.delete_cookie('customer_access_token');
    custom.delete_cookie('customer_mobile');
    custom.delete_cookie('register_user');
    custom.delete_cookie('country_code');
    custom.delete_cookie('country_iso_code');
    custom.delete_cookie('cart_session_id');
    
     userloginData()
    .then(this.props.HeaderUserloginSuccess);
 } 


 render(){
 const { user_login } = this.props;
        //let self = this;

     function opt_form(e) {
         $("#otp_form input[type=submit]").val('loading...').prop("disabled", true);
         e.preventDefault();
         var form_data         = $("#otp_form").serialize();
         var response =sendOTP(form_data);
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
                      $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                      $('.login_mobile_success').html('<i class="fa fa-check-circle"></i> '+data.msg);
                      $('.login_mobile_success').show();
                      $('.login_mobile_error').hide();
                      $(".login_mobile_nmr").html(data.reg_telephone);
                      $("#reg_telephone_verify").val(data.reg_telephone);
                      $("#edit_reg_telephone").show();
                      $("#save_reg_telephone, #send_otp_title").hide();
                      $("#collapseverify").css('visibility','visible').show();
                    }

         })
        .catch(function() {
          console.log('data fetch error');
         });
       }

        function verify_otp_form(e) 
        {        
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
          var form_data = $("#verify_otp_form").serialize();
          var response =verifyOtp(form_data);
          response.then(function(data){
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
                      custom.createCookie("customer_id", data.customer_id, 7);
                      custom.createCookie("customer_access_token", data.customer_access_token, 7);
                     
                      var url = window.location.href;
                      var final_url = url.split("#");
                      window.history.pushState('', null, final_url[0]+'#/orders/getPickpupOrderRequested/');
                      window.location.reload();

                    }
            })
        .catch(function() {
          console.log('data fetch error');
         });

       }

        function login_form(e) 
        { 
         e.preventDefault();
        $("#login_form input[type=submit]").val('loading...').prop("disabled", true);
         var form_data = $("#login_form").serialize();
         $("#login_form :input").prop("disabled", true);
        
         var response = loginData(form_data);
             response.then(function(data){
               $("#login_form :input").prop("disabled", false);
               $("#login_form input[type=submit]").val('LOG IN').prop("disabled", false);
                  if(data.error)
                    {
                     $('.login_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_error').show();
                    }
                    else
                    {
                      $('.login_error').hide();
                      custom.createCookie("customer_id", data.customer_id, 7);
                      custom.createCookie("customer_access_token", data.customer_access_token, 7);
                      var url = window.location.href;
                      var final_url = url.split("#");
                      window.history.pushState('', null, final_url[0]+'#/orders/getPickpupOrderRequested/');
                      window.location.reload();
                    }
                })
                .catch(function() {
                   console.log('data fetch error');
                });
       }
    

function check_reg_telephone(){
    var reg_email = $('#reg_telephone').val();
    var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
            
    if (email_pattern.test(reg_email))
    {
        $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
    }                   
    else
    {
       $("#otp_form input[type=submit]").val('continue').prop("disabled", true);
    }
}

function edit_register_no(){
    $("#edit_reg_telephone").hide();
    $("#save_reg_telephone, #send_otp_title").show();
    $('.login_mobile_success, .login_mobile_error').hide(500);
    $("#verify_otp_form :input[name=otp]").val('');
    $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
    $("#collapseverify").hide();
}

function send_otp_agin(e) {
  $('.login_mobile_success, .login_mobile_error').hide();
  $("#verify_otp_form input[name=otp]").val('');
  $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px');
  e.preventDefault();
  opt_form(e);
};       

    return (
      <header className="color_white height_header">
       <div className="container">
       
       <div className="row">
       {user_login && user_login.logged ?

         <Loginmenu userlogin={user_login.logged} logout={this.logout} /> 
        :
         <div className="seller_head col-sm-12">
          <div className="col-sm-4 logo">
           <Link to={Api.folder_path}> <img src="http://cdnimages.net/img/logo_seller.png" width="296px" height="60px" alt="wholesalebox" /></Link>
            <div className="seller_hub">Manufacturer Hub</div>
          </div>
              
          <div className="col-sm-8 float_right login_seller">
             <div className="danger_msg login_error" style={{marginBottom:'0px'}}></div>
                <form className="form_login float_right" id="login_form"  onSubmit={login_form}>
                <label className="lable_seller">Manufacturer Login</label><br />
                <input type="text" name="email" className="in_style" placeholder="Email Address" />
                <input type="hidden" name="form_button" value="log-button" />
                <input type="password" name="password" className="in_style" placeholder="Password" />
                <input type="submit" name="submit" value="LOG IN" className="submit_btn log_bt" />
               <div className="clearfix"></div>
                <div className="otp_agin pull-left">
                  <Anchor icon="" title="Forgot Password" id="login_link_header" dataToggle="modal" dataTarget="#login_verify_popup" />
                </div>
               </form>
            </div>
         </div>
          }
         <div className="col-sm-6"> 
           <div className="col-sm-12">
              <ul className="menu_bar list-unstyled">
              <li><a href={Api.api_url}> Wholesalebox.in</a></li>
              <li className="ben_li"><Link to="#" id="scrollto_ben"> Benefits</Link></li>
              <li className="ben_li"><Link to="#" id="scrollto_htosell"> How to sell</Link></li>
              </ul>
           </div>
         </div> 
       </div>
    </div>

  <div className="modal fade add_new_address" id="login_verify_popup" role="dialog">
        <div className="modal-dialog login_register_popup" style={{width:'500px'}}>
          <div className="modal-content">
            <div className="modal-header address_popup_head">
              <button type="button" className="close" data-dismiss="modal">&times;</button>
              <h4 className="modal-title" id="send_otp_title">Please enter your Email address</h4>
            </div>
            <div className="modal-body">
            <div className="success_msg login_mobile_success" style={{display: 'none'}}></div>
             <div className="danger_msg login_mobile_error" style={{display: 'none'}}></div>
             <div className="otp_model">
              <form name="otp_form" id="otp_form" onSubmit={opt_form}>
               <div className="mobile_details_panel" id="edit_reg_telephone" style={{display: 'none'}}>
                 <div className="login_mobile_nmr"></div> &nbsp;
                   <a onClick={edit_register_no}><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
               </div>

                <div className="mobile_details_panel" id="save_reg_telephone">
                    <input type="text" name="reg_telephone" className="mobile_input" placeholder="E-Mail Address" alt="E-Mail Address" id="reg_telephone" onKeyUp={() => check_reg_telephone() } onChange={() => check_reg_telephone() } />
                    <input type="submit" name="send_otp" value="continue" className="submit_btn log_bt pull-right" id="send_otp" disabled />
                     <div className="clearfix"></div>
                </div>
              </form>

            <form name="verify_otp_form" id="verify_otp_form" onSubmit={verify_otp_form}> 
                <div id="collapseverify" className="panel-collapse collapse">
                 <input type="text" name="otp" className="password_box" placeholder="Please Enter OTP" maxLength="6" alt="OTP" />
                 <input type="submit" name="verify_otp" value="Verify" className="submit_btn log_bt pull-right" id="verify_otp" />
                <div className="clearfix"></div>
                <div className="otp_agin"><a onClick={send_otp_agin} className="send_otp_agin">Didn't get OTP?</a></div>
                 </div>
                <input type="hidden" name="reg_telephone" id="reg_telephone_verify" />
            </form>
           </div>
            </div>
          </div>
        </div>
   </div>

   </header>
    )
  }
}

const mapStateToProps = state => ({ user_login: state.user_login });

export default connect(mapStateToProps, {
    HeaderUserloginSuccess:HeaderUserloginSuccess
})(Header);