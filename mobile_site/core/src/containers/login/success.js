import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'

import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { SuccessPopupOpen } from '../../actions/LoginAction';
import Api from '../../api/Api';
import  './index.css'

class Success extends Component {

  constructor(props)
  {
     super(props);
      this.success_popup_close     = this.success_popup_close.bind(this);
  }

  success_popup_close()
  {
    this.props.dispatch(SuccessPopupOpen(0));     
  }

	render() {
		return (
          <Dialog
             fullScreen={true}
             open={this.props.SuccessPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup success_cart"
            >

            <DialogTitle className="address_popup_head popup-title">
              Congratulation
            </DialogTitle>

              <DialogContent className="modal-body">
                  <div className="success_msg cart_shopping_success">Your mobile number verified successfully.</div>
                   <div className="otp_model">
                      <form method="get">
                          <button id="continue_shopping_btn" onClick={this.success_popup_close} className="btn deliver_btn pull-right" type="button" style={{fontSize:'12px', marginLeft:'10px'}}>Continue Shopping</button>
                          <Link to={Api.folder_path+"cart"} onClick={this.success_popup_close}> 
                         <button style={{fontSize:'12px'}}  id="go_to_cart_btn" className="btn deliver_btn pull-left" type="button">Go To Cart</button>
                       </Link>
                     </form>
                    </div>
            </DialogContent>

         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    SuccessPopup: state.loginReducer.SuccessPopup,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(SuccessPopupOpen)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(Success));