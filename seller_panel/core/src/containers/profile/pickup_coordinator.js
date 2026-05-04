import React, { Component } from 'react';
import { connect } from 'react-redux'
import ExpansionPanel from 'material-ui/ExpansionPanel';
import ExpansionPanelSummary from 'material-ui/ExpansionPanel/ExpansionPanelSummary';
import ExpansionPanelDetails from 'material-ui/ExpansionPanel/ExpansionPanelDetails';
import Typography from 'material-ui/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import TextField from 'material-ui/TextField';
import Checkbox from 'material-ui/Checkbox';
import Button from 'material-ui/Button';
import Person from '@material-ui/icons/Person';
import { showNotification as showNotificationAction } from 'react-admin'
import $ from 'jquery'

import { updateSellerProfile, sameAsPrimary } from '../../actions/ProfileAction';

import  './index.css'

  class PickupCoordinator  extends Component {

   constructor(props)
   {
     super(props);

     this.BassicSubmit     = this.BassicSubmit.bind(this);
     this.handleValidation = this.handleValidation.bind(this);
     this.handleChange     = this.handleChange.bind(this); 

     this.state = {
     fields: {},
     errors: {},
     pickup_holder_name: this.props.profile_data.pickup_holder_name ? this.props.profile_data.pickup_holder_name : '',
     pickup_contact_no: this.props.profile_data.pickup_contact_no ? this.props.profile_data.pickup_contact_no : '',
     pickup_holder_name_update: this.props.profile_data.pickup_holder_name ? this.props.profile_data.pickup_holder_name : '',
     pickup_contact_no_update: this.props.profile_data.pickup_contact_no ? this.props.profile_data.pickup_contact_no : '',
     same_as_primary_pickup:(this.props.profile_data.same_as_primary_pickup === "1")?true:false,
     };
  }

   BassicSubmit(field)
    {
     if(this.handleValidation())
     {  
      let self         = this;
      let fields       = this.state;
      let old_value    = fields[field];
      let update_value = fields[field+'_update'];

      var form_data = new FormData();
      form_data.append('field', field);
      form_data.append('update_value', update_value);
      form_data.append('old_value', old_value);
     
     const { showNotification } = this.props; 
     if(update_value !== old_value)
    {
      var response = updateSellerProfile(form_data);
      response.then(function(data){
        if(data.statusCode === 200)
        {
          fields[field] = update_value;
          self.setState({fields});
          showNotification("Your profile updated successfully.");
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

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['pickup_holder_name_update'] = $("#pickup_holder_name_update").val();
        fields['pickup_contact_no_update']   = $("#pickup_contact_no_update").val();

        this.setState({fields});  
        //Name
        if(!fields["pickup_holder_name_update"]){
           formIsValid = false;
           errors["pickup_holder_name_update"] = 'Please provide your name';
        }
        if(typeof fields["pickup_holder_name_update"] !== "undefined"){
             if(!fields["pickup_holder_name_update"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["pickup_holder_name_update"] = "Only letters";
             }

             if(fields["pickup_holder_name_update"].length < 2 || fields["pickup_holder_name_update"].length > 64)
             {
                 formIsValid = false;
                 errors["pickup_holder_name_update"] = 'Name must be between 2 and 64 characters!';
             }
        } 

        //Contact
        if(!fields["pickup_contact_no_update"]){
           formIsValid = false;
           errors["pickup_contact_no_update"] = 'Mobile must have 10 numbers!';
        }

        if(typeof fields["pickup_contact_no_update"] !== "undefined"){
             if(fields["pickup_contact_no_update"].length < 10 || fields["pickup_contact_no_update"].length > 10){
                 formIsValid = false;
                 errors["pickup_contact_no_update"] = 'Mobile  does not appear to be valid!';
             }         
        }

       this.setState({errors: errors});
       return formIsValid;
   }


handleChange(field, e){ 
   if(field === 'same_as_primary_pickup')
   {
    if(this.state.same_as_primary_pickup)
    {
      this.setState({
      same_as_primary_pickup : false,
     });
    }
    else
    {
      this.setState({
      same_as_primary_pickup : true,
     });
      
      const { showNotification } = this.props; 
      var self = this;
      var form_data = new FormData();
      form_data.append('type', 'same_as_primary_pickup');
      var response = sameAsPrimary(form_data);
      response.then(function(data){
        if(data.statusCode === 200)
        {
          self.setState({
           pickup_holder_name : data.data.result.primary_contact_name,
           pickup_contact_no : data.data.result.primary_contact_no,
           pickup_holder_name_update : data.data.result.primary_contact_name,
           pickup_contact_no_update : data.data.result.primary_contact_no,
          });
          showNotification("Your profile updated successfully.");
        }
        else
        {
           showNotification("Error: Please fill bassic information first.", "warning");
        }
      })
       .catch(function() {
          console.log('data fetch error');
      });
    }
   }
   else
   {
    this.setState({
      [field]: e.target.value,
    });
    let fields = this.state.fields;
    fields[field] = e.target.value; 
    this.setState({fields});  
    this.handleValidation();
   }
  }

 render(){ 

return(
    <ExpansionPanel>
         <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
          <Typography><Person /> Pickup Coordinator</Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography> 

          <p>Please enter details of the person who will be coordinating for goods pickup from your premises.</p>
          <br />
           <form noValidate autoComplete="off"  className="profile_form">
            <Checkbox name="same_as_primary_pickup" id="same_as_primary_pickup" value="1" checked={this.state.same_as_primary_pickup} onChange={this.handleChange.bind(this, "same_as_primary_pickup")} /> 
            Same as the Main Contact Person
            <br />
            <TextField required fullWidth id="pickup_holder_name_update" name="pickup_holder_name_update" label="Pickup Coordinator Name" value={this.state.pickup_holder_name_update} onChange={this.handleChange.bind(this, "pickup_holder_name_update")} className="text_box" />
            <Button onClick={() => this.BassicSubmit("pickup_holder_name")} disabled={this.state.same_as_primary_pickup} primary={true} className={this.state.same_as_primary_pickup ? 'custom_submit_disable_btn': 'custom_submit_btn'}>Save</Button>
            <span className="error">{this.state.errors["pickup_holder_name_update"]}</span>

            <TextField required fullWidth id="pickup_contact_no_update" name="pickup_contact_no_update" value={this.state.pickup_contact_no_update} onChange={this.handleChange.bind(this, "pickup_contact_no_update")} label="Contact No." className="text_box" />
            <Button onClick={() => this.BassicSubmit("pickup_contact_no")} disabled={this.state.same_as_primary_pickup} primary={true} className={this.state.same_as_primary_pickup ? 'custom_submit_disable_btn': 'custom_submit_btn'}>Save</Button>
            <span className="error">{this.state.errors["pickup_contact_no_update"]}</span>
            
         
          </form>  
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}      

export default connect(null, {
    showNotification: showNotificationAction
})(PickupCoordinator);