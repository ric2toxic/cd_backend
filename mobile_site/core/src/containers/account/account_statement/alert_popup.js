import React, { Component } from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import  './index.css'

class AlertPopup extends Component {

	render() {

		return (
          <Dialog
             fullScreen={false}
             open={this.props.alertPopupOpen}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.props.alert_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Note
            </DialogTitle>
            <DialogContent className="modal-body">
                     <div className="row">
                        <div className="col-sm-12 col-xs-12 col-md-8 col-lg-12" style={{fontSize:'14px'}}>
                        {this.props.important_note}
                        </div>
                     </div>
            </DialogContent>
         </Dialog>
		)
	}
}


export default withMobileDialog()(AlertPopup);