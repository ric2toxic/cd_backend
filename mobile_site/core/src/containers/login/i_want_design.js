import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import custom from '../../custom/custom';
import $ from 'jquery';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { DesignPopupOpen } from '../../actions/LoginAction';
import { i_want_design } from '../../actions/LoginAction'
import  './index.css'

class IWantDesign extends Component {

  constructor(props)
  {
     super(props);
      this.design_popup_close     = this.design_popup_close.bind(this);
      this.i_want_design_comment  = this.i_want_design_comment.bind(this);
  }

  design_popup_close()
  {
    $('#want_design_warning').hide(); 
    this.props.dispatch(DesignPopupOpen(0)); 
  }

   i_want_design_comment()
   {  
      var customer_mobile  = $('#design_customer_mobile').val();
      var product_id       = $('#want_design_product_id').val();
      var product_status   = $('#want_design_product_status').val();
      var popup_comment    = $('#want_design_comment').val();
      var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
      var mobile_pattern = new RegExp(/^\d{10}$/);

      if(custom.getCookie("customer_mobile") !== '')
      {
        customer_mobile = custom.getCookie("customer_mobile");
      }

      if (!email_pattern.test(customer_mobile) && !mobile_pattern.test(customer_mobile))
      {
        $('#want_design_warning').html('Please provide correct email address or mobile');
        $('#want_design_warning').show();
        return;
      }  
      else if(popup_comment.length < 2)
      {
        $('#want_design_warning').html("Comment should have atleast two characters.");
        $('#want_design_warning').show();
        return;
      }
    
    
      this.props.dispatch(i_want_design(customer_mobile, product_id, product_status, popup_comment));
      this.props.dispatch(DesignPopupOpen(0));
    
   } 

	render() {
		return (
          <Dialog
             fullScreen={true}
             open={this.props.DesignPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" data-dismiss="modal" onClick={this.design_popup_close} id="login_verify_popup_close">&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              I want this design.
            </DialogTitle>
       
              <DialogContent className="modal-body">
                    <div id="want_design_warning" className="danger_msg"></div>
                   <div className="otp_model">
                       <input type="hidden" id="want_design_product_id" value=""/>
                       <input type="hidden" id="want_design_product_status" value="out of stock"/>
                        <br/>
                        <p>The product is out of stock but you could still ask factory if they can provide it.</p>
                        <p>Please specify how many pieces and what sizes/colors you like to have:</p>
                        <input type="text" name="customer_mobile" id="design_customer_mobile" placeholder="Mobile OR Email" style={{display:'none'}} />
                        <textarea rows="4" id="want_design_comment" maxLength="300" ></textarea>
                    </div>
            </DialogContent>

            <DialogActions className="modal-footer">
              <button className="btn deliver_btn pull-left cancel_want_design" onClick={this.design_popup_close} style={{backgroundColor:'#ccc', borderColor:'#ccc'}}>Cancel</button>
               <button className="btn deliver_btn pull-right" id="submit_want_design" onClick={()=>this.i_want_design_comment()}>Send</button>
          </DialogActions>

         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    DesignPopup: state.loginReducer.DesignPopup,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(DesignPopupOpen,i_want_design)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(IWantDesign));