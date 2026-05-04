import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { ConfirmPopupOpen } from '../../actions/CartAction';
import  './index.css'

class ConfirmPopup extends Component {

  constructor(props)
  {
     super(props);
      this.confirm_popup_close     = this.confirm_popup_close.bind(this);
  }

  confirm_popup_close()
  {
    this.props.dispatch(ConfirmPopupOpen(0)); 
  } 

	render() {
		return (
          <Dialog
             fullScreen={false}
             open={this.props.ConfirmPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.confirm_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Alert
            </DialogTitle>
       
            <DialogContent className="modal-body" id="confirm_body">
           
            </DialogContent>

            <DialogActions  id="confirm_footer">
            </DialogActions>

         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    ConfirmPopup: state.cartReducer.ConfirmPopup,
    actions: bindActionCreators(ConfirmPopupOpen)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(ConfirmPopup));