import React, { Component } from 'react'
import $ from 'jquery'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { bindActionCreators } from 'redux'
import Api from '../../../api/Api'
import  './index.css'
import { bankDetailData, userBankDetailUpdate, updateBankOtp} from '../../../actions/AccountAction'

class BankDetail  extends Component {

  constructor(props) {
    super(props);
    //this.props.user_detail.firstname='';
    this.state = {
          ifsc_code:'',
          bank_ac_number:'',
          bank_ac_holder_name:'',
          customer_vpa:'',
          fields: {},
          errors: {},
          api_data:false,
          otp_send:false
    };

    this.handleChange      = this.handleChange.bind(this);
    this.handleFormSubmit  = this.handleFormSubmit.bind(this);
    this.valueChange       = this.valueChange.bind(this);
    this.handleValidation  = this.handleValidation.bind(this);
    this.otpSend           = this.otpSend.bind(this);
  }

  componentDidMount()
  {  
   this.props.dispatch(bankDetailData());
  } 
  
  componentWillReceiveProps()
  {
     this.valueChange();
  }

  componentDidUpdate(prevProps)
  {
    if(this.props.bank_detail!==prevProps.bank_detail){
        this.valueChange();
      }

  }

  valueChange()
  {
    this.setState({
                  ifsc_code:this.props.bank_detail.ifsc_code,
                  bank_ac_number:this.props.bank_detail.bank_ac_number,
                  bank_ac_holder_name:this.props.bank_detail.bank_ac_holder_name,
                  customer_vpa:this.props.bank_detail.customer_vpa,
                  api_data:true
                });

    let fields = this.state.fields; 
      fields['bank_ac_holder_name'] = this.props.bank_detail.bank_ac_holder_name;
      fields['bank_ac_number']      = this.props.bank_detail.bank_ac_number;
      fields['ifsc_code']           = this.props.bank_detail.ifsc_code;
      fields['customer_vpa']        = this.props.bank_detail.customer_vpa;
      this.setState({fields});
  }


handleValidation(){
        let fields = this.state.fields; 
        let errors = {};
        let formIsValid = true;
        //Account Name
        if(!fields["bank_ac_holder_name"]){
           formIsValid = false;
           errors["bank_ac_holder_name"] = 'Account Name must be between 1 and 32 characters!';
        }
        if(typeof fields["bank_ac_holder_name"] !== "undefined"){
             if(fields["bank_ac_holder_name"].length < 2 || fields["bank_ac_holder_name"].length > 64){
                 formIsValid = false;
                 errors["bank_ac_holder_name"] = 'Account Name must be between 2 and 32 characters!';
             }          
        } 

        //Account Number
        if(!fields["bank_ac_number"]){
           formIsValid = false;
           errors["bank_ac_number"] = 'Please Enter Account Number!';
        }
        if(typeof fields["bank_ac_number"] !== "undefined"){
             if(fields["bank_ac_number"].length < 1){
                 formIsValid = false;
                 errors["bank_ac_number"] = 'Please Enter Account Number!';
             }          
        }

        //IFSC Code
        if(!fields["ifsc_code"]){
           formIsValid = false;
           errors["ifsc_code"] = 'Please Enter Ifsc code!';
        }

        if(typeof fields["ifsc_code"] !== "undefined"){
             if(fields["ifsc_code"].length < 1){
                 formIsValid = false;
                 errors["ifsc_code"] = 'Please Enter Ifsc code!';
             }          
        }

        //OTP
        if(!fields["otp"]){
           this.otpSend();
           formIsValid = false;
           errors["otp"] = 'Please enter 4 digit otp which has been sent on your mobile number or email.';
        }

        if(typeof fields["otp"] !== "undefined"){
             if(fields["otp"].length < 4){
                 this.otpSend();
                 formIsValid = false;
                 errors["otp"] = 'Please enter 4 digit otp which has been sent on your mobile number or email.';
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
        var form    = $('#edit_bank_detail_form');
        var  formData = new FormData();
        var  params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });
     var response = this.props.dispatch(userBankDetailUpdate(formData));
     var self = this;
       response.then(function(data){
          self.props.dispatch(bankDetailData());
        });
    }
     e.preventDefault();
  }

  otpSend()
  {
    if(!this.state.otp_send)
    {
      this.props.dispatch(updateBankOtp());
      this.setState({otp_send:true});
    }   
  }

 render(){
    return (
      <div>
        <div className="contner head_margin side-collapse-container">
        <section className="cart_box">
          <h2 className="about_title">My Bank Information</h2>
         <div className="col-xs-12">
         {this.state.api_data ?
              <form id="edit_bank_detail_form" onSubmit={this.handleFormSubmit}>
                  <div className="mobile_details_panel delivery_box">                      
                        <div className="new_add_popup_input_box account_info">
                          <input id="bank_ac_holder_name" name="bank_ac_holder_name" type="text" className="pr_info_input" value={this.state.bank_ac_holder_name} placeholder="Account Holder Name*" alt="Account Holder Name" onChange={this.handleChange.bind(this, "bank_ac_holder_name")} />
                          <span className="error">{this.state.errors["bank_ac_holder_name"]}</span>
                        </div>
                        <div className="new_add_popup_input_box account_info"> 
                          <input id="bank_ac_number" name="bank_ac_number" type="text" className="pr_info_input" value={this.state.bank_ac_number} placeholder="Account number *" alt="Account number" onChange={this.handleChange.bind(this, "bank_ac_number")} />
                           <span className="error">{this.state.errors["bank_ac_number"]}</span>
                        </div>
                        <div className="new_add_popup_input_box account_info"> 
                          <input id="ifsc_code" name="ifsc_code" type="text"  className="pr_info_input" value={this.state.ifsc_code} placeholder="Ifsc code *" alt="Ifsc code" onChange={this.handleChange.bind(this, "ifsc_code")} />
                          <span className="error">{this.state.errors["ifsc_code"]}</span>
                        </div>
                        <h2 className="about_title">Your UPI Details</h2>
                        <div className="new_add_popup_input_box account_info">
                          <input id="customer_vpa" name="customer_vpa" type="text" className="pr_info_input" value={this.state.customer_vpa} placeholder="UPI Vpa" alt="UPI Vpa" onChange={this.handleChange.bind(this, "customer_vpa")} />
                          <span className="error">{this.state.errors["customer_vpa"]}</span>
                        </div>
                        {this.state.otp_send ?
                        <div className="new_add_popup_input_box account_info">
                          <input id="otp" name="otp" type="text" className="pr_info_input" placeholder="OTP" alt="OTP" onChange={this.handleChange.bind(this, "otp")} />
                          <span className="error">{this.state.errors["otp"]}</span>
                        </div>
                        : ''}
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
         </section>
         <div className="clearfix"></div>

         </div>

         
       
   </div>
)}}


function mapStateToProps(state){
  return {
    bank_detail: state.accountReducer.bank_detail,
    actions: bindActionCreators(bankDetailData, updateBankOtp)
  };
}
export default connect(mapStateToProps)(BankDetail);