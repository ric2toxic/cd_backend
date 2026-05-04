import React, { Component } from 'react'
import $ from 'jquery'

class List  extends Component {	

	render(){
		return (
	    	<section>	 
        <div className="col-xs-12 no-padding">
          <div className="all_summary_of_order" style={{padding:'0px'}}>
            <div className="order_details_summary">
              
              <div className="row">
                <div className="col-xs-8 breakup_left_section">
                  <h4>Particulars</h4>
                </div>
                <div className="col-xs-4 breakup_right_section">
                  <h4>Amount</h4>
                </div>
              </div>

             { $.map(this.props.orders, function(order, index) {
              return (<div className="row breakup_section">
                <div className="col-xs-8 breakup_left_section">
                    <div className="row" style={{padding:'4px 0px'}}>  
                      <div className="col-xs-4 breakup_title">Ref.</div>
                      <div className="col-xs-8 nopadding breakup_title" style={{fontWeight:'bold'}}>{order.particular}</div>
                   </div>

                   
                    <div className="row" style={{padding:'4px 0px'}}>  
                      <div className="col-xs-4 breakup_title">Doc. No.</div>
                      <div className="col-xs-8 nopadding breakup_title">{order.doc_ref}</div>
                   </div>

                   <div className="row" style={{padding:'4px 0px'}}>  
                      <div className="col-xs-4 breakup_title">Date</div>
                      <div className="col-xs-8 nopadding breakup_title">{order.date}</div>
                   </div>
                    
                   {order.download_link ? 
                    <div className="row" style={{padding:'20px 0px 10px 0px'}}>  
                      <div className="col-xs-12">
                         <a href={order.download_link} className="download_btn"><i className="fa fa-download"></i> Download</a>
                      </div>
                    </div>
                    :''}

                </div>
                <div className="col-xs-4  breakup_right_section">
                  {order.debit_amount ?
                  <p>{$('<div/>').html(order.debit_amount).text()} Dr</p>
                  : ''}

                  {order.credit_amount ?
                  <p>{$('<div/>').html(order.credit_amount).text()} Cr</p>
                  :''}
                </div>
              </div>)
         })   
         } 
             
            </div>
          </div>
        </div>
      </section>
		)
	}
}

export default (List);