import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';

import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';

import $ from 'jquery'
import custom from '../../custom/custom'
import webengage from '../../custom/webengage'
import {state_listData, pincodeAddressData, registerData, RegisterPopupOpen} from '../../actions/LoginAction';
import { userloginData, cartData, wishlistData } from '../../actions/HeaderAction';
import  './index.css'


class Register extends Component {

   constructor(props)
   {
   	 super(props);

       this.state = {
           fields: {},
           errors: {},
           dropshipper:0,
           register_redirect_cart:''
       }

      this.register_form        = this.register_form.bind(this);
      this.handleValidation     = this.handleValidation.bind(this);
      this.handleChange         = this.handleChange.bind(this); 
      this.registerSubmit       = this.registerSubmit.bind(this);
      this.postcode             = this.postcode.bind(this);
      this.reg_telephone_signup = this.reg_telephone_signup.bind(this);
      this.get_state_list       = this.get_state_list.bind(this); 
      this.register_popup_close = this.register_popup_close.bind(this);

   } 

  register_popup_close()
  {
    this.props.dispatch(RegisterPopupOpen(0));     
  }

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['reg_telephone'] = $("#reg_telephone_signup").val();
        fields['reg_email'] = $("#reg_email_signup").val();

        this.setState({fields});  
        //Name
        if(!fields["name"]){
           formIsValid = false;
           errors["name"] = 'Please provide your name';
        }
        if(typeof fields["name"] !== "undefined"){
             if(!fields["name"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["name"] = "Only letters";
             }
             if(fields["name"].length < 2 || fields["name"].length > 64){
                 formIsValid = false;
                 errors["name"] = 'Name must be between 2 and 64 characters!';
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
        //company
        if(!fields["company"]){
           formIsValid = false;
           errors["company"] = 'Please provide business name';
        }
         if(typeof fields["company"] !== "undefined"){
             if(fields["company"].length < 2 || fields["company"].length > 64){
                 formIsValid = false;
                 errors["company"] = 'Business name must be between 2 and 64 characters!';
             }          
        }
        //gst number
        if(fields["gst_uin_number"] && fields["gst_uin_number"] === 1){
           
           if(!fields["gst_number"]){
            formIsValid = false;
            errors["gst_number"] = 'Please provide valid gst number';
           }
       
        if(typeof fields["gst_number"] !== "undefined"){  

         var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/;
         var code = /([C,P,H,F,A,T,B,L,J,G,E])/;
         var textObj = fields["gst_number"];

         var code_chk = textObj.substring(5,6).toUpperCase();       
         if (textObj!=="") {
          if(reggstin.test(textObj) === false) {
            formIsValid = false;
            errors["gst_number"] = 'Please provide valid gst number';
          }
          if (code.test(code_chk)===false) {
            formIsValid = false;
            errors["gst_number"] = 'Please provide valid gst number';
          }
         } 

       }


        }     
        //postcode
        if(!fields["postcode"]){
           formIsValid = false;
           errors["postcode"] = 'Please enter postcode!';
        } 
        if(typeof fields["postcode"] !== "undefined"){
             if(fields["postcode"].length < 2 || fields["postcode"].length > 10){
                 formIsValid = false;
                 errors["postcode"] = 'Invalid postcode!';
             }          
        } 
        //address
        if(!fields["address_1"]){
           formIsValid = false;
           errors["address_1"] = 'Address must be between 3 and 128 characters!';
        }  
        if(typeof fields["address_1"] !== "undefined"){
             if(fields["address_1"].length < 3 || fields["address_1"].length > 128){
                 formIsValid = false;
                 errors["address_1"] = 'Address must be between 3 and 128 characters!';
             }          
        }  

        //Telephone
        if(typeof fields["reg_telephone"] !== "undefined" && fields["reg_telephone"] !== '')
        {
             if(this.props.international_store === 0 && fields["reg_telephone"].length < 10){
                 formIsValid = false;
                 errors["reg_telephone"] = 'Mobile  does not appear to be valid!';
             } 
             if(this.props.international_store === 1 && fields["reg_telephone"].length < 6){
                 formIsValid = false;
                 errors["reg_telephone"] = 'Mobile  does not appear to be valid!';
             }         
        }
        //Email

        if(typeof fields["reg_email"] !== "undefined" && fields["reg_email"] !== ''){
            let lastAtPos = fields["reg_email"].lastIndexOf('@');
            let lastDotPos = fields["reg_email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["reg_email"].indexOf('@@') === -1 && lastDotPos > 2 && (fields["reg_email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["reg_email"] = 'E-Mail address does not appear to be valid!';
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
        var dropshipper = $("#input-is_dropshipper").val();
        if(dropshipper > 0) { this.setState({ dropshipper: 'dropshipper'}); }
        if(e.target.name === 'postcode') { this.postcode(); }
        if(e.target.name === 'reg_telephone') { this.reg_telephone_signup(); }   
        if(e.target.name === 'gst_number') { $("#gst_number").val(e.target.value.toUpperCase());} 
        if(e.target.name === 'gst_uin_number' &&  e.target.checked === false) { fields[field] = 0;  }   
        else { fields[field] = e.target.value;  }    
        this.setState({fields});
        this.handleValidation();
        
    }

   register_form() {
         let international_store = this.props.international_store;
         $("#register_form input[type=submit]").val('loading...').prop("disabled", true);
         $("#country_id, #zone_id, #city").prop("disabled", false);
         $("#input-is_dropshipper").val(this.state.dropshipper);

        var country_code     = custom.getCookie("country_code");
        var cart_session_id  = custom.getCookie('cart_session_id');
        var wishlist_session_id  = custom.getCookie('wishlist_session_id');

        $("#register_form input[name=country_code]").val(country_code);
        $("#register_form input[name=cart_session_id]").val(cart_session_id);
        $("#register_form input[name=wishlist_session_id]").val(wishlist_session_id);
       
        var response =this.props.dispatch(registerData());
        var self = this;
        response.then(function(data){
                    if(data.error)
                    {
                     $("#register_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_register_error').html('<i class="fa fa-check-circle"></i> '+data.msg);
                     $('.login_register_error').show();
                     $('.login_register_success').hide();
                    }
                    else
                    {
                      webengage.register('Direct Sign up', data, international_store);
                      webengage.login(data);
                      $("#register_form input[type=submit]").val('continue').prop("disabled", false);
                      custom.createCookie("customer_id", data.customer_id, 7);
                      custom.createCookie("customer_access_token", data.customer_access_token, 7);
                      self.props.dispatch(userloginData());
                      self.props.dispatch(cartData());
                      self.props.dispatch(wishlistData());
                      self.props.dispatch(RegisterPopupOpen(0));  
                       //window.location.assign(data['url']);
                       if(data.redirect_cart === "credit")
                        {
                          window.location.href = data.credit_application
                        }
                    }
         })
        .catch(function() {
          console.log('data fetch error');
         });
    }
   
      get_state_list(zone_id=0) {
        $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
        var response =this.props.dispatch(state_listData($("#country_id").val()));

        response.then(function(data){
          $("i.fa-circle-o-notch").remove();
          var result = '<option value="">'+data['zone_title']+'</option>';
              for(var i=0; i<data['zone_data'].length; i++)
                {
                  result = result+'<option value="'+data['zone_data'][i].zone_id+'">'+data['zone_data'][i].name+'</option>';
                }
               $(" #zone_id").html(result);
               if(zone_id > 0) { $("#zone_id option[value=" + zone_id + "]").prop("selected",true); }
         })
        .catch(function() {
          console.log('data fetch error');
         });
       } 


       postcode() {
        var self = this;
        var value = $("#postcode").val();
        if (value && /^\d{6,}$/.test(value.trim()) && value.length === 6)
        {

          var response =self.props.dispatch(pincodeAddressData(value));
          response.then(function(data){
              if(!data['error'])
                  {
                    if(data['zone_id'] === '')
                    {
                      $("#country_id option[value='99'").prop('selected',true);
                      self.get_state_list();
                      $("#city").val('');
                      $("#city").prev('label').css('margin-top', '20px');
                    }
                    else
                    {
                      $("#city").prev('label').css('margin-top', '0px');
                      $("#city").val(data['city']);
                      $("#country_id option[value=" + data['country_id'] + "]").prop("selected",true);
                      self.get_state_list(data['zone_id']);
                    }
                  }
           })
          .catch(function() {
             console.log('data fetch error');
            });
        }
    }

      reg_telephone_signup()
        {
            var reg_telephone = $('#reg_telephone_signup').val();
            if (reg_telephone.charAt(0) === 0 )
            {
              $('#reg_telephone_signup').val(reg_telephone.slice(1));
            }
            if (/\D/g.test(reg_telephone))
            {
             var node = $('#reg_telephone_signup');
             node.val(node.val().replace(/[^0-9]/g,'') );
            }
        }     


	render() {
    
    $("#input-is_dropshipper").val();

    function show_password()
      {
          if($(".show_password").html() === 'Show Password')
            {
                $(".show_password").html('Hide Password');
                $("#register_password").attr('type','text');
            }
          else
           {
               $(".show_password").html('Show Password');
               $("#register_password").attr('type','password');
           }
      }

      $(document).delegate('#gst_uin_number', 'click', function(e){
          if($(this).prop("checked") === true){
           $("#gst_uin_number_area").removeClass("hide");
           }
          else { 
           $("#gst_uin_number_area").addClass("hide");
           $('#gst_number').val('');
           $("#gst_number").prev('label').animate({'margin-top': '20px'});
          }
      }); 

       $(document).delegate('.customer_type_dropshipper_checkbox', 'click', function(e){
         $(".customer_type_checkbox").prop("checked", false);     
      });

      $(document).delegate('.customer_type_checkbox', 'click', function(e){
         $(".customer_type_dropshipper_checkbox").prop("checked", false);     
      }); 


		return (
            <Dialog
             fullScreen={true}
             open={this.props.RegisterPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.register_popup_close} id="signup_popup_close">&times;</button>
            
            <DialogTitle className="address_popup_head popup-title">
             SIGN UP
            </DialogTitle>

            <DialogContent className="modal-body popup_scroll">

            <div className="success_msg login_register_success"></div>
            <div className="danger_msg login_register_error"></div>

            <form name="register_form"  id="register_form" onSubmit={this.registerSubmit.bind(this)}>
            <input type="hidden" name="is_dropshipper" value="" id="input-is_dropshipper" className="form-control" />
            <input type="hidden" name="referrers" value=""/>
             <input type="hidden" name="redirect_cart" id="register_redirect_cart" />    
            <input type="hidden" name="reg_telephone" value=""/>
            <input type="hidden" name="cart_session_id" value=""/>
            <input type="hidden" name="wishlist_session_id" value=""/>
            <input type="hidden" name="country_code" value=""/>

                <div className="mobile_details_panel">
                         <span className="flag_area"><i className="country-flag flagstrap-icon flagstrap-"></i></span>
                         <div className="login_mobile_nmr"></div>
                         <div className="clearfix"></div>
                         <h4 className="modal-title">Please provide following details</h4>
                         <div className="clearfix"></div>

           
            {this.props.customer_type ?
             this.props.customer_type.map((customer_type, index) => { 
              return (<div className="checkbox_information" key={index}>
                  <label>
                  <input type="checkbox" name="customer_type_id[]" className="customer_type_checkbox" value={customer_type.customer_type_id} /> {customer_type.type}
                  <div className="control__indicator"></div>
                  </label>
                  </div>)
            })
             : ''
            }

            <div className="clearfix"></div>
             <p className="middle_title"><span>OR</span></p>
            <div className="clearfix"></div>

            {this.props.CustomersDropshipperType ?
             this.props.CustomersDropshipperType.map((customer_type, index) => { 
              return (<div className="checkbox_information" key={index}>
                  <label>
                  <input type="checkbox" name="customer_type_id[]" className="customer_type_dropshipper_checkbox" value={customer_type.customer_type_id} /> {customer_type.type}
                  <div className="control__indicator"></div>
                  </label>
                  </div>)
            })
             : ''
            }

            <div className="clearfix"></div>
            <div id="reg_email_field">
            <div className="col-sm-12 nopadding">
            <input type="text" name="reg_email" id="reg_email_signup" className="password_box" placeholder="Email Address" alt="Email Address" autoComplete="new-password" onChange={this.handleChange.bind(this, "reg_email")} />
           <span className="error">{this.state.errors["reg_email"]}</span>
           </div>
            <div className="clearfix"></div>
            </div>
            <div  id="reg_telephone_field" className="hide">
             <div className="col-sm-12 nopadding">
            <input type="text" name="reg_telephone" className="password_box" placeholder="Mobile Number" alt="Mobile Number" id="reg_telephone_signup" maxLength="10" onChange={this.handleChange.bind(this, "reg_telephone")} />
             <span className="error">{this.state.errors["reg_telephone"]}</span>
            </div>
            <div className="clearfix"></div>
            </div>
            <div className="col-sm-12 nopadding">
            <input type="text" name="password" className="password_box" placeholder="Password" alt="Password" id="register_password" autoComplete="new-password" onChange={this.handleChange.bind(this, "password")} />
            <div className="show-password">
            <span className="show_password" onClick={show_password}>Hide Password</span>
            </div>
            <span className="error">{this.state.errors["password"]}</span>
            </div>
            <div className="clearfix"></div>
           <div className="col-sm-12 nopadding">
           <input type="text" name="name" className="password_box capitalize" placeholder="Name"  alt="Name" onChange={this.handleChange.bind(this, "name")} />
           <span className="error">{this.state.errors["name"]}</span>
            </div>
            <div className="clearfix"></div>
            {this.props.international_store ?
            <div className="col-sm-12 nopadding">
            <input type="text" name="company" className="password_box" placeholder="Company" alt="Company" onChange={this.handleChange.bind(this, "company")} /> 
            <span className="error">{this.state.errors["company"]}</span>
            </div>
            : <div className="gst_area"><div className="col-sm-6 nopadding">
            <input type="text" name="company" className="password_box" placeholder="Company" alt="Company" onChange={this.handleChange.bind(this, "company")} /> 
            <span className="error">{this.state.errors["company"]}</span>
            </div> 
             <div className="col-sm-6 nopadding">
             <div className="checkbox_information checkbox_information_full">
               <label>
                  <input type="checkbox" name="gst_uin_number" id="gst_uin_number" value="1" onChange={this.handleChange.bind(this, "gst_uin_number")} /> Have you registerd GST Number ?
                  <div className="control__indicator"></div>
                </label>
            </div>
            </div>
             <div className="col-sm-12 nopadding hide" id="gst_uin_number_area">
             <input type="text" name="gst_number" id="gst_number" className="password_box uppercase" placeholder="GST Number" alt="GST Number" onChange={this.handleChange.bind(this, "gst_number")} />
              <span className="error">{this.state.errors["gst_number"]}</span>
             </div>
            </div>
             }
            <div className="clearfix"></div>
             <div className="clearfix"></div>
           <div className="col-sm-6 nopadding">
            <input type="text" name="postcode" onChange={this.handleChange.bind(this, "postcode")} className="password_box" placeholder="Postcode" alt="Postcode" id="postcode" />
            <span className="error">{this.state.errors["postcode"]}</span>
           </div>
           <div className="col-sm-6 nopadding">
            <select name="country_id" className="password_box" id="country_id" onChange={this.get_state_list}>
            <option value="0">Select Country</option>
            {this.props.country_list ?
             this.props.country_list.country_data.map((country, index) => { 
              return (<option key={index} value={country.country_id}>{country.name}</option>)
            })
             : ''
            } 
            </select>
            <span className="error">{this.state.errors["country_id"]}</span>
            </div>
            <div className="clearfix"></div>
            <div className="col-sm-6 nopadding">
            <select name="zone_id" className="password_box" id="zone_id">
            <option value="0">Select Zone</option>
            </select>
            <span className="error">{this.state.errors["zone_id"]}</span>
            </div>
            <div className="col-sm-6 nopadding">
            <input type="text" name="city" className="password_box" placeholder="City" id="city" />
            <span className="error">{this.state.errors["city"]}</span> 
            </div>
            <div className="clearfix"></div>
            <div className="col-sm-12 nopadding">
            <textarea name="address_1" className="password_box" placeholder="Address" alt="Address" onChange={this.handleChange.bind(this, "address_1")}></textarea>
            <span className="error">{this.state.errors["address_1"]}</span> 
            </div>
            <div className="clearfix"></div>
            <input type="submit" name="register" value="Continue" className="btn deliver_btn pull-right" id="register" />
            <div className="clearfix"></div>
                </div>
            </form>
                <div className="clearfix"></div>
          </DialogContent>
        </Dialog>   
		)
	}
}


function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    RegisterPopup: state.loginReducer.RegisterPopup,
    customer_type: state.loginReducer.customer_type.CustomersType,
    CustomersDropshipperType: state.loginReducer.customer_type.CustomersDropshipperType,
    country_list: state.loginReducer.country_list,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(state_listData, pincodeAddressData, registerData, userloginData, cartData, wishlistData, RegisterPopupOpen)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(Register));