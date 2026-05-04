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
import Button from 'material-ui/Button';
import AddLocation from '@material-ui/icons/AddLocation';
import { showNotification as showNotificationAction } from 'react-admin'
import $ from 'jquery'

import { updateSellerBankDetailsAndAddress, pincodeAddressData } from '../../actions/ProfileAction';

import  './index.css'

 class Address  extends Component {

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
     pincode: this.props.profile_data.pincode,
     pincode_update: this.props.profile_data.pincode,
     seller_city: this.props.profile_data.city ? this.props.profile_data.city : '',
     seller_city_update: this.props.profile_data.city ? this.props.profile_data.city : '',
     seller_zone_id: this.props.profile_data.zone_id,
     seller_zone_id_update: this.props.profile_data.zone_id,
     seller_zone_name: false,
     address: this.props.profile_data.address1 ? this.props.profile_data.address1+' '+this.props.profile_data.address2: '',
     address_update: this.props.profile_data.address1 ? this.props.profile_data.address1+' '+this.props.profile_data.address2: '',
     zones:this.props.profile_data.zones,
     seller_change_update_id:0, 
     };
  }


    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['pincode_update']        = $("#pincode_update").val();
        fields['seller_city_update']    = $("#seller_city_update").val();
        fields['seller_zone_id_update'] = $("#seller_zone_id_update").val();
        fields['address_update']        = $("#address_update").val();

        this.setState({fields});  
        //Name
        if(!fields["pincode_update"]){
           formIsValid = false;
           errors["pincode_update"] = 'Please provide your pincode';
        }
        if(typeof fields["pincode_update"] !== "undefined"){
             if(fields["pincode_update"].length < 6 || fields["pincode_update"].length > 6)
             {
                 formIsValid = false;
                 errors["pincode_update"] = 'Pincode is invalid!';
             }
        }
        
        //City
        if(!fields["seller_city_update"]){
           formIsValid = false;
           errors["seller_city_update"] = 'Please provide your city';
        }
        if(typeof fields["seller_city_update"] !== "undefined"){
             if(fields["seller_city_update"].length < 2 || fields["seller_city_update"].length > 64)
             {
                 formIsValid = false;
                 errors["seller_city_update"] = 'City must be between 2 and 64 characters!';
             }
        } 

        //Zone
        if(!fields["seller_zone_id_update"]){
           formIsValid = false;
           errors["seller_zone_id_update"] = 'Please provide your zone';
        }

        //Address
        if(!fields["address_update"]){
           formIsValid = false;
           errors["address_update"] = 'Please provide your address';
        }
        if(typeof fields["address_update"] !== "undefined"){
             if(fields["address_update"].length < 10 || fields["address_update"].length > 100)
             {
                 formIsValid = false;
                 errors["address_update"] = 'Address must be between 10 and 100 characters!';
             }
        }
        
       this.setState({errors: errors});
       return formIsValid;
   }


handleChange(field, e){ 
    this.setState({
      [field]: e.target.value,
    });

    if(e.target.name === 'pincode_update') { this.postcode(); }

    let fields = this.state.fields;
    fields[field] = e.target.value; 
    this.setState({fields});  
    this.handleValidation();
  }

   addressSubmit()
    {
     if(this.handleValidation())
     {  
      var self = this;
      const { showNotification } = this.props; 

      var form_data = new FormData();
      form_data.append('address', this.state.address_update);
      form_data.append('old_address', this.state.address);

      form_data.append('seller_city', this.state.seller_city_update);
      form_data.append('old_seller_city', this.state.seller_city);

      form_data.append('pincode', this.state.pincode_update);
      form_data.append('old_pincode', this.state.pincode);

      form_data.append('seller_zone_id', this.state.seller_zone_id_update);
      form_data.append('old_seller_zone_id', this.state.seller_zone_id);

      form_data.append('type', 'address_details');
      form_data.append('seller_zone_name', this.state.seller_zone_name);

      form_data.append('seller_change_update_id', this.state.seller_change_update_id);
    
     
      var response = updateSellerBankDetailsAndAddress(form_data);
      response.then(function(data){
         if(data.statusCode === 200)
        {
           self.setState({
                address : self.state.address_update,
                seller_city : self.state.seller_city_update,
                pincode : self.state.pincode_update,
                seller_zone_id : self.state.seller_zone_id_update,
               });

          showNotification('Address updated successfully.'); 
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
        var value = $("#pincode_update").val();
        if (value && /^\d{6,}$/.test(value.trim()) && value.length === 6)
        {

          var response = pincodeAddressData(value);
          response.then(function(data){
              self.setState({
                seller_zone_id_update : data.zone_id.toString(),
                seller_zone_name : data.state,
                seller_city_update : data.city
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
          <Typography> <AddLocation /> Address</Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography>

            <p>Please enter your registered Firm Address (if you have TIN no, please mention the registered address against the TIN.</p>
            <form noValidate autoComplete="off"  className="profile_form">
            
            <TextField required fullWidth id="pincode_update" name="pincode_update" value={this.state.pincode_update} onChange={this.handleChange.bind(this, "pincode_update")} label="Pincode" className="text_box" />
            <Button onClick={() => this.addressSubmit()} primary={true} className="custom_submit_btn">Save</Button>
            <span className="error">{this.state.errors["pincode_update"]}</span>

            <TextField required id="seller_city_update" name="seller_city_update" label="City" value={this.state.seller_city_update} onChange={this.handleChange.bind(this, "seller_city_update")} className="text_box" />
            <span className="error">{this.state.errors["seller_city_update"]}</span>

           <Select
            value={this.state.seller_zone_id_update}
            onChange={this.handleChange.bind(this, "seller_zone_id_update")}
            className="text_box"
            name="seller_zone_id_update"
            id="seller_zone_id_update"
           >
           {this.state.zones ? 
            this.state.zones.map((value, index) => {
            return (<MenuItem value={value.zone_id}>{value.name}</MenuItem>)
           })
           : ''}
          </Select>
          <span className="error">{this.state.errors["seller_zone_id_update"]}</span>

            <TextField required fullWidth multiline id="address_update" value={this.state.address_update} onChange={this.handleChange.bind(this, "address_update")} name="address_update" label="Address" className="text_box text_box_full" />
             <span className="error">{this.state.errors["address_update"]}</span>
          </form>  
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}      

export default connect(null, {
    showNotification: showNotificationAction
})(Address);