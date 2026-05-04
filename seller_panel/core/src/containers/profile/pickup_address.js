import React, { Component } from 'react'
import { connect } from 'react-redux'
import ExpansionPanel from 'material-ui/ExpansionPanel';
import ExpansionPanelSummary from 'material-ui/ExpansionPanel/ExpansionPanelSummary';
import ExpansionPanelDetails from 'material-ui/ExpansionPanel/ExpansionPanelDetails';
import Typography from 'material-ui/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import TextField from 'material-ui/TextField';
import MenuItem from 'material-ui/Menu/MenuItem';
import Select from 'material-ui/Select';
import Checkbox from 'material-ui/Checkbox';
import Button from 'material-ui/Button';
import AddLocation from '@material-ui/icons/AddLocation';
import { showNotification as showNotificationAction } from 'react-admin'
import $ from 'jquery'

import { updateSellerBankDetailsAndAddress, pincodeAddressData, sameAsPrimary } from '../../actions/ProfileAction';

import  './index.css'

 class PickupAddress  extends Component {

   constructor(props)
   {
     super(props);
     
     this.addressSubmit     = this.addressSubmit.bind(this);
     this.handleValidation  = this.handleValidation.bind(this);
     this.handleChange      = this.handleChange.bind(this);
     this.postcode          = this.postcode.bind(this); 

     this.state = {
     fields: {},
     errors: {}, 
     pickup_pincode: this.props.profile_data.pickup_pincode ? this.props.profile_data.pickup_pincode : '',
     pickup_pincode_update: this.props.profile_data.pickup_pincode ? this.props.profile_data.pickup_pincode : '',
     pickup_city: this.props.profile_data.pickup_city ? this.props.profile_data.pickup_city : '',
     pickup_city_update: this.props.profile_data.pickup_city ? this.props.profile_data.pickup_city : '',
     pickup_zone_id: this.props.profile_data.pickup_zone_id,
     pickup_zone_id_update: this.props.profile_data.pickup_zone_id,
     seller_zone_name: false,
     pickup_address: this.props.profile_data.pickup_address ? this.props.profile_data.pickup_address : '',
     pickup_address_update: this.props.profile_data.pickup_address ? this.props.profile_data.pickup_address : '',
     zones:this.props.profile_data.zones,
     seller_change_update_id:0,
     same_as_primary_address:(this.props.profile_data.same_as_primary_address === "1")?true:false, 
     };
  }


    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['pickup_pincode_update'] = $("#pickup_pincode_update").val();
        fields['pickup_city_update']    = $("#pickup_city_update").val();
        fields['pickup_zone_id_update'] = $("#pickup_zone_id_update").val();
        fields['pickup_address_update'] = $("#pickup_address_update").val();

        this.setState({fields});  
        //Name
        if(!fields["pickup_pincode_update"]){
           formIsValid = false;
           errors["pickup_pincode_update"] = 'Please provide your pincode';
        }
        if(typeof fields["pickup_pincode_update"] !== "undefined"){
             if(fields["pickup_pincode_update"].length < 6 || fields["pickup_pincode_update"].length > 6)
             {
                 formIsValid = false;
                 errors["pickup_pincode_update"] = 'Pincode is invalid!';
             }
        }
        
        //City
        if(!fields["pickup_city_update"]){
           formIsValid = false;
           errors["pickup_city_update"] = 'Please provide your city';
        }
        if(typeof fields["pickup_city_update"] !== "undefined"){
             if(fields["pickup_city_update"].length < 2 || fields["pickup_city_update"].length > 64)
             {
                 formIsValid = false;
                 errors["pickup_city_update"] = 'City must be between 2 and 64 characters!';
             }
        } 

        //Zone
        if(!fields["pickup_zone_id_update"]){
           formIsValid = false;
           errors["pickup_zone_id_update"] = 'Please provide your zone';
        }

        //Address
        if(!fields["pickup_address_update"]){
           formIsValid = false;
           errors["pickup_address_update"] = 'Please provide your address';
        }
        if(typeof fields["pickup_address_update"] !== "undefined"){
             if(fields["pickup_address_update"].length < 10 || fields["pickup_address_update"].length > 100)
             {
                 formIsValid = false;
                 errors["pickup_address_update"] = 'Address must be between 10 and 100 characters!';
             }
        }
        
       this.setState({errors: errors});
       return formIsValid;
   }


handleChange(field, e){ 
  if(field === 'same_as_primary_address')
   {
        if(this.state.same_as_primary_address)
    {
      this.setState({
      same_as_primary_address : false,
     });
    }
    else
    {
      this.setState({
      same_as_primary_address : true,
     });
      const { showNotification } = this.props; 
      var self = this;
      var form_data = new FormData();
      form_data.append('type', 'same_as_primary_address');
      var response = sameAsPrimary(form_data);
      response.then(function(data){
        if(data.statusCode === 200)
        {
          self.setState({
           pickup_pincode : data.data.result.primary_pincode,
           pickup_pincode_update : data.data.result.primary_pincode,
           pickup_city : data.data.result.primary_city,
           pickup_city_update : data.data.result.primary_city,
           pickup_zone_id : data.data.result.primary_zone,
           pickup_zone_id_update : data.data.result.primary_zone,
           pickup_address : data.data.result.primary_address,
           pickup_address_update : data.data.result.primary_address,
          });

          showNotification("Pickup address updated successfully."); 
        }
        else
        {
           showNotification("Error: Please fill address first.", "warning");
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

    if(e.target.name === 'pickup_pincode_update') { this.postcode(); }

    let fields = this.state.fields;
    fields[field] = e.target.value; 
    this.setState({fields});  
    this.handleValidation();
    }
  }

   addressSubmit()
    {
     if(this.handleValidation())
     {  
      const { showNotification } = this.props; 
      var self = this;
      var form_data = new FormData();
      form_data.append('pickup_address', this.state.pickup_address_update);
      form_data.append('old_pickup_address', this.state.pickup_address);

      form_data.append('pickup_city', this.state.pickup_city_update);
      form_data.append('old_pickup_city', this.state.pickup_city);

      form_data.append('pickup_pincode', this.state.pickup_pincode_update);
      form_data.append('old_pickup_pincode', this.state.pickup_pincode);

      form_data.append('pickup_zone_id', this.state.pickup_zone_id_update);
      form_data.append('old_pickup_zone_id', this.state.pickup_zone_id);

      form_data.append('type', 'pickup_address');
      form_data.append('pickup_zone_name', this.state.seller_zone_name);
    
     
      var response = updateSellerBankDetailsAndAddress(form_data);
      response.then(function(data){
        if(data.statusCode === 200)
        {
           self.setState({
                pickup_address : self.state.pickup_address_update,
                pickup_city : self.state.pickup_city_update,
                pickup_pincode : self.state.pickup_pincode_update,
                pickup_zone_id : self.state.pickup_zone_id_update,
               });

            showNotification("Pickup address updated successfully.");
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

    postcode() 
    {
        let self = this; 
        var value = $("#pickup_pincode_update").val();
        if (value && /^\d{6,}$/.test(value.trim()) && value.length === 6)
        {

          var response = pincodeAddressData(value);
          response.then(function(result){
              var data = result.data;
              self.setState({
                pickup_zone_id_update : data.zone_id.toString(),
                seller_zone_name : data.state,
                pickup_city_update : data.city
               });
           })
          .catch(function() {
             console.log('data fetch error');
            });
        }
    }

 render(){ 
return(
    <ExpansionPanel>
         <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
          <Typography> <AddLocation /> Pickup Address</Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography>

            <p>Please enter the address for picking up goods, if different from your registered Firm Address. Otherwise, please select Same as Registered Firm Address.</p>
            <form noValidate autoComplete="off"  className="profile_form">
             <Checkbox name="same_as_primary_address" id="same_as_primary_address" value="1" checked={this.state.same_as_primary_address} onChange={this.handleChange.bind(this, "same_as_primary_address")} /> 
            Same as the Main Contact Person
            <br />
            <TextField required fullWidth id="pickup_pincode_update" name="pickup_pincode_update" value={this.state.pickup_pincode_update} onChange={this.handleChange.bind(this, "pickup_pincode_update")} label="Pincode" className="text_box" />
            <Button onClick={() => this.addressSubmit()} primary={true} disabled={this.state.same_as_primary_address} className={this.state.same_as_primary_address ? 'custom_submit_disable_btn': 'custom_submit_btn'}>Save</Button>
            <span className="error">{this.state.errors["pickup_pincode_update"]}</span>

            <TextField required id="pickup_city_update" name="pickup_city_update" label="City" value={this.state.pickup_city_update} onChange={this.handleChange.bind(this, "pickup_city_update")} className="text_box" />
            <span className="error">{this.state.errors["pickup_city_update"]}</span>

           <Select
            value={this.state.pickup_zone_id_update}
            onChange={this.handleChange.bind(this, "pickup_zone_id_update")}
            className="text_box"
            name="pickup_zone_id_update"
            id="pickup_zone_id_update"
           >
           {this.state.zones ?
            this.state.zones.map((value, index) => {
            return (<MenuItem value={value.zone_id}>{value.name}</MenuItem>)
           })
           : ''}
          </Select>
          <span className="error">{this.state.errors["pickup_zone_id_update"]}</span>

            <TextField required fullWidth multiline id="pickup_address_update" value={this.state.pickup_address_update} onChange={this.handleChange.bind(this, "pickup_address_update")} name="pickup_address_update" label="Address" className="text_box text_box_full" />
             <span className="error">{this.state.errors["pickup_address_update"]}</span>
          </form>  
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}      

export default connect(null, {
    showNotification: showNotificationAction
})(PickupAddress);