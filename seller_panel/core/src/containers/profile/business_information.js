import React, { Component } from 'react';
import { connect } from 'react-redux'
import ExpansionPanel from 'material-ui/ExpansionPanel';
import ExpansionPanelSummary from 'material-ui/ExpansionPanel/ExpansionPanelSummary';
import ExpansionPanelDetails from 'material-ui/ExpansionPanel/ExpansionPanelDetails';
import Typography from 'material-ui/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import TextField from 'material-ui/TextField';
import Button from 'material-ui/Button';
import Input from 'material-ui/Input';
import ArrowForward from '@material-ui/icons/ArrowForward';
import Equalizer from '@material-ui/icons/Equalizer';
import { showNotification as showNotificationAction } from 'react-admin'
import $ from 'jquery'

import { updateSellerBankDetailsAndAddress } from '../../actions/ProfileAction';

import  './index.css'

  class BusinessInformation  extends Component {

   constructor(props)
   {
     super(props);

     this.GstSubmit     = this.GstSubmit.bind(this);
     this.handleValidation = this.handleValidation.bind(this);
     this.handleChange     = this.handleChange.bind(this);

     this.state = {
     fields: {},
     errors: {},
     company: this.props.profile_data.company,
     nickname: this.props.profile_data.nickname,
     pan: this.props.profile_data.pan,
     pan_image: this.props.profile_data.pan_image,
     tin: this.props.profile_data.tin,
     tin_image: this.props.profile_data.tin_image,
     tin_tax_type: this.props.profile_data.tin_tax_type,
     gst_provisional_id: this.props.profile_data.gst_provisional_id,
     gst_provisional_id_update: this.props.profile_data.gst_provisional_id,
     gst_arn: this.props.profile_data.gst_arn,
     gst_arn_update: this.props.profile_data.gst_arn
     };
  }

   GstSubmit()
    {
     if(this.handleValidation())
     {  
      const { showNotification } = this.props; 
      $("#business_detail_area").addClass("blur");
      let errors = {};
      var self = this;
      var file_data                   = $('#gst_certificate_upload').prop('files')[0];
      var gst_certificate_upload_path = $("#gst_certificate_upload").val();
      var form_data = new FormData();

      form_data.append('gst_arn', this.state.gst_arn_update);
      form_data.append('old_gst_arn', this.state.gst_arn);

      form_data.append('gst_provision_id', this.state.gst_provisional_id_update);
      form_data.append('old_gst_provision_id', this.state.gst_provisional_id);
      form_data.append('img', gst_certificate_upload_path);
      form_data.append('upload_file', file_data);

      form_data.append('type', 'gst_details');

      var response = updateSellerBankDetailsAndAddress(form_data);
      response.then(function(data){
        $("#business_detail_area").removeClass("blur");
        if(data.statusCode === 200)
        {
          self.setState({
            gst_arn: self.state.gst_arn_update,
            gst_provision_id: self.state.gst_provision_id_update
          });

          showNotification("Your request has been submitted for verification.On approval, it shall be updated in your profile.");
        }
        else
        {
           if(data.data.type === 'get_certificate')
          {
             errors["gst_certificate_upload"] = data.message;
          }
          if(data.data.type === 'gst_provisional_id')
          {
            errors["gst_provisional_id_update"] = data.message;
          }
          self.setState({errors: errors});
        }
      })
      .catch(function() {
          console.log('data fetch error');
         });
     }
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

  handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['gst_provisional_id_update'] = $("#gst_provisional_id_update").val();

        this.setState({fields}); 

        //GST
        if(!fields["gst_provisional_id_update"]){
           formIsValid = false;
           errors["gst_provisional_id_update"] = 'Please provide your GST ID';
        }

        
        if(typeof fields["gst_provisional_id_update"] !== "undefined"){
            var gst_number = fields["gst_provisional_id_update"];
            var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/;
            if (reggstin.test(gst_number) === false) {
                formIsValid = false;
                errors["gst_provisional_id_update"] = "Please correct GST Provisional ID";
                this.setState({errors: errors});
                return formIsValid;
            }

            var factor_even = 1;
            var factor_odd = 2;
            var sum = 0;
            var gst_number_array = gst_number.split("");
            var checksum_weight_array = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split("");
            var checksum_mod = checksum_weight_array.length;
            var factor = factor_even;
            var entered_checksum_character = gst_number.substr(-1);
            var gst_number_without_checksum = gst_number.substring(0, gst_number.length - 1);
            
            if(gst_number_array.length === 15) {
                gst_number_array.pop();
            }

            for(var index = 0; index < gst_number_array.length; ++index) {
                var current_letter_weight = checksum_weight_array.indexOf(gst_number_array[index]);
                var current_checksum_digit = 0;
                if(current_letter_weight !== -1) {
                    current_checksum_digit = current_letter_weight * factor;
                    current_checksum_digit = parseInt((current_checksum_digit / checksum_mod) + (current_checksum_digit % checksum_mod),10);
                    sum += current_checksum_digit;
                }
                factor = (factor === factor_even) ? factor_odd : factor_even;
            }

            var calculated_checksum_weight = (checksum_mod - (sum % checksum_mod)) % checksum_mod;
            var calculated_checksum_letter = (checksum_weight_array[calculated_checksum_weight])
                                            ? checksum_weight_array[calculated_checksum_weight] 
                                            : false;
            if(calculated_checksum_letter !== entered_checksum_character) {
                formIsValid = false;
                errors["gst_provisional_id_update"] = "Invalid GST Number! Do you mean "+ gst_number_without_checksum + calculated_checksum_letter +" instead? Please check and enter correct GST number again!";
            }
        } 

        //GST Certificate
        if(!fields["gst_certificate_upload"]){
           formIsValid = false;
           errors["gst_certificate_upload"] = 'Please choose file!';
        }

       this.setState({errors: errors});
       return formIsValid;
   }  

 render(){ 

return(
    <ExpansionPanel defaultExpanded>
         <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
          <Typography> <Equalizer /> Business Information</Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography id="business_detail_area">
            <form noValidate autoComplete="off"  className="profile_form">
            <TextField required fullWidth id="nickname" disabled={true} name="nickname" label="Your Unique WholesaleBox ID" value={this.state.nickname} className="text_box" />
            <TextField required fullWidth id="company" disabled={true} name="company" label="Firm Name" value={$('<div/>').html(this.state.company).text()} className="text_box" />
             <br /><br />

            <div className="gst_block">   
              <p className="text-center"><strong>GST Detail</strong></p>

              <TextField required fullWidth id="gst_provisional_id_update" name="gst_provisional_id_update" value={this.state.gst_provisional_id_update} label="GST Provisional ID" onChange={this.handleChange.bind(this, "gst_provisional_id_update")} className="text_box" />
              <Button onClick={() => this.GstSubmit()} primary={true} className="custom_submit_btn">Save</Button>
              <span className="error">{this.state.errors["gst_provisional_id_update"]}</span>

              <Input type="file" required id="gst_certificate_upload" name="gst_certificate_upload" onChange={this.handleChange.bind(this, "gst_certificate_upload")} className="text_box" />
              <span className="error">{this.state.errors["gst_certificate_upload"]}</span>

              <TextField fullWidth id="gst_arn_update" name="gst_arn_update" value={this.state.gst_arn_update} onChange={this.handleChange.bind(this, "gst_arn_update")} label="GST ARN" className="text_box" />
               <span className="error">{this.state.errors["gst_arn_update"]}</span>
            </div>

              <br /><br />
             <p className="note"> <ArrowForward /> Please enter your registered business PAN. Kindly upload PAN Card image for verification.</p>  
             <TextField required fullWidth id="pan" name="pan" disabled={true} value={this.state.pan} label="PAN (Permanent Account Number)" className="text_box" />
               
           </form>
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}

export default connect(null, {
    showNotification: showNotificationAction
})(BusinessInformation);      