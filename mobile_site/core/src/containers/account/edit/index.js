import React, { Component } from 'react'
import $ from 'jquery'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { Link } from 'react-router-dom'
import  './index.css'
import { userDetailData, userProfileUpdate, UpdatePopupOpen } from '../../../actions/AccountAction'
import Anchor from '../../../component/anchor'
import Api from '../../../api/Api'
import UpdatePopup from './update_popup'

class Edit  extends Component {

  constructor(props) {
    super(props);
    //this.props.user_detail.firstname='';
    this.state = {
          firstname:'',
          lastname:'',
          email:'',
          telephone:'',
          gst_number:'',
          fax:'',
          fields: {},
          errors: {},
          api_data:false
    };

    this.handleChange     = this.handleChange.bind(this);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
    this.valueChange      =this.valueChange.bind(this);
    this.update_popup     = this.update_popup.bind(this);
    this.handleValidation = this.handleValidation.bind(this);
  }

  componentWillMount()
  {  
    this.props.dispatch(userDetailData());
  } 

 componentDidUpdate(prevProps)
  {
      if(this.props.user_detail!==prevProps.user_detail)
      {
        this.valueChange();
      }
  }

componentWillReceiveProps()
{
    this.valueChange();
}

valueChange()
 {
      this.setState({firstname:this.props.user_detail.firstname});
      this.setState({lastname:this.props.user_detail.lastname});
      this.setState({email:this.props.user_detail.email});
      this.setState({telephone:this.props.user_detail.telephone});
      this.setState({gst_number:this.props.user_detail.gst_number});
      this.setState({fax:this.props.user_detail.fax});
      this.setState({api_data:true});
      
      let fields = this.state.fields; 
      fields['firstname']     = this.props.user_detail.firstname;
      fields['lastname']      = this.props.user_detail.lastname;
      fields['email']         = this.props.user_detail.email;
      fields['telephone']     = this.props.user_detail.telephone;
      fields['gst_number']    = this.props.user_detail.gst_number;
      this.setState({fields}); 
  } 

handleValidation(){
        let fields = this.state.fields; 
        let errors = {};
        let formIsValid = true;
        //Frist Name
        if(!fields["firstname"]){
           formIsValid = false;
           errors["firstname"] = 'Please provide your first name';
        }
        if(typeof fields["firstname"] !== "undefined"){
             if(!fields["firstname"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["firstname"] = "Only letters";
             }
             if(fields["firstname"].length < 2 || fields["firstname"].length > 64){
                 formIsValid = false;
                 errors["firstname"] = 'Name must be between 2 and 64 characters!';
             }          
        } 

        //Frist Name
        if(!fields["lastname"]){
           formIsValid = false;
           errors["lastname"] = 'Please provide your last name';
        }
        if(typeof fields["lastname"] !== "undefined"){
             if(!fields["lastname"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["lastname"] = "Only letters";
             }
             if(fields["lastname"].length < 2 || fields["lastname"].length > 64){
                 formIsValid = false;
                 errors["lastname"] = 'Name must be between 2 and 64 characters!';
             }          
        }

        //gst number
        if(typeof fields["gst_number"] !== "undefined" && fields["gst_number"] !== '')
        {  
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

        //Telephone
        if(!fields["telephone"]){
           formIsValid = false;
           errors["telephone"] = 'Mobile  does not appear to be valid!';
        }

        if(typeof fields["telephone"] !== "undefined")
        {
             if(this.props.international_store === 0 && fields["telephone"].length < 10){
                 formIsValid = false;
                 errors["telephone"] = 'Mobile  does not appear to be valid!';
             } 
             if(this.props.international_store === 1 && fields["telephone"].length < 6){
                 formIsValid = false;
                 errors["telephone"] = 'Mobile  does not appear to be valid!';
             }         
        }

        //Email
         if(!fields["email"]){
           formIsValid = false;
           errors["email"] = 'E-Mail address does not appear to be valid!';
        }

        if(typeof fields["email"] !== "undefined"){
            let lastAtPos = fields["email"].lastIndexOf('@');
            let lastDotPos = fields["email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["email"].indexOf('@@') === -1 && lastDotPos > 2 && (fields["email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["email"] = 'E-Mail address does not appear to be valid!';
            }
       }
       this.setState({errors: errors});
       return formIsValid;
   }


  handleChange(field, event) {
    let fields = this.state.fields;
    var currentInput = event.target.name;
    this.setState({[currentInput]: event.target.value});
    fields[field] = event.target.value; 
    this.setState({fields});
    this.handleValidation();
  }


  handleFormSubmit(e){
     if(this.handleValidation())
     {
       $('body').removeClass('loaded').addClass('loading');
           var form    = $('#edit_profile_form');
           var  formData = new FormData();
           var  params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });

       var response = this.props.dispatch(userProfileUpdate(formData));
       var self = this;
       response.then(function(data){
          self.props.dispatch(userDetailData());
        });
     }
       e.preventDefault();
  }

  update_popup()
  {
    this.props.dispatch(UpdatePopupOpen(1));
  } 

 render(){
    return (
      <div>
        <div className="contner head_margin side-collapse-container">
        <section className="cart_box">
          <h2 className="about_title">Account Information</h2>
         <div className="col-xs-12">
             {this.state.api_data ?
              <form id="edit_profile_form" onSubmit={this.handleFormSubmit}>
                  <div className="delivery_box">                      
                        <div className="new_add_popup_input_box account_info">
                          <input id="firstname" name="firstname" type="text" className="pr_info_input"  value={this.state.firstname} placeholder="Enter Your Name*" alt="Name" onChange={this.handleChange.bind(this, "firstname")} />
                          <span className="error">{this.state.errors["firstname"]}</span>
                        </div>
                        <div className="new_add_popup_input_box account_info"> 
                          <input id="lastname" name="lastname" type="text" className="pr_info_input" value={this.state.lastname} placeholder="Enter Last Name *" alt="Last Name" onChange={this.handleChange.bind(this, "lastname")} />
                          <span className="error">{this.state.errors["lastname"]}</span>
                        </div>
                        {this.props.user_detail.email === "" ?
                        <div className="new_add_popup_input_box account_info"> 
                          <input id="email" name="email" type="text"  className="pr_info_input" value={this.state.email} placeholder="Enter Your E-Mail *" alt="E-Mail Id" onChange={this.handleChange.bind(this, "email")} />
                          <span className="error">{this.state.errors["email"]}</span>
                        </div>
                        : 
                        <div className="new_add_popup_input_box account_info"> 
                         <span> {this.state.email } </span> &nbsp; &nbsp; <Anchor id="update_email_mobile" title="Update" onClick={this.update_popup}  />
                        </div>
                          }
                         {this.props.user_detail.telephone === "" ?
                          <div className="new_add_popup_input_box account_info"> 
                          <input id="telephone" name="telephone" type="tel" className="pr_info_input" value={this.state.telephone} placeholder="Enter Your Telephone Number *" alt="Telephone Number" onChange={this.handleChange.bind(this, "telephone")} />
                            <span className="error">{this.state.errors["telephone"]}</span>
                           </div>
                          :
                          <div className="new_add_popup_input_box account_info"> 
                          <br />
                           <span> {this.state.telephone } </span> &nbsp; &nbsp; <Anchor id="update_email_mobile" title="Update" onClick={this.update_popup}  />
                          </div>
                          }
                       
                          {this.props.user_detail.gst_number !== "" ?
                           <div className="new_add_popup_input_box account_info"> 
                           <input id="gst_number" name="gst_number" type="text" className="pr_info_input" value={this.state.gst_number} placeholder="Enter Your GST Number *" alt="GST Number" readonly="read" />
                           </div>
                          : 
                           <div className="new_add_popup_input_box account_info"> 
                           <input id="gst_number" name="gst_number" type="text" className="pr_info_input" value={this.state.gst_number} placeholder="Enter Your GST Number *" alt="GST Number" onChange={this.handleChange.bind(this, "gst_number")} />
                           <span className="error">{this.state.errors["gst_number"]}</span>
                           </div>
                          }
                        
                         <div className="new_add_popup_input_box account_info"> 
                          <input id="fax" name="fax" type="text" className="pr_info_input" value={this.state.fax} placeholder="Fax" alt="Fax" onChange={this.handleChange.bind(this, "fax")} />
                        </div>

                        <div className="clearfix"></div>
                        <div className="new_add_popup_input_box"> 
                        
                        <div className="col-xs-6">
                         <Link to={Api.folder_path}  className="btn deliver_btn pull-left" style={{margin:'0px', padding:'10px 8px'}} >CANCEL</Link>
                        </div> 
                        <div className="col-xs-6">
                         <button type="submit"  className="btn add_btn_new_popup pull-right" data-dismiss="modal" data-direction='right'>SAVE</button>
                        </div> 
                         <div className="clearfix"></div>
                        </div>
                         <div className="clearfix"></div>
                  </div>
                </form>
              : 
              <div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>
              }  
              <div className="clearfix"></div>
         </div>
           
           <UpdatePopup /> 
         </section>

         <div className="clearfix"></div>
         </div>
       
   </div>
)}}


function mapStateToProps(state){
  return {
    user_detail: state.accountReducer.user_detail,
    actions: bindActionCreators(userDetailData,UpdatePopupOpen)
  };
}
export default connect(mapStateToProps)(Edit);