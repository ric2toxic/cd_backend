import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { getShippingPreferencesForOrderDetail, ShippingPreferencesPopupOpen, setShippingPreferences } from '../../../actions/AccountAction';
import  './index.css'
import $ from 'jquery'

class ShippingPreferences extends Component {

  constructor(props)
  {
     super(props);
      this.shipping_preferences_close     = this.shipping_preferences_close.bind(this);
      this.handleFormSubmit               = this.handleFormSubmit.bind(this);
  }

  shipping_preferences_close()
  {
    this.props.dispatch(ShippingPreferencesPopupOpen(0)); 
  }

  handleFormSubmit()
  {
    $('body').removeClass('loaded').addClass('loading');
        var  formData = new FormData();
        formData.append("no_wsb_tape", 0);
        formData.append("courier_preference", "");
        formData.append("no_invoice_with_shipment", 0);
        formData.append('update_preferences', 1);
        formData.append('order_id', this.props.order_id);
        formData.append('suborder_id', this.props.suborder_id);  

         var form    = $('#shipping_form');
        var  params   = form.serializeArray();
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });

    var response = this.props.dispatch(setShippingPreferences(formData));
    var self = this;
       response.then(function(data){
          self.props.dispatch(ShippingPreferencesPopupOpen(0)); 
        });
  } 

	render() {
		return (
          <Dialog
             fullScreen={true}
             open={this.props.ShippingPreferencesPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.shipping_preferences_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Shipping Preferences
            </DialogTitle>
       
            <DialogContent className="modal-body">
              <form id="shipping_form" onSubmit={this.handleFormSubmit}>
              <div style={{color: '#000', marginTop: '10px', fontSize:'16px'}}>
                <label><strong>Shipping Preferences</strong></label>
              </div>

              <div className="checkbox_area">
              <input type="checkbox" id="no_wsb_tape" name="no_wsb_tape" value="1" style={{width:'auto'}} /> 
              <label style={{verticalAlign: 'super', marginLeft: '5px', fontWeight: '200'}}>Do not use wholesalebox packing tape.</label> 
              </div>
              
               <div className="checkbox_area">
               <input type="checkbox" id="no_invoice_with_shipment" name="no_invoice_with_shipment" value="1" style={{width:'auto'}} /> 
               <label style={{verticalAlign: 'super', marginLeft: '5px', fontWeight: '200'}}>Do not send invoice with shipment.</label>   
               </div>
               
               <div style={{color: '#000', marginTop: '10px', fontSize:'16px'}}>
                <label><strong>Courier Partner Preference</strong></label>
              </div>
               
                {
                 $.map(this.props.shipping_preferences.all_courier_preference, function(courier_partners, index) {
                 return(<div className="checkbox_area" key={index}>
                             <input type="radio" name="courier_preference" value={courier_partners.courier_name} style={{width:'auto'}} /> 
                             <label style={{verticalAlign: 'super', marginLeft: '5px', fontWeight: '200'}}>{courier_partners.courier_name}</label>   
                         </div>)   
                
                })
               }
               
              <div style={{color: '#000', marginTop: '10%', fontSize:'16px'}}>
                <label><strong>Please note, we will do our best to accommodate your request for courier preference, however a specific courier preference is not Gauranteed.</strong></label>
              </div>
             </form>
            </DialogContent>

            <DialogActions>
              <button type="submit" onClick={this.handleFormSubmit}  className="btn add_btn_new_popup pull-right" data-direction='right'>Save</button>
            </DialogActions>

         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    ShippingPreferencesPopup: state.accountReducer.ShippingPreferencesPopup,
    actions: bindActionCreators(ShippingPreferencesPopupOpen, getShippingPreferencesForOrderDetail, setShippingPreferences)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(ShippingPreferences));