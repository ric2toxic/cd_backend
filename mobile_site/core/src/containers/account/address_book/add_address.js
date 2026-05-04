import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import $ from 'jquery'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { AddressPopupOpen, userAddAddress, userAddressListData, userAddressBookUpdate } from '../../../actions/AccountAction';
import {state_listData, pincodeAddressData} from '../../../actions/LoginAction';
import  './index.css'

class AddAddress extends Component {

  constructor(props)
  {
     super(props);

     this.state = {
          fields: this.props.fields,
          errors: this.props.fields,
          address_telephone: this.props.address_telephone
     };

     this.address_popup_close           = this.address_popup_close.bind(this);
     this.get_state_list                = this.get_state_list.bind(this);
     this.handleFormSubmit              = this.handleFormSubmit.bind(this);
  }

  componentWillReceiveProps()
  {  
     this.setState({fields:this.props.fields});
     this.setState({errors:this.props.errors});
     this.setState({address_telephone:this.props.address_telephone});
  }

  address_popup_close()
  {
    this.props.dispatch(AddressPopupOpen(false)); 
  } 

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;
        //Name
        if(!fields["name"]){
           formIsValid = false;
           errors["name"] = 'Please provide your name';
        }
        if(typeof fields["name"] !== "undefined"){
             if(!fields["name"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["name"] = "Only letters";
             }
             if(fields["name"].length < 2 || fields["name"].length > 64){
                 formIsValid = false;
                 errors["name"] = 'Name must be between 2 and 64 characters!';
             }          
        } 
   
        //address
        if(!fields["address_1"]){
           formIsValid = false;
           errors["address_1"] = 'Address must be between 3 and 128 characters!';
        }  
        if(typeof fields["address_1"] !== "undefined"){
             if(fields["address_1"].length < 3 || fields["address_1"].length > 128){
                 formIsValid = false;
                 errors["address_1"] = 'Address must be between 3 and 128 characters!';
             }          
        }

        //address telephone
        if(typeof fields["address_telephone"] !== "undefined")
        {
          if(fields["address_telephone"].length > 0)
          {
             if(!fields["address_telephone"].match(/^[0-9,]+$/) || fields["address_telephone"].length < 5)
             {
                 formIsValid = false;
                 errors["address_telephone"] = 'Please provide valid mobile number!';
             }  
          }           
        } 

        //postcode
        if(!fields["postcode"]){
           formIsValid = false;
           errors["postcode"] = 'Please enter postcode!';
        } 
        if(typeof fields["postcode"] !== "undefined"){
             if(fields["postcode"].length < 2 || fields["postcode"].length > 10){
                 formIsValid = false;
                 errors["postcode"] = 'Invalid postcode!';
             }          
        } 

        //country
        if(!fields["country_id"]){
           formIsValid = false;
           errors["country_id"] = 'Please select a country!';
        }  
        if(typeof fields["country_id"] !== "undefined"){
             if(fields["country_id"] < 1)
             {
                 formIsValid = false;
                 errors["address_1"] = 'Please select a country!';
             }          
        }

        //zone
        if(!fields["zone_id"]){
           formIsValid = false;
           errors["zone_id"] = 'Please select a region / state!';
        }  
        if(typeof fields["zone_id"] !== "undefined"){
             if(fields["zone_id"] < 1)
             {
                 formIsValid = false;
                 errors["zone_id"] = 'Please select a region / state!';
             }          
        }

        //city
        if(!fields["city"]){
           formIsValid = false;
           errors["city"] = 'City must be between 2 and 128 characters!';
        }  
        if(typeof fields["city"] !== "undefined"){
             if(fields["city"].length < 2 || fields["city"].length > 128)
             {
                 formIsValid = false;
                 errors["city"] = 'City must be between 2 and 128 characters!';
             }          
        } 

       this.setState({errors: errors});
       return formIsValid;
   }

   handleChange(field, event) 
   {
    let fields = this.state.fields;
     if(event.target.name === 'postcode') { this.postcode(); }
     else { fields[field] = event.target.value; } 
     this.setState({fields});
     this.handleValidation();
  
   }

  handleFormSubmit(e)
  {
    if(this.handleValidation())
    { 
       $('body').removeClass('loaded').addClass('loading');
       var address_id        = $("#add_address_form input[name=address_id]").val();
      // var address_telephone = this.state.address_telephone.join();
       
        var form    = $('#add_address_form');
        var  formData = new FormData();
        var  params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });
      //formData.append('address_telephone', address_telephone);
       var response;
       if(address_id && address_id > 0)
       {
          response = this.props.dispatch(userAddressBookUpdate(formData));
       }
       else
       {
         response = this.props.dispatch(userAddAddress(formData));
       }
       var self = this;
       response.then(function(data){
          self.props.dispatch(userAddressListData());
          self.props.dispatch(AddressPopupOpen(false));
        });
    }  
    e.preventDefault();
  }

      get_state_list(zone_id=0) {

        let country_id = $("#country_id").val();
        let fields = this.state.fields;
        fields['country_id'] = country_id;
        fields['zone_id'] = '';
        this.setState({fields});
        this.handleValidation();

        $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
        var response =this.props.dispatch(state_listData(country_id));
        response.then(function(data){
          $("i.fa-circle-o-notch").remove();
          var result = '<option value="">'+data['zone_title']+'</option>';
              for(var i=0; i<data['zone_data'].length; i++)
                {
                  result = result+'<option value="'+data['zone_data'][i].zone_id+'">'+data['zone_data'][i].name+'</option>';
                }
               $(" #zone_id").html(result);
               if(zone_id > 0) { $("#zone_id option[value=" + zone_id + "]").prop("selected",true); }
         })
        .catch(function() {
          console.log('data fetch error');
         });
       }

  postcode() 
   {
      var self = this;
      var value = $("#postcode").val();

      let fields = this.state.fields;
        fields['postcode'] = value;
        this.setState({fields});
        this.handleValidation();

      if (value && /^\d{6,}$/.test(value.trim()) && value.length === 6)
      {
          var response =self.props.dispatch(pincodeAddressData(value));
          response.then(function(data){
              if(!data['error'])
                  {
                    if(data['zone_id'] === '')
                    {
                      $("#country_id option[value='99'").prop('selected',true);
                      self.get_state_list();
                      $("#city").val('');
                      $("#city").prev('label').css('margin-top', '20px');
                      fields['city']       = '';
                      fields['zone_id']    = '';
                      fields['country_id'] = 99;
                      self.setState({fields});
                      self.handleValidation();
                    }
                    else
                    {
                      $("#city").prev('label').css('margin-top', '0px');
                      $("#city").val(data['city']);
                      $("#country_id option[value=" + data['country_id'] + "]").prop("selected",true);
                      self.get_state_list(data['zone_id']);
                      
                      fields['city']       = data['city'];
                      fields['zone_id']    = data['zone_id'];
                      fields['country_id'] = data['country_id'];
                      self.setState({fields});
                      self.handleValidation();
                    }
                  }
           })
          .catch(function() {
             console.log('data fetch error');
            });
      }
  }

	render() {
/*let tags_data = {
  id: 'address_telephone',
  name: 'address_telephone',
  placeholder: 'Mobile Number'
};*/
		return (
          <Dialog
             fullScreen={true}
             open={this.props.AddressPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.address_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
             New Address
            </DialogTitle>
       
            <DialogContent className="modal-body">
              <form id="add_address_form">
              <input id="address_id" type="hidden"  name="address_id" />
            <div className="delivery_box">                      
              <div className="new_add_popup_input_box"> 
                <input id="name" type="text" className="pr_info_input" placeholder="Enter your Name" alt="Name" name="name" onChange={this.handleChange.bind(this, "name")} />
                 <span className="error">{this.state.errors["name"]}</span> 
              </div>
              <div className="new_add_popup_input_box"> 
                <input id="company" type="text" className="pr_info_input" placeholder="Enter your Business Name" alt="Business Name"  name="company" onChange={this.handleChange.bind(this, "company")} />
                <span className="error">{this.state.errors["company"]}</span> 
              </div>
              <div className="new_add_popup_input_box"> 
                <input id="address_1" type="text" className="pr_info_input" placeholder="Enter your Address" alt="Address"  name="address_1" onChange={this.handleChange.bind(this, "address_1")} />
                 <span className="error">{this.state.errors["address_1"]}</span> 
              </div>
              <div className="new_add_popup_input_box"> 
                <input id="address_2" type="text" className="pr_info_input" placeholder="Enter your Address2" alt="Address"  name="address_2" onChange={this.handleChange.bind(this, "address_2")} />
                <span className="error">{this.state.errors["address_2"]}</span> 
              </div>

              <div className="new_add_popup_input_box"> 
                <input id="address_telephone" type="text" className="pr_info_input" placeholder="Mobile Number"  name="address_telephone" onChange={this.handleChange.bind(this, "address_telephone")} />
                <span className="error">{this.state.errors["address_telephone"]}</span>
              </div>

              <div className="new_add_popup_input_box"> 
                <input id="postcode" type="text" className="pr_info_input" placeholder="Enter your Post Code" alt="Post Code"  name="postcode" onChange={this.handleChange.bind(this, "postcode")} />
                <span className="error">{this.state.errors["postcode"]}</span>
              </div>
              <div className="new_add_popup_input_box"> 
                <select name="country_id" id="country_id" className="address_select_input" onChange={this.get_state_list}>
                  <option value=""> --- Please Select Country --- </option>
                   {
                      this.props.country_list_model.country_data ?
                      this.props.country_list_model.country_data.map((country, index) => {
                        return (<option key={index} value={country.country_id}>{country.name}</option>)
                        })
                        : ''
                    }
                 </select>
                 <span className="error">{this.state.errors["country_id"]}</span>
              </div>
              <div className="new_add_popup_input_box"> 
                <select name="zone_id" id="zone_id" className="address_select_input" onChange={this.handleChange.bind(this, "zone_id")}>
                  <option value="">--- Please Select Region / State ---</option>
                </select>
                 <span className="error">{this.state.errors["zone_id"]}</span>
              </div>
              <div className="new_add_popup_input_box"> 
                <input id="city" type="text" className="pr_info_input" placeholder="Enter your City Name" alt="City Name" name="city" onChange={this.handleChange.bind(this, "city")} />
                <span className="error">{this.state.errors["city"]}</span>
              </div>

             <div className="form-group">
             <label className="col-xs-12 control-label nopadding" style={{fontSize:'14px'}}>Default Address</label>
             <div className="col-xs-12 nopadding">
             <div className="new_add_popup_input_box"> 
                <input type="radio" name="default" value="1" className="address_radio" id="default_yes" /> Yes
                <input type="radio" name="default" value="0" className="address_radio" id="default_no" /> No
                </div>
               </div>
             </div>
                 <div className="clearfix"></div>
             </div>
            </form>
            </DialogContent>
           
            <DialogActions>
                 <button type="submit" onClick={this.handleFormSubmit}  className="btn deliver_btn pull-right" data-direction='right'>Save</button>
            </DialogActions>
         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    country_list_model: state.loginReducer.country_list,
    AddressPopup: state.accountReducer.AddressPopup,
    actions: bindActionCreators(AddressPopupOpen, state_listData, pincodeAddressData, userAddAddress, userAddressListData, userAddressBookUpdate)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(AddAddress));