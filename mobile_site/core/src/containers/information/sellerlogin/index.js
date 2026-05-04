import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom';
import Helmet from 'react-helmet';
import Api from '../../../api/Api';
import $ from 'jquery'
import {sellerRegisterData, DialogOpen} from '../../../actions/InformationAction';
import SellerAgreement from './seller_agreement';
import  './index.css'

class SellerLogin  extends Component {

constructor(props)
 {
     super(props);
     this.state = {
           fields: {},
           errors: {},
           dropshipper:0
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
      this.props.dispatch(DialogOpen(1));
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
        var self = this;
        $("#seller_form input[type=submit]").val('loading...').prop("disabled", true);
        var form_data = $("#seller_form").serialize();
       var response =this.props.dispatch(sellerRegisterData(form_data));
        response.then(function(data){
                    $("html, body").animate({ scrollTop: 0 }, 800);
                    if(data.errors)
                    {
                     $("#seller_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_register_error').html('<i class="fa fa-check-circle"></i> '+data.errors);
                     $('.login_register_error').show();
                     $('.login_register_success').hide();
                    }
                    else
                    {
                       $('.login_register_success').show();
                       $('.login_register_error').hide();
                       $("#seller_form input[type=text]").val('');
                       $("#seller_form input[type=password]").val('');
                       $("#seller_form input[type=email]").val('');
                       $("#seller_form input[type=number]").val('');
                       self.setState({fields:{}});
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

 render()
 {

    return (
         <div className="contner head_margin side-collapse-container">
         <Helmet title="Seller Register" />
         <SellerAgreement />
         <h2 className="about_title">Seller Register</h2>

         <p style={{margin:'8px 0px 0px 15px'}}><b>Please fill up this form to leave your contact details with us.</b></p>

         <section className="col-xs-12" style={{marginTop:'10px'}}>
             <div className="danger_msg login_register_error"></div>
             <div className="success_msg login_register_success">
              You are successfully registered with us! For Login <br />
              Please download our <a rel="noopener noreferrer" href="https://play.google.com/store/apps/details?id=com.seller&hl=en" target="_blank">Seller App</a> or use our desktop website
             </div>
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
                                   <div className="checkbox_information" style={{background:'none'}}>
                                    <label>
                                     <input type="checkbox" name="terms" value="1" /> I have read and agree to the <a className="agree" onClick={this.dialog_open} alt="Seller Agreement" style={{cursor:'pointer'}}><b>Seller Agreement</b></a>
                                    <div className="control__indicator"></div>
                                    </label>
                                   </div> 
                                </div>
                            </div>

                            <p><b>
                            IMPORTANT - Fill this up only if you have a factory - we only visit the factories and do business from your factory premises and do not deal with wholesalers and traders
                            </b>
                            </p>
                            
                            <div className="form-group">
                                <div className="col-sm-3">&nbsp;</div>
                                <div className="col-sm-8">
                                    <div className="buttons">
                                        <div className="pull-left">
                                           <Link to={Api.folder_path}>
                                            <button type="button" className="btn btn-default" style={{border:'1px solid #efe8e8'}} id="ms-submit-button" value="Cancel">Cancel</button>
                                           </Link>
                                        </div>
                                        <div className="pull-right">
                                            <button type="submit" className="btn btn-primary" id="ms-submit-button" value="Continue">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

          </section>
          
         <div className="clearfix"></div>
       
   </div>)
}}


function mapStateToProps(state){
  return {
   actions: bindActionCreators(sellerRegisterData, DialogOpen)
  };
}

export default connect(mapStateToProps)(SellerLogin);