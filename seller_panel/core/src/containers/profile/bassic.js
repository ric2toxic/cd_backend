import React, { Component } from 'react';
import { connect } from 'react-redux'
import ExpansionPanel from 'material-ui/ExpansionPanel';
import ExpansionPanelSummary from 'material-ui/ExpansionPanel/ExpansionPanelSummary';
import ExpansionPanelDetails from 'material-ui/ExpansionPanel/ExpansionPanelDetails';
import Typography from 'material-ui/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import TextField from 'material-ui/TextField';
import Button from 'material-ui/Button';
import Person from '@material-ui/icons/Person';
import { showNotification as showNotificationAction } from 'react-admin'
import $ from 'jquery'

import { updateSellerProfile } from '../../actions/ProfileAction';

import  './index.css'

  class Bassic  extends Component {

   constructor(props)
   {
     super(props);

     this.BassicSubmit            = this.BassicSubmit.bind(this);
     this.handleValidation        = this.handleValidation.bind(this);
     this.handleChange            = this.handleChange.bind(this);
     this.add_email               = this.add_email.bind(this);
     this.remove_email            = this.remove_email.bind(this);
     this.additional_email_Change = this.additional_email_Change.bind(this);
     this.additional_email_submit = this.additional_email_submit.bind(this);   

     this.state = {
     fields: {},
     errors: {},
     primary_contact_name: this.props.profile_data.primary_contact_name,
     primary_contact_name_update: this.props.profile_data.primary_contact_name,
     primary_contact_no: this.props.profile_data.primary_contact_no,
     primary_contact_no_update: this.props.profile_data.primary_contact_no,
     email: this.props.profile_data.email,
     additional_emails: this.props.profile_data.additional_emails ? this.props.profile_data.additional_emails.split(","):[],
     additional_emails_update: this.props.profile_data.additional_emails ? this.props.profile_data.additional_emails.split(","):[]
     };
  }

   BassicSubmit(field)
    {
     if(this.handleValidation()){ 
      let self         = this;
      let fields       = this.state;
      let old_value    = fields[field];
      let update_value = fields[field+'_update'];

      const { showNotification } = this.props; 

      var form_data = new FormData();
      form_data.append('field', field);
      form_data.append('update_value', update_value);
      form_data.append('old_value', old_value);
     
    if(update_value !== old_value)
    { 
      var response = updateSellerProfile(form_data);
      response.then(function(data){
        if(data.statusCode===200)
        {
          fields[field] = update_value;
          self.setState({fields});
          showNotification('profile update successfully');
        }
        else
        {
          showNotification(data.message, "warning");
        }
      })
        .catch(function() {
          console.log('data fetch error');
         });
     }
    }  
  }

    additional_email_submit()
    { 
      let self         = this;
      let additional_emails    = this.state.additional_emails;
      let additional_emails_update = this.state.additional_emails_update;
      let errors = {};
      let valid = true;

       var i = 0;
       for (let value of additional_emails_update/**/) 
       {
             let lastAtPos  = value.lastIndexOf('@');
             let lastDotPos = value.lastIndexOf('.');
             if (!(lastAtPos < lastDotPos && lastAtPos > 0 && value.indexOf('@@') === -1 && lastDotPos > 2 && (value.length - lastDotPos) > 2)) 
             {
              errors["additional_emails_"+i] = 'Invalid email address!';
              valid = false;
            }
            i++;
         }

        this.setState({errors});
      
   if(valid)
    {
      additional_emails        = additional_emails.join();
      additional_emails_update = additional_emails_update.join();

      const { showNotification } = this.props; 
      var form_data = new FormData();
      form_data.append('field', 'additional_emails');
      form_data.append('update_value', additional_emails_update);
      form_data.append('old_value', additional_emails);
     
      if(additional_emails_update !== additional_emails)
      { 
      var response = updateSellerProfile(form_data);
      response.then(function(data){
        if(data.statusCode === 200)
        {
          additional_emails = self.state.additional_emails_update;
          self.setState({additional_emails});
          showNotification('Additional emails update successfully');
        }
        else
        {
          showNotification('Error: Invalids additional emails.', 'warning');
        }

       })
        .catch(function() {
          console.log('data fetch error');
         });
       }
     }
  } 

  add_email()
  {
     let additional_emails_update = this.state.additional_emails_update;
     additional_emails_update.push("");
     this.setState({additional_emails_update});
  }

  remove_email(index)
  {
     var email = $("#additional_emails_"+index).val(); 
     let additional_emails_update = this.state.additional_emails_update;
     
     additional_emails_update = $.grep(additional_emails_update, function( a ) {
             return a !== email;
             });

     this.setState({additional_emails_update});
  }

  additional_email_Change(field, e)
  { 
    let additional_emails_update = this.state.additional_emails_update;
    additional_emails_update[field]  = e.target.value;
    this.setState({additional_emails_update});
  }

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['primary_contact_name_update'] = $("#primary_contact_name_update").val();
        fields['primary_contact_no_update']   = $("#primary_contact_no_update").val();

        this.setState({fields});  
        //Name
        if(!fields["primary_contact_name_update"]){
           formIsValid = false;
           errors["primary_contact_name_update"] = 'Please provide your name';
        }
        if(typeof fields["primary_contact_name_update"] !== "undefined"){
             if(!fields["primary_contact_name_update"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["primary_contact_name_update"] = "Only letters";
             }

             if(fields["primary_contact_name_update"].length < 2 || fields["primary_contact_name_update"].length > 64)
             {
                 formIsValid = false;
                 errors["primary_contact_name_update"] = 'Name must be between 2 and 64 characters!';
             }
        } 

        //Contact
        if(!fields["primary_contact_no_update"]){
           formIsValid = false;
           errors["primary_contact_no_update"] = 'Mobile must have 10 numbers!';
        }

        if(typeof fields["primary_contact_no_update"] !== "undefined"){
             if(fields["primary_contact_no_update"].length < 10 || fields["primary_contact_no_update"].length > 10){
                 formIsValid = false;
                 errors["primary_contact_no_update"] = 'Mobile  does not appear to be valid!';
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
let self = this;
let additional_emails = this.state.additional_emails_update.map((email, index) =>
    {
      return(<span><TextField required fullWidth id={"additional_emails_"+index} onChange={self.additional_email_Change.bind(this, index)} label="Email" value={email} className="text_box" />
             <Button onClick={() => this.remove_email(index)} primary={true} className="minus_btn">-</Button>
             <span className="error">{this.state.errors["additional_emails_"+index]}</span>
             </span>)
    });

return(
    <ExpansionPanel defaultExpanded>
         <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
          <Typography> <Person /> Basic Information</Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography>
           
          <form noValidate autoComplete="off" className="profile_form" id="bassic_form">

            <TextField required fullWidth id="primary_contact_name_update" name="primary_contact_name_update" label="Person Name" value={this.state.primary_contact_name_update} onChange={this.handleChange.bind(this, "primary_contact_name_update")} className="text_box" />
            <Button onClick={() => this.BassicSubmit("primary_contact_name")} primary={true} className="custom_submit_btn">Save</Button>
            <span className="error">{this.state.errors["primary_contact_name_update"]}</span>

            <TextField required fullWidth id="primary_contact_no_update" name="primary_contact_no_update" value={this.state.primary_contact_no_update} onChange={this.handleChange.bind(this, "primary_contact_no_update")} label="Contact Number" className="text_box" />
            <Button onClick={() => this.BassicSubmit("primary_contact_no")} primary={true} className="custom_submit_btn">Save</Button>
            <span className="error">{this.state.errors["primary_contact_no_update"]}</span>
            
            <TextField required fullWidth id="email" name="email" label="Email" value={this.state.email} className="text_box" />
             
             <br /> <br />
            <b>Additional CC Emails</b> 
            <Button onClick={this.add_email} primary={true} className="plus_btn">+</Button> 
            <Button onClick={() => this.additional_email_submit()} primary={true} className="custom_submit_btn">Save</Button>
             <br />
              {additional_emails }
          </form>  
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}      


export default connect(null, {
    showNotification: showNotificationAction
})(Bassic);