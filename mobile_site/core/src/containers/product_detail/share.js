import React, { Component } from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import {FacebookShareButton, GooglePlusShareButton, TwitterShareButton, WhatsappShareButton, PinterestShareButton, EmailShareButton, TelegramShareButton} from 'react-share';
import  './index.css'

class Share extends Component {

	render() {
		return (
          <Dialog
             fullScreen={false}
             open={this.props.SharePopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.props.SharePopupClose}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
             Share this product
            </DialogTitle>
       
            <DialogContent className="modal-body">
            <WhatsappShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp
            </WhatsappShareButton>

            <FacebookShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-facebook" aria-hidden="true"></i> Facebook
            </FacebookShareButton>

            <TwitterShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-twitter" aria-hidden="true"></i> Twitter
            </TwitterShareButton>

            <GooglePlusShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-google" aria-hidden="true"></i> Google
            </GooglePlusShareButton>
           
            <PinterestShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-pinterest" aria-hidden="true"></i> Pinterest
            </PinterestShareButton>

            <TelegramShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-telegram" aria-hidden="true"></i> Telegram
            </TelegramShareButton>

            <EmailShareButton className="share_btn" url={this.props.url} title={this.props.product_name} image={this.props.product_image}>
              <i className="fa fa-envelope" aria-hidden="true"></i> Email
            </EmailShareButton>
            </DialogContent>

         </Dialog>
		)
	}
}

export default withMobileDialog()((Share));