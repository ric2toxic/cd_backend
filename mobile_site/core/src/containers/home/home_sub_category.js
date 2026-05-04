import React, { Component } from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { Link } from 'react-router-dom'
import Api from '../../api/Api'
import $ from 'jquery'
import  './index.css'

class HomeSubCategory extends Component {

	render() {
		return (
          <Dialog
             fullScreen={true}
             open={this.props.SubCategoryPopup}
             aria-labelledby="responsive-dialog-title"
             className="sub_menu_popup"
            >
            <button type="button" className="popup_close close" onClick={this.props.SubCategoryPopupClose}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
             {$('<div/>').html(this.props.SubCategoryData.link_title).text()}
            </DialogTitle>
            
            {this.props.SubCategoryData.children ?
            <DialogContent className="modal-body">
              <ul className="menu_popup">
                {
                this.props.SubCategoryData.children.map((submenu, index) => {
                    return(<li><Link to={Api.folder_path+submenu.href}> <img src={submenu.image} alt="" style={{width:'8%'}} /> {$('<div/>').html(submenu.link_title).text()}</Link></li>)
                })
               }
              </ul>
            </DialogContent>
            : ''}
         </Dialog>
		)
	}
}

export default withMobileDialog()((HomeSubCategory));