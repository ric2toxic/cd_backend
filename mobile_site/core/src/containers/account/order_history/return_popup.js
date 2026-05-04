import React, { Component } from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import  './index.css'

class ReturnPopup extends Component {

	render() {
		return (
          <Dialog
             fullScreen={false}
             open={this.props.returnPopupOpen}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.props.return_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Alert
            </DialogTitle>
       
            <DialogContent className="modal-body" style={{fontSize:'14px', minWidth:'200px', textAlign:'center'}}>
             Return to using mobile <br />
             please download our app
            </DialogContent>

            <DialogActions>
               <a rel="noopener noreferrer" href="https://play.google.com/store/apps/details?id=in.wholesalebox" target="_blank">
               <button type="button" className="btn return_btn_popup pull-right">Android</button>
               </a> 
               &nbsp; &nbsp; 
               <a rel="noopener noreferrer" href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank">
               <button type="button" className="btn return_btn_popup pull-right">IOS</button>
               </a> 
            </DialogActions>

         </Dialog>
		)
	}
}


export default withMobileDialog()(ReturnPopup);