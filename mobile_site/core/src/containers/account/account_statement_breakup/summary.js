import React, { Component } from 'react'
import $ from 'jquery'

class Summary  extends Component {

	render(){
			return (
               <div className="col-sm-12 no-padding">
                   <div className="Total_summary_of_order">
                     <div className="col-xs-4">
                      <p>Total Debit</p>
                       <h4>{$('<div/>').html(this.props.total_debits).text()}</h4>
                     </div>
            
                   <div className="col-xs-4 no-padding">
                     <p>Total Credit</p>
                      <h4>{$('<div/>').html(this.props.total_credits).text()}</h4>
                    </div>
          
                   <div className="col-xs-4 no-padding">
                     <div className={this.props.is_positive_bal ? "outstanding_bal" : "outstanding_bal negative_bal"}>
                     <p>Order Balance</p>
                    <h4>{$('<div/>').html(this.props.balance).text()}</h4>
                  </div>
                </div>
                </div>
              </div>)
	}
}

export default (Summary);