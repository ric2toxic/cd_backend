import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import {userloginData, HeaderUserloginSuccess} from '../../actions/HeaderAction';
import {registerData} from '../../actions/HomeAction';
import custom from '../../custom/custom'
import SellerAgreement from '../seller_agreement';
import $ from 'jquery'
import  './index.css'

class Home extends Component {
 
 constructor(props)
 {
     super(props);
     this.state = {
           fields: {},
           errors: {},
           dropshipper:0,
           dialog_open:0
       }

     this.register_form        = this.register_form.bind(this);
     this.handleValidation     = this.handleValidation.bind(this); 
     this.handleChange         = this.handleChange.bind(this); 
     this.registerSubmit       = this.registerSubmit.bind(this); 
     this.reg_telephone_signup = this.reg_telephone_signup.bind(this);
     this.dialog_open           = this.dialog_open.bind(this);
 }
     
     dialog_open()
     { 
      var self = this;
       this.setState({dialog_open:1});
        setTimeout(function(){ self.setState({dialog_open:1}); }, 100);
     }

     handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['seller_name']      = $("#seller_name").val();
        fields['seller_email']     = $("#seller_email").val();

        this.setState({fields});  
        //Name
        if(!fields["seller_name"]){
           formIsValid = false;
           errors["seller_name"] = 'Please provide your name';
        }
        if(typeof fields["seller_name"] !== "undefined"){
             if(fields["seller_name"].length < 2 || fields["seller_name"].length > 64){
                 formIsValid = false;
                 errors["seller_name"] = 'Name must be between 2 and 64 characters!';
             }          
        }     
        //Telephone
        if(!fields["seller_telephone"]){
           formIsValid = false;
           errors["seller_telephone"] = 'Mobile must have 10 numbers!';
        }   

        if(typeof fields["seller_telephone"] !== "undefined"){
             if(fields["seller_telephone"].length < 10){
                 formIsValid = false;
                 errors["seller_telephone"] = 'Mobile  does not appear to be valid!';
             } 
        }

        //Email
         if(!fields["seller_email"]){
           formIsValid = false;
           errors["seller_email"] = 'E-Mail address does not appear to be valid!';
        }
        if(typeof fields["seller_email"] !== "undefined" && fields["seller_email"] !== ''){
            let lastAtPos = fields["seller_email"].lastIndexOf('@');
            let lastDotPos = fields["seller_email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["seller_email"].indexOf('@@') === -1 && lastDotPos > 2 && (fields["seller_email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["seller_email"] = 'E-Mail address does not appear to be valid!';
            }
       }

       //Password
        if(!fields["password"]){
           formIsValid = false;
           errors["password"] = 'Password must be between 4 and 20 characters!';
        }
         if(typeof fields["password"] !== "undefined"){
             if(fields["password"].length < 4 || fields["password"].length > 20){
                 formIsValid = false;
                 errors["password"] = 'Password must be between 4 and 20 characters!';
             }          
        } 

       //Confirm Password
        if(!fields["password_confirm"]){
           formIsValid = false;
           errors["password_confirm"] = 'Please confirm your password!';
        }
         if(typeof fields["password_confirm"] !== "undefined"){
             if(fields["password_confirm"] !== fields["password"]){
                 formIsValid = false;
                 errors["password_confirm"] = 'Both password Must be same!';
             }          
        }

       this.setState({errors: errors});
       return formIsValid;
   } 

   registerSubmit(e){
        e.preventDefault();
        if(this.handleValidation()){
          this.register_form();
        }
    }

    handleChange(field, e){  
        let fields = this.state.fields;
        if(e.target.name === 'seller_telephone') { this.reg_telephone_signup(); fields[field] = e.target.value; }   
        else { fields[field] = e.target.value;  }    
        this.setState({fields});
        this.handleValidation();
        
    }

   register_form() {
        $("#seller_form input[type=submit]").val('loading...').prop("disabled", true);
        var form_data = $("#seller_form").serialize();
        var response =registerData(form_data);
        var self = this;
        response.then(function(data){
                    if(data.errors)
                    {
                     $("#seller_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_register_error').html('<i class="fa fa-check-circle"></i> '+data.errors);
                     $('.login_register_error').show();
                     $('.login_register_success').hide();
                    }
                    else
                    {
                      $("#seller_form input[type=submit]").val('continue').prop("disabled", false);
                      custom.createCookie("customer_id", data.customer_id, 7);
                      custom.createCookie("customer_access_token", data.customer_access_token, 7);

                      userloginData()
                      .then(self.props.HeaderUserloginSuccess);

                      $("#home_page").show();
                      $(".register-seller").hide();

                      var url = window.location.href;
                      var final_url = url.split("#");
                      window.history.pushState('', null, final_url[0]+'#/profile/');
                      window.location.reload();
                       
                    }
         })
        .catch(function() {
          console.log('data fetch error');
         });
    } 
  
  reg_telephone_signup()
    {
      var reg_telephone = $('#seller_telephone').val();
      if (reg_telephone.charAt(0) === 0 )
      {
        $('#seller_telephone').val(reg_telephone.slice(1));
      }
      if (/\D/g.test(reg_telephone))
      {
        var node = $('#seller_telephone');
        node.val(node.val().replace(/[^0-9]/g,'') );
      }
    } 


  render(){

    const { user_login } = this.props;

    function register_from_step1(e) {
         e.preventDefault();
         var error = 0;
         var reg_email = $('#email').val();
         var name      = $('#name').val();
         var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
         
         if (name === '')
         {
            error = 1;
            $("#name").css("color", "red");
         }
         else
         {
          $("#name").css("color", "#000");
         } 

         if (!email_pattern.test(reg_email))
         {
            error = 1;
            $("#email").css("color", "red");
         }
         else
         {
          $("#email").css("color", "#000");
         } 

         if(error === 0)
         {
           $("#seller_name").val(name);
           $("#seller_email").val(reg_email);
           $("#home_page").hide();
           $(".register-seller").show();
         }                  
       }
    return (
       <section>
          <SellerAgreement dialog_open={this.state.dialog_open} login_page={1} />
         <div className="container register-seller" style={{display:'none'}}>
          <div className="big-heading"><h1>Register seller account</h1></div>
          <div className="alert alert-danger warning main display_none"></div>
          <div className="row">                
          <div id="content" className="col-sm-12 color_white box_sha">
          <div className="signup-form-wrapper">
                <div className="row contentblock">
                    <div className="col-md-7">
                        <div className="danger_msg login_register_error"></div>
                        <div className="danger_msg login_register_success"></div>

                        <form id="seller_form" className="form-horizontal" onSubmit={this.registerSubmit.bind(this)}>
                        <div className="form-element-part">

                          <div className="form-group required">
                                <label className="col-sm-3 control-label">Company Name</label>
                                <div className="col-sm-8">
                                    <input type="text" name="seller_name" id="seller_name" placeholder="Company Name" className="form-control name" onChange={this.handleChange.bind(this, "seller_name")} />
                                    <span className="error">{this.state.errors["seller_name"]}</span>
                                </div>
                          </div>

                          <div className="form-group required">
                                <label className="col-sm-3 control-label">Mobile</label>
                                <div className="col-sm-8">
                                    <input type="text" name="seller_telephone" id="seller_telephone" placeholder="Mobile" className="form-control name" onChange={this.handleChange.bind(this, "seller_telephone")} />
                                    <span className="error">{this.state.errors["seller_telephone"]}</span>
                                </div>
                          </div>

                          <div className="form-group required">
                                <label className="col-sm-3 control-label">E-Mail</label>
                                <div className="col-sm-8">
                                    <input type="email" name="seller_email" id="seller_email" placeholder="E-Mail" className="form-control email" onChange={this.handleChange.bind(this, "seller_email")} />
                                    <span className="error">{this.state.errors["seller_email"]}</span>
                                </div>
                          </div>

                          <div className="form-group required">
                                <label className="col-sm-3 control-label">Password</label>
                                <div className="col-sm-8">
                                    <input type="password" name="password" placeholder="Password" className="form-control password" onChange={this.handleChange.bind(this, "password")} />
                                    <span className="error">{this.state.errors["password"]}</span>
                                </div>
                            </div>

                            <div className="form-group required">
                                <label className="col-sm-3 control-label">Password Confirm</label>
                                <div className="col-sm-8">
                                    <input type="password" name="password_confirm" placeholder="Password Confirm" className="form-control password" onChange={this.handleChange.bind(this, "password_confirm")} />
                                    <span className="error">{this.state.errors["password_confirm"]}</span>
                                </div>
                            </div>

                            <div className="form-group required">
                                <label className="col-sm-3 control-label">Accept terms</label>
                                <div className="col-sm-8">
                                    <p style={{marginBottom: '0'}}>
                                        <input type="checkbox" name="terms" value="1" />
                                        I have read and agree to the <a className="agree" onClick={this.dialog_open} alt="Seller Agreement" style={{cursor:'pointer'}}><b>Seller Agreement</b></a></p>
                                </div>
                            </div>
                            
                            <div className="form-group">
                                <div className="col-sm-3">&nbsp;</div>
                                <div className="col-sm-8">
                                    <div className="buttons">
                                        <div className="pull-left">
                                            <button type="submit" className="btn btn-primary" id="ms-submit-button" value="Continue">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                    <div className="col-md-4">
                        <div className="calltobox">
                            <h3>Already have an account?</h3>
                                <h4><Link to="/">Sign In</Link> with the login detail you already have.
                                </h4>
                                <p>You can use the email address and password from your existing account.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                 </div>
                </div>
              </div>


       <div id="home_page">
       <div className="image_banner"></div>
       <div className="container nopadding">
        <div className="row">
          <div className="col-sm-12">
          {!user_login ?
            <div className="col-sm-4 float_right register_seller">
              <h3>Register Now </h3>
              <form id="registerform" method="post" onSubmit={register_from_step1}>
                    <fieldset>
                    <input type="text" name="name" id="name" className="in_style in_style_reg" placeholder="Company Name" /><br /><br />
                    <input type="tel" name="email" id="email" className="in_style in_style_reg" placeholder="Email Address" /><br /><br />
                    <input type="submit" name="submit" value="SIGN UP" className="submit_btn_reg" />
                    </fieldset>
                </form>
            </div>
             : ''
           }   

                <div className="col-sm-8 text_slogan">
                    <h1 className="text"> Are you a manufacturer? A factory owner? An importer?</h1>
                    <div className="col-sm-3 text_manufacture text_manufacture1" style={{minWidth: '198px', minHeight: '100px', display: 'block'}}>
                        <i className="fa fa-check-circle li_fa"></i>   No hassle. No Commission                     </div>
                    <div className="col-sm-3 text_manufacture text_manufacture2" style={{minWidth: '198px', minHeight: '100px', display: 'block'}}>
                        <i className="fa fa-check-circle li_fa"></i> Grow your business using our platform</div>
                    <div className="col-sm-3 text_manufacture text_manufacture3" style={{minWidth: '198px', minHeight: '100px', display: 'block'}}>
                        <i className="fa fa-check-circle li_fa"></i> Sell on wholesale basis at Wholesalebox</div>
                    <h2 className="color_white_font">Are you willing to sell on lowest possible margins? <br />  If yes, get bulk orders on regular basis</h2>
                </div>
           </div>

            <div className="main_round" id="main_benefit">
                <h1 className="text text_hts">Benefits in Doing Business with Wholesalebox</h1>
                 <div className="col-sm-4 round_ben round_ben_1">
                 <div className="round_ben_in"> Grow your business using our platform</div>
                 </div>

                <div className="col-sm-4 round_ben round_ben_2">
                <div className="round_ben_in">
                Sell your products on lowest wholesale rates to achieve maximum sales</div>
               </div>

                <div className="col-sm-4 round_ben round_ben_3">
                  <div className="round_ben_in">
                    (No hassle. No Commission ) We do not charge any commission.</div>
                  </div>
                  <div className="col-sm-4 round_ben round_ben_4">
                    <div className="round_ben_in">Shipping and payment responsibility is ours</div>
                  </div>

                  <div className="col-sm-4 round_ben round_ben_5">
                    <div className="round_ben_in">Complete Marketing Support</div>
                  </div>

                  <div className="col-sm-4 round_ben round_ben_6">
                   <div className="round_ben_in">
                     Total Support = Sales + Marketing + Delivery + Payment Collection + Customer Support</div>
                    </div>
                 </div>
              </div>
              </div>
              </div>

        <div className="main_htosell" id="htosell">
         <h2 className="main_round text">How To Sell?</h2>
          <div className="boxes_text container">
              <div className="col-sm-4 text_how">List your products on Wholesalebox.in completely free of cost.</div>
              <div className="col-sm-4 text_how">Customer buys the products on wholesalebox.in and It should be Shopkeeper.</div>
              <div className="col-sm-4 text_how">We collect the items from you and ship them to the customer.</div>
              <div className="col-sm-4 text_how">You get paid for the order</div>
          </div>
          </div>
         
        </section>

             )
 
  }
}


const mapStateToProps = state => ({ user_login: state.user_login });

export default connect(mapStateToProps, {
    HeaderUserloginSuccess:HeaderUserloginSuccess
})(Home);
