import React, { Component } from 'react'
import $ from 'jquery'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { bindActionCreators } from 'redux'
import Api from '../../../api/Api'
import  './index.css'
import { userPasswordUpdate} from '../../../actions/AccountAction'

class ChangePassword  extends Component {

  constructor(props) {
    super(props);

    this.state = {
           fields: {},
           errors: {}
       }

    this.handleChange     = this.handleChange.bind(this);
    this.handleValidation = this.handleValidation.bind(this);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
  }

  handleChange(field, e){  
        let fields = this.state.fields; 
        fields[field] = e.target.value;  
        this.setState({fields});
        this.handleValidation();
  }

  handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;
        this.setState({fields});  
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
        if(!fields["confirm_password"]){
           formIsValid = false;
           errors["confirm_password"] = 'Please enter both password same!';
        }
         if(typeof fields["confirm_password"] !== "undefined"){
             if(fields["confirm_password"] !== fields["password"])
             {
                 formIsValid = false;
                 errors["confirm_password"] = 'Please enter both password same!';
             }          
        }

       this.setState({errors: errors});
       return formIsValid;
   }

  handleFormSubmit(e)
  {
     e.preventDefault();
      if(this.handleValidation())
      {
        $('body').removeClass('loaded').addClass('loading');
        var form    = $('#edit_password_form');
        var  formData = new FormData();
        var  params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });

        this.props.dispatch(userPasswordUpdate(formData));
        $('#edit_password_form').find('input[name="password"]').val('');
        $('#edit_password_form').find('input[name="confirm_password"]').val('');
      }  
  }

 render(){

    return (
      <div>
       <div className="contner head_margin side-collapse-container">
        <section className="cart_box">
          <h2 className="about_title">Change Password</h2>

         <div className="col-xs-12">
              <form id="edit_password_form" onSubmit={this.handleFormSubmit}>
              <div className="mobile_details_panel  delivery_box">
                <div className="new_add_popup_input_box account_info"> 
                    <input id="password"  name="password" type="Password" className="pr_info_input" placeholder="Enter New Password" alt="New Password" onChange={this.handleChange.bind(this, "password")} />
                     <span className="error">{this.state.errors["password"]}</span>
                </div>
                <div className="new_add_popup_input_box account_info"> 
                    <input id="confirm_password" name="confirm_password" type="Password" className="pr_info_input" placeholder="Enter Confirm password" alt="confirm password" onChange={this.handleChange.bind(this, "confirm_password")} />
                     <span className="error">{this.state.errors["confirm_password"]}</span>
                </div>
                <div className="clearfix"></div>
               <div className="col-xs-6">
                         <Link to={Api.folder_path}  className="btn deliver_btn pull-left" style={{margin:'0px', padding:'10px 8px'}} >CANCEL</Link>
                        </div> 
                        <div className="col-xs-6">
                         <button type="submit"  className="btn add_btn_new_popup pull-right" data-dismiss="modal" data-direction='right'>SAVE</button>
                        </div>

              </div>
              </form>
              <div className="clearfix"></div>
         </div>
           
         </section>

         <div className="clearfix"></div>
       
   </div>
   </div>
)}}
function mapStateToProps(state){
  return {
    actions: bindActionCreators(userPasswordUpdate)
  };
}
export default connect(mapStateToProps)(ChangePassword);