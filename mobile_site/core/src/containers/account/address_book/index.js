import React, { Component } from 'react'
import $ from 'jquery'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import  './index.css'
import {userAddressListData, userAddressDelete, AddressPopupOpen, userAddressBookUpdate} from '../../../actions/AccountAction'
import {state_listData} from '../../../actions/LoginAction';
//import UpdateForm from './update_form';
import AddAddress from './add_address';
import ConfirmPopup from './confirm_popup'

class AddressBook  extends Component {

  constructor(props) {
    super(props);
    this.state = {
      fields: {},
      errors: {},
      address_telephone:[],
      confirmPopupOpen: false,
      delete_address_id:false
    };

    this.handleDeleteAddress      = this.handleDeleteAddress.bind(this);
    this.address_popup_open       = this.address_popup_open.bind(this);
    this.edit_address_popup_open  = this.edit_address_popup_open.bind(this);
    this.get_state_list           = this.get_state_list.bind(this);
    this.handleFormSubmit         = this.handleFormSubmit.bind(this);
    this.confirm_popup            = this.confirm_popup.bind(this);
    this.confirm_popup_close      = this.confirm_popup_close.bind(this);
  }

 componentDidMount()
  {   
    this.props.dispatch(userAddressListData());

    $(function(){
      $(document).click(function(){  
       $(".dropdown_menu").addClass("hide");
       });
      $(document).delegate('.address_icons', 'click', function(e)
       { 
        e.stopPropagation();
        var address_id = $(this).attr("id");
        
        if($("#dropdown_menu_"+address_id).hasClass( "hide" ))
          {
            $(".dropdown_menu").addClass("hide");
            $("#dropdown_menu_"+address_id).removeClass("hide");
          }
        else
          {
            $(".dropdown_menu").addClass("hide");
          }
       });
    });
  } 

  confirm_popup(delete_address_id)
  {
    this.setState({delete_address_id: delete_address_id});
    this.setState({confirmPopupOpen: true});
  }

  confirm_popup_close()
  {
    this.setState({confirmPopupOpen: false}); 
  } 

address_popup_open()
{
  this.setState({fields:{}});
  this.setState({errors:{}});
  this.setState({address_telephone:[]});
   let self = this;
   setTimeout(function()
    { 
      self.props.dispatch(AddressPopupOpen(true)); 
      $(".popup-title").children("h2").html("New Address");
      $("#default_no").attr('checked', true);
     }, 100);
} 

edit_address_popup_open(address)
{  
   let self              = this;
   let fields            = this.state.fields;
   let address_telephone = this.state.address_telephone;
   fields['name']       = address.firstname+' '+address.lastname;
   fields['address_1']  = address.address_1;
   fields['postcode']   = address.postcode;
   fields['country_id'] = address.country_id;
   fields['zone_id']    = address.zone_id;
   fields['city']       = address.city;
   address_telephone    = address.address_telephone;



   this.setState({fields});
   this.setState({address_telephone});

   setTimeout(function()
    { 
      self.props.dispatch(AddressPopupOpen(true));
      $(".popup-title").children("h2").html("Update Address");
      $("#add_address_form input[name=address_id]").val(address.address_id);
      $("#add_address_form input[name=name]").val(address.firstname+' '+address.lastname);
      $("#add_address_form input[name=company]").val(address.company);
      $("#add_address_form input[name=address_1]").val(address.address_1);
      $("#add_address_form input[name=address_2]").val(address.address_2);
      $("#address_telephone").val(address.address_telephone);
      $("#add_address_form input[name=postcode]").val(address.postcode);
      $("#country_id option[value=" + address.country_id + "]").prop("selected",true);
      $("#add_address_form input[name=city]").val(address.city);
      self.get_state_list(address.country_id, address.zone_id);
     if(address.default)
          { $("#default_yes").attr('checked', true); }
     else { $("#default_no").attr('checked', true);  }

     }, 200);
} 

 handleDeleteAddress(formId){
     this.setState({confirmPopupOpen: false}); 
     $('body').removeClass('loaded').addClass('loading');
     this.props.dispatch(userAddressDelete(formId));
  }

  handleFormSubmit(address)
  {    
       $('body').removeClass('loaded').addClass('loading');
        var  formData = new FormData();
        formData.append('address_id', address.address_id);
        formData.append('name', address.firstname+' '+address.lastname);
        formData.append('company', address.company);
        formData.append('address_1', address.address_1);
        formData.append('address_2', address.address_2);
        formData.append('address_telephone', address.address_telephone);
        formData.append('postcode', address.postcode);
        formData.append('country_id', address.country_id);
        formData.append('zone_id', address.zone_id);
        formData.append('city', address.city);
        formData.append('default', 1);
       
       var response = this.props.dispatch(userAddressBookUpdate(formData));
       var self = this;
       response.then(function(data){
          self.props.dispatch(userAddressListData());
        });
  } 

 get_state_list(country_id, zone_id) 
    {
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

 render(){
let self = this;

if(this.props.address_list)
{
 return (
  <div className="container-fluid head_margin">
   <div className="row">
      <div className="cart_full_box">
          <div className="col-xs-12">
              <div className="panel-group" id="accordion">
                  <section className="cart_box">
                    <h2 className="about_title">My Address Book ({this.props.address_list.length})</h2>
                    <h2 className="about_title add_address" onClick={this.address_popup_open}><i className="fa fa-plus" aria-hidden="true"></i> &nbsp; Add New Address</h2>
                    <div id="collapsefour" className="panel-collapse">
                      <div className="panel-body nopadding">
                        <div id="accordion_payment" className="panel-group payment_view_box">
                        { this.props.address_list.map((data, index) => {
                            return(
                          <div key={index} className="panel panel-default address_box" id={"Address_"+data.address_details.address_id}>
                              <div className="panel-collapse n">
                              
                                 <div className="panel-heading">  
                                  <h4 className="panel-title" >                                      
                                      {data.address_details.firstname} {data.address_details.lastname}
                                      <span className="default">{data.address_details.default ? '(Default)' : ''}</span>
                                    
                                    <div className="address_icons" id={data.address_details.address_id}>
                                      <i className="fa fa-ellipsis-v" aria-hidden="true"></i>
                                       <ul className="dropdown_menu hide" id={"dropdown_menu_"+data.address_details.address_id}>
                                        <li onClick={()=>self.handleFormSubmit(data.address_details)}> Set Default</li>
                                        <li onClick={()=>this.edit_address_popup_open(data.address_details)}>Edit</li>
                                        <li onClick={(e) => {this.confirm_popup(data.address_details.address_id)}}>Remove</li>
                                       </ul>
                                    </div>   
                                  </h4>
                                  </div>

                                  <div className="panel-body" id="online" style={{padding:'10px 15px'}}>
                                    <p>{data.address_details.company}</p>
                                    <p>{data.address_details.address_1} {data.address_details.address_2} {data.address_details.city} {data.address_details.postcode} {data.address_details.zone} {data.address_details.country}</p>
                                    
                                    <div className="clearfix"></div>
                                  </div>

                              </div>
                          </div>
                                //return finish
                              )
                            })
                            //Loop finish
                           }

                         </div>     
                      </div>
                    </div>
                  </section>


                 </div>
                </div>
                <div className="clearfix"></div>
             </div>
           </div>
           <AddAddress fields={this.state.fields} errors={this.state.errors} address_telephone={this.state.address_telephone} />
           <ConfirmPopup confirmPopupOpen={this.state.confirmPopupOpen} confirm_popup_close={this.confirm_popup_close} handleDeleteAddress={this.handleDeleteAddress} delete_address_id={this.state.delete_address_id} />
          </div>)
}
else
{
  return(<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)
}

}}


function mapStateToProps(state){
  return {
    account_success_message: state.accountReducer.account_success_message,
    address_list: state.accountReducer.address_list,
    country_list: state.loginReducer.country_list,
    form_detail: state.accountReducer.form_detail,
    actions: bindActionCreators(state_listData,userAddressListData,AddressPopupOpen,userAddressBookUpdate)
  };
}

export default connect(mapStateToProps)(AddressBook);