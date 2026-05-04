import React, { Component } from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import  './index.css'

class ConfirmPopup extends Component {

	render() {
		return (
          <Dialog
             fullScreen={false}
             open={this.props.confirmPopupOpen}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.props.confirm_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Alert
            </DialogTitle>
       
            <DialogContent className="modal-body" style={{fontSize:'14px', minWidth:'200px', textAlign:'center'}}>
             Are you sure to delete address from address book ?
            </DialogContent>

            <DialogActions>
              
               <button type="button" className="btn return_btn_popup pull-right" onClick={this.props.confirm_popup_close}>Cancel</button>
              
               &nbsp; &nbsp; 
             
               <button type="button" className="btn return_btn_popup pull-right" onClick={(e) => {this.props.handleDeleteAddress(this.props.delete_address_id)}}>Delete</button>
              
            </DialogActions>

         </Dialog>
		)
	}
}


export default withMobileDialog()(ConfirmPopup);