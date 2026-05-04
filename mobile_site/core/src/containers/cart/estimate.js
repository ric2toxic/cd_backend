import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import Dialog from '@material-ui/core/Dialog'
import DialogContent from '@material-ui/core/DialogContent'
import DialogTitle from '@material-ui/core/DialogTitle'
import DialogActions from '@material-ui/core/DialogActions'
import withMobileDialog from '@material-ui/core/withMobileDialog'
import { EstimatePopupOpen } from '../../actions/CartAction'

class Estimate extends Component {
 
  constructor(props)
  {
     super(props);
      this.estimate_popup_close     = this.estimate_popup_close.bind(this);
  }

 estimate_popup_close()
  {
    this.props.dispatch(EstimatePopupOpen(0)); 
  } 

    render() {
        return (<div className="cart_top_bar">
                                  <div className="col-xs-12 top_right_box">
                                    <span className="col-xs-12 nopadding">ESTIMATE SHIPPING:</span>
                                    <input type="text" className="form-control top-pincode" name="estimate_pincode" id="estimate_pincode" placeholder="Enter Pincode " />
                                    <button type="button" className="btn btn-estimate" onClick={this.props.estimateShipping}>{this.props.language.text_estimate_shipping}</button>
                                  </div>
          <Dialog
             fullScreen={true}
             open={this.props.EstimatePopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.estimate_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              ESTIMATE SHIPPING
            </DialogTitle>
       
            <DialogContent className="modal-body">
           <div className="panel-body padding_top_bottem">
             <div id="shipping_method_header"></div>
                    <div className="clearfix"></div>
                    <div className="col-sm-12 cart_table nopadding">
                        <div id="shipping_method_table"></div>
                        <div id="ess_charges"></div>
                    </div>
            </div>
            </DialogContent>

            <DialogActions>
             <button type="button" className="btn deliver_btn" id="save_estimate_shipping" onClick={this.props.updateShippingMethod}>Save & Close</button>
            </DialogActions>
           </Dialog>

                </div>);
    }
}

function mapStateToProps(state){
  return {
    EstimatePopup: state.cartReducer.EstimatePopup,
    actions: bindActionCreators(EstimatePopupOpen)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(Estimate));