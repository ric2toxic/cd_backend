import React, { Component } from 'react';
import { connect } from 'react-redux'
import ExpansionPanel from 'material-ui/ExpansionPanel';
import ExpansionPanelSummary from 'material-ui/ExpansionPanel/ExpansionPanelSummary';
import ExpansionPanelDetails from 'material-ui/ExpansionPanel/ExpansionPanelDetails';
import Typography from 'material-ui/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import TextField from 'material-ui/TextField';
import Button from 'material-ui/Button';
import Lock from '@material-ui/icons/Lock';
import { showNotification as showNotificationAction } from 'react-admin'

import { changePassword } from '../../actions/ProfileAction';

import  './index.css'

  class Password  extends Component {

   constructor(props)
   {
     super(props);

     this.PasswordSubmit     = this.PasswordSubmit.bind(this);
     this.handleValidation = this.handleValidation.bind(this);
     this.handleChange     = this.handleChange.bind(this); 

     this.state = {
     fields: {},
     errors: {},
     password: '',
     confirm_password: ''
     };
  }

   PasswordSubmit()
    {
     if(this.handleValidation())
     {  
      const { showNotification } = this.props; 
      var form_data = new FormData();
      form_data.append('password', this.state.password);
      form_data.append('confirm_password', this.state.confirm_password);
      
       this.setState({
       'password': '',
       });
       this.setState({
       'confirm_password': '',
       });

      var response = changePassword(form_data);
      response.then(function(data){
          showNotification(data.message);
      })
        .catch(function() {
          console.log('data fetch error');
         });
     }
    }

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        this.setState({fields});  
        //Name
        if(!fields["password"]){
           formIsValid = false;
           errors["password"] = 'Please provide your password';
        }
        if(typeof fields["password"] !== "undefined"){
             if(fields["password"].length < 6 || fields["password"].length > 20)
             {
                 formIsValid = false;
                 errors["password"] = 'password must be between 6 and 20 characters!';
             }
        } 

        //Contact
        if(!fields["confirm_password"]){
           formIsValid = false;
           errors["confirm_password"] = 'Please enter confirm password!';
        }

        if(typeof fields["confirm_password"] !== "undefined"){
             if(fields["confirm_password"] !== fields["password"]){
                 formIsValid = false;
                 errors["confirm_password"] = 'Please enter both password same!';
             }          
        }

       this.setState({errors: errors});
       return formIsValid;
   }


handleChange(field, e){ 
    this.setState({
      [field]: e.target.value,
    });
    let fields = this.state.fields;
    fields[field] = e.target.value; 
    this.setState({fields});  
    this.handleValidation();
  }

 render(){ 

return(
    <ExpansionPanel defaultExpanded>
         <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
          <Typography> <Lock /> Change Password</Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography>
          
           <form noValidate autoComplete="off"  className="profile_form">

            <TextField type="password" required fullWidth id="password" name="password" label="Password" value={this.state.password} onChange={this.handleChange.bind(this, "password")} className="text_box" />
            <span className="error">{this.state.errors["password"]}</span>

            <TextField type="password" required fullWidth id="confirm_password" name="confirm_password" value={this.state.confirm_password} onChange={this.handleChange.bind(this, "confirm_password")} label="Confirm Password" className="text_box" />
            <span className="error">{this.state.errors["confirm_password"]}</span>
             <br /> 
            <Button onClick={() => this.PasswordSubmit()} primary={true} className="custom_submit_btn">Save</Button>
          </form>  
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}      

export default connect(null, {
    showNotification: showNotificationAction
})(Password);