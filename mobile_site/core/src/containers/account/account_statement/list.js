import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import Api from '../../../api/Api'
import NoOrders from '../../loading/no_orders';
import $ from 'jquery'

class List  extends Component {	

	render(){
    let self = this;
		return (
	    	<section>	 
         {this.props.orders && this.props.orders.length > 0 ? 
          $.map(this.props.orders, function(order, index) {
           return (<div className="col-xs-12 no-padding">
          <div className="all_summary_of_order">
            <div className="order_details_summary">
              <div className="row">
                <div className="col-xs-4">
                  <p>Order No</p>
                  <h4>{order.order_no}</h4>
                </div>
                <div className="col-xs-4">
                  <p>Order Date</p>
                  <h4>{order.ordered_date}</h4>
                </div>
                
              </div>
              <div className="row">
                <div className="col-xs-4">
                  <p>Total Debit</p>
                  <h4>{$('<div/>').html(order.total_debits).text()}</h4>
                </div>
                <div className="col-xs-4">
                  <p>Total Credit</p>
                  <h4>{$('<div/>').html(order.total_credits).text()}</h4>
                </div>
                <div className="col-xs-4">
                  <p>Order Balance</p>
                  <h4>{$('<div/>').html(order.balance).text()} {order.txn_type}</h4>
                </div>
              </div>
              <div className="row">
                <div className="col-xs-12">
                  <div className="order_detail_btn">
                     <Link className="col-xs-6 order_breakup_button" to={Api.folder_path+"account/breakup/"+order.order_id+"/"+order.order_no+self.props.filter_url}>Breakup</Link>
                    <a target="_blank" rel="noopener noreferrer" className="col-xs-6" href={Api.folder_path+"account/order_history#!&filter_order_no="+order.order_no}>Details</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>)
         })   
         :
         <div className="col-xs-12 no-padding">
          <div className="all_summary_of_order">
            <div className="order_details_summary">
            <NoOrders />
           </div></div></div>
         }   
        </section>
		)
	}
}

export default (List);