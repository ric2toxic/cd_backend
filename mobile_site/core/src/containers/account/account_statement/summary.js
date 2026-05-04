import React, { Component } from 'react'
import AlertPopup from './alert_popup'
import $ from 'jquery'

class Summary  extends Component {
 
  constructor(props)
  {
     super(props);
      this.state = {
        alertPopupOpen: false
      };
      
      this.alert_popup             = this.alert_popup.bind(this);
      this.alert_popup_close       = this.alert_popup_close.bind(this);
 }

   alert_popup()
  {
    this.setState({alertPopupOpen: 1});
  }

  alert_popup_close()
  {
    this.setState({alertPopupOpen: 0}); 
  } 

	render(){
			return (
               <div className="col-sm-12 no-padding">

                   <AlertPopup 
                      alertPopupOpen={this.state.alertPopupOpen} 
                      alert_popup_close={this.alert_popup_close}
                      important_note={this.props.important_note}
                       />

                   <div className="Total_summary_of_order">
                     <div className="col-xs-4">
                      <p>Total Debit</p>
                       <h4>{$('<div/>').html(this.props.account_summary.total_debits).text()}</h4>
                     </div>
            
                   <div className="col-xs-4 no-padding">
                     <p>Total Credit</p>
                      <h4>{$('<div/>').html(this.props.account_summary.total_credits).text()}</h4>
                    </div>
          
                   <div className="col-xs-4 no-padding">
                     <div className={this.props.account_summary.is_positive_bal ? "outstanding_bal" : "outstanding_bal negative_bal"}>
                     <p>Outstanding Balance <i onClick={this.alert_popup} className="fa fa-question-circle" aria-hidden="true"></i></p>
                    <h4>{$('<div/>').html(this.props.account_summary.balance).text()}</h4>
                  </div>
                </div>
                </div>
              </div>)
	}
}

export default (Summary);