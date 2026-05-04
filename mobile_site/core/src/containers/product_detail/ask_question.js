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
import { QuestionPopupOpen } from '../../actions/ProductDetailAction';
import { ask_question } from '../../actions/ProductDetailAction'
import  './index.css'

class AskQuestion extends Component {

  constructor(props)
  {
     super(props);
      this.question_popup_close     = this.question_popup_close.bind(this);
      this.ask_question_comment     = this.ask_question_comment.bind(this);
  }

  question_popup_close()
  {
    $('#want_design_warning').hide(); 
    this.props.dispatch(QuestionPopupOpen(0)); 
  }

  ask_question_comment()
  {
      var product_id        = $('#Question_product_id').val();
      var customer_name     = $('#question_customer_name').val();
      var telephone         = $('#question_telephone').val();
      var email             = $('#question_email').val();
      var popup_question    = $('#popup_question').val();
      var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
      var mobile_pattern = new RegExp(/^\d{10}$/);

      if(custom.getCookie("customer_mobile") === '')
      {
         if (customer_name === '')
         {
          $('#ask_question_warning').html('Fill out all the given fields to ask a question.');
          $('#ask_question_warning').show();
          return;
         }

         if (!email_pattern.test(email) && !mobile_pattern.test(telephone)  )
         {
          $('#ask_question_warning').html('Please provide your Mobile No or Email');
          $('#ask_question_warning').show();
          return;
         }
      }

       if(popup_question.length < 10)
      {
        $('#ask_question_warning').html("Comment should have atleast 10 characters.");
        $('#ask_question_warning').show();
        return;
      }

    
      this.props.dispatch(ask_question(product_id, customer_name, telephone, email, popup_question));
      this.props.dispatch(QuestionPopupOpen(0));
  }


	render() {
		return (
          <Dialog
             fullScreen={true}
             open={this.props.QuestionPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.question_popup_close} id="login_verify_popup_close">&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Ask a question about this product.
            </DialogTitle>
       
              <DialogContent className="modal-body">
                    <div id="ask_question_warning" className="danger_msg"></div>
                     <input type="hidden" name="product_id" id="Question_product_id" value=""/>
                    <div className="otp_model">
                         <input type="text" name="customer_name" id="question_customer_name" placeholder="Please Enter your Name" style={{display:'none'}} />
                         <input type="text" name="telephone" id="question_telephone" placeholder="Please Enter your Mobile" style={{display:'none'}} />
                         <input type="text" name="email" id="question_email" placeholder="Please Enter your Email ID" style={{display:'none'}} />
                        <textarea rows="6" name="popup_question" id="popup_question" maxLength="300" placeholder="Enter your Question"></textarea>
                    </div>
            </DialogContent>

            <DialogActions className="modal-footer">
              <button className="btn deliver_btn pull-left cancel_want_design" onClick={this.question_popup_close} style={{backgroundColor:'#ccc', borderColor:'#ccc'}}>Cancel</button>
              <button className="btn deliver_btn pull-right" id="submit_ask_question" onClick={()=>this.ask_question_comment()}>Submit</button>
          </DialogActions>

         </Dialog>
		)
	}
}

function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    QuestionPopup: state.product_detailReducer.QuestionPopup,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(QuestionPopupOpen,ask_question)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(AskQuestion));