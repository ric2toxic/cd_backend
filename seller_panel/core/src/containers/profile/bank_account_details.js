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
import Store from '@material-ui/icons/Store';
import { showNotification as showNotificationAction } from 'react-admin'
import $ from 'jquery'

import { updateSellerBankDetailsAndAddress, bankDetails } from '../../actions/ProfileAction';

import  './index.css'

  class BankAccountDetails  extends Component {

   constructor(props)
   {
     super(props);

     this.BankDetailSubmit = this.BankDetailSubmit.bind(this);
     this.handleValidation = this.handleValidation.bind(this);
     this.handleChange     = this.handleChange.bind(this);
     this.ifsccode_check   = this.ifsccode_check.bind(this);

     this.state = {
     fields: {},
     errors: {},
     bank_ac_holder_name: this.props.profile_data.bank_ac_holder_name,
     bank_ac_holder_name_update: this.props.profile_data.bank_ac_holder_name,
     bank_ac_number: this.props.profile_data.bank_ac_number,
     bank_ac_number_update: this.props.profile_data.bank_ac_number,
     cancel_cheque_image: this.props.profile_data.cancel_cheque_image,
     cancel_cheque_image_update: this.props.profile_data.cancel_cheque_image,
     ifsc_code: this.props.profile_data.ifsc_code,
     ifsc_code_update: this.props.profile_data.ifsc_code,
     ADDRESS: '',
     BANK: '',
     BANKCODE: '',
     BRANCH: '',
     CITY: '',
     CONTACT: '',
     DISTRICT: '',
     IFSC: '',
     STATE: ''
     };
  }


    componentWillMount() 
    {
        this.ifsccode_check(this.state.ifsc_code);
    }

   BankDetailSubmit()
    {
     if(this.handleValidation())
     {  
      const { showNotification } = this.props; 
      $("#bank_detail_area").addClass("blur");
      let errors = {};
      var self = this;
      var file_data                   = $('#cancel_cheque_image').prop('files')[0];
      var cancel_cheque_image_upload_path = $("#cancel_cheque_image").val();
      var form_data = new FormData();

      form_data.append('bank_ac_holder_name', this.state.bank_ac_holder_name_update);
      form_data.append('old_bank_ac_holder_name', this.state.bank_ac_holder_name);

      form_data.append('bank_ac_number', this.state.bank_ac_number_update);
      form_data.append('old_bank_ac_number', this.state.bank_ac_number);

      form_data.append('ifsc_code', this.state.ifsc_code_update);
      form_data.append('old_ifsc_code', this.state.ifsc_code);

      form_data.append('img', cancel_cheque_image_upload_path);
      form_data.append('upload_file', file_data);

      form_data.append('ifsc_code', this.state.ifsc_code_update);
      form_data.append('old_ifsc_code', this.state.ifsc_code);

      form_data.append('type', 'bank_details');

      var response = updateSellerBankDetailsAndAddress(form_data);
      response.then(function(data){
      
        $("#bank_detail_area").removeClass("blur");
        if(data.statusCode === 200)
        {
          self.setState({
            bank_ac_holder_name: self.state.bank_ac_holder_name_update,
            bank_ac_number: self.state.bank_ac_number,
            ifsc_code: self.state.ifsc_code_update
          });
          showNotification("Your request has been submitted for verification.On approval, it shall be updated in your profile."); 
        }
        else
        {
          errors["cancel_cheque_image"] = data.message;
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
    
    if(e.target.name === 'ifsc_code_update') 
      { 
        this.ifsccode_check(e.target.value);
      }

    if(e.target.name === 'bank_ac_number_update') 
      { 
         if(e.target.value !== this.state.bank_ac_number || this.state.ifsc_code !== this.state.ifsc_code_update || this.state.bank_ac_holder_name !== this.state.bank_ac_holder_name_update)
         {
           this.setState({
                image_upload: false,
               });
         }
         else
         {
            this.setState({
               image_upload: true,
              });
         }
      }

     if(e.target.name === 'bank_ac_holder_name_update') 
      { 
         if(e.target.value !== this.state.bank_ac_holder_name || this.state.ifsc_code !== this.state.ifsc_code_update || this.state.bank_ac_number !== this.state.bank_ac_number_update)
         {
           this.setState({
                image_upload: false,
               });
         }
         else
         {
            this.setState({
               image_upload: true,
              });
         }
      }    

    let fields = this.state.fields;
    fields[field] = e.target.value; 
    this.setState({fields});  
    this.handleValidation();
  }

    ifsccode_check(value) 
    {
        let self = this; 
        if (value && value.length >= 11)
        {
          var response = bankDetails(value);
          response.then(function(data){
      if(data.BANK)
        { 
          $(".gst_block").show(); 
          if(value !== self.state.ifsc_code || self.state.bank_ac_holder_name !== self.state.bank_ac_holder_name_update)
           {
              self.setState({
                image_upload: false,
               });
           }
          else
           {
              self.setState({
               image_upload: true,
              });
           }

              self.setState({
                ADDRESS : data.ADDRESS,
                BANK : data.BANK,
                BANKCODE : data.BANKCODE,
                BRANCH : data.BRANCH,
                CITY : data.CITY,
                CONTACT : data.CONTACT,
                DISTRICT : data.DISTRICT,
                IFSC : data.IFSC,
                STATE : data.STATE
               });
            } 
            else
            {
              $(".gst_block").hide();
            } 
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

        fields['bank_ac_holder_name_update'] = $("#bank_ac_holder_name_update").val();
        fields['bank_ac_number_update']      = $("#bank_ac_number_update").val();
        fields['ifsc_code_update']           = $("#ifsc_code_update").val();

        this.setState({fields}); 

        //account holder
        if(!fields["bank_ac_holder_name_update"]){
           formIsValid = false;
           errors["bank_ac_holder_name_update"] = 'Please enter bank account holder name!';
        }

        //account number
        if(!fields["bank_ac_number_update"]){
           formIsValid = false;
           errors["bank_ac_number_update"] = 'Please enter bank account number!';
        }

        //GST Certificate
        if(!fields["cancel_cheque_image"]){
           formIsValid = false;
           errors["cancel_cheque_image"] = 'Please choose file!';
        }

        //ifsc code
        if(!fields["ifsc_code_update"]){
           formIsValid = false;
           errors["ifsc_code_update"] = 'IFSC Code Not Proper!';
        }

         if(typeof fields["ifsc_code_update"] !== "undefined"){
             if(fields["ifsc_code_update"].length < 11)
             {
                 formIsValid = false;
                 errors["ifsc_code_update"] = "IFSC Code Not Proper";
             }
        }

       this.setState({errors: errors});
       return formIsValid;
   }  

 render(){ 

return(
    <ExpansionPanel>
         <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
          <Typography> <Store /> Bank Account Details </Typography>
         </ExpansionPanelSummary>
        <ExpansionPanelDetails>
          <Typography id="bank_detail_area">
             <form noValidate autoComplete="off"  className="profile_form">
            <TextField required fullWidth id="bank_ac_holder_name_update" name="bank_ac_holder_name_update" label="Bank Account Holder Name" value={this.state.bank_ac_holder_name_update} onChange={this.handleChange.bind(this, "bank_ac_holder_name_update")} className="text_box" />
             <Button onClick={() => this.BankDetailSubmit()} primary={true} className="custom_submit_btn">Save</Button>
             <span className="error">{this.state.errors["bank_ac_holder_name_update"]}</span>
            <TextField required fullWidth id="bank_ac_number_update"  name="bank_ac_number_update" label="Bank Account Number" value={this.state.bank_ac_number_update} onChange={this.handleChange.bind(this, "bank_ac_number_update")} className="text_box" />    
            <span className="error">{this.state.errors["bank_ac_number_update"]}</span>
            <Input type="file" required disabled={this.state.image_upload} id="cancel_cheque_image" name="cancel_cheque_image" onChange={this.handleChange.bind(this, "cancel_cheque_image")}  className="text_box" />
            <span className="error">{this.state.errors["cancel_cheque_image"]}</span>
            <TextField required fullWidth id="ifsc_code_update"  name="ifsc_code_update" label="Bank Account IFSC Code" value={this.state.ifsc_code_update} onChange={this.handleChange.bind(this, "ifsc_code_update")} className="text_box" />    
             <span className="error">{this.state.errors["ifsc_code_update"]}</span>
             
             <br /><br />
             <div className="gst_block">   
              <p className="text-center"><strong>Bank Detail</strong></p>

              <TextField fullWidth value={this.state.BANK} disabled={true} label="Bank Name" className="text_box" />
             
              <TextField fullWidth value={this.state.BANKCODE} disabled={true} label="Branch" className="text_box" />
              
              <TextField fullWidth value={this.state.ADDRESS} disabled={true} label="Address" className="text_box" />
              
              <TextField fullWidth disabled={true} value={this.state.CITY+' '+this.state.DISTRICT+' '+this.state.STATE} label="Place" className="text_box" />
            </div>
            </form>
          </Typography>
        </ExpansionPanelDetails>
      </ExpansionPanel>
     )
}}

export default connect(null, {
    showNotification: showNotificationAction
})(BankAccountDetails);        