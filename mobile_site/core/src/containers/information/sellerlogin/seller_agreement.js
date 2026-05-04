import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import {  sellerAgreementData, DialogOpen } from '../../../actions/InformationAction';
import  './index.css'

class SellerAgreement extends Component {

    constructor(props)
   {
     super(props);
     this.dailogClose = this.dailogClose.bind(this);
   }
     
    componentWillMount() 
    {   
      this.props.dispatch(sellerAgreementData());
    }

   dailogClose()
   {
     this.props.dispatch(DialogOpen(0));
   }

	render() {
		return (
          <Dialog
             fullScreen={true}
             open={this.props.dialog_open}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup success_cart"
            >
            <button type="button" className="popup_close close" onClick={this.dailogClose}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Seller Agreement
            </DialogTitle>

              <DialogContent className="modal-body">
                   <div className="otp_model">
                      {this.props.seller_agreement_data.description ?
                        <section className="col-xs-12 contact_sanction privacy_text" dangerouslySetInnerHTML={{ __html: this.props.seller_agreement_data.description }} />
                        :'please wait...'}
                    </div>
            </DialogContent>

         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    dialog_open: state.informationReducer.dialog_open,
    seller_agreement_data: state.informationReducer.seller_agreement_data,
    actions: bindActionCreators(sellerAgreementData, DialogOpen)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(SellerAgreement));