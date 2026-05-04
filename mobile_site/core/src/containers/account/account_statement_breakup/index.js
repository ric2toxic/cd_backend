import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import List    from './list';
import NoOrders from '../../loading/no_orders';
import { Link } from 'react-router-dom'
import Api from '../../../api/Api'
import Summary    from './summary';
import  './index.css'
import { getOrderLevelBreakupAccountDetails } from '../../../actions/AccountAction'
import $ from 'jquery'

class AccountStatementBreakup  extends Component {
   
   constructor(props)
  {
     super(props);
      this.state = {
        filter_url:''
      };

   this.getUrlParameter          = this.getUrlParameter.bind(this); 
  } 

  componentWillMount()
  {  
        let filter_search                         = {};
        var hash                                  = window.location.hash;
        filter_search['filter_order_no']          = this.getUrlParameter('filter_order_no',hash);
        filter_search['filter_invoice_no']        = this.getUrlParameter('filter_invoice_no',hash);
        filter_search['filter_payment_method']    = this.getUrlParameter('filter_payment_method',hash);
        filter_search['filter_quarterly']         = this.getUrlParameter('filter_quarterly',hash);
        filter_search['filter_financial_year']    = this.getUrlParameter('filter_financial_year',hash);
        filter_search['filter_ordered_date_from'] = this.getUrlParameter('filter_ordered_date_from',hash);
        filter_search['filter_ordered_date_to']   = this.getUrlParameter('filter_ordered_date_to',hash);
        filter_search['filter_invoice_date_from'] = this.getUrlParameter('filter_invoice_date_from',hash);
        filter_search['filter_invoice_date_to']   = this.getUrlParameter('filter_invoice_date_to',hash);
        filter_search['filter_order_id']          = this.getUrlParameter('filter_order_id',hash);
        filter_search['breakup_order_no']         = this.getUrlParameter('breakup_order_no',hash);
       
        var filter_url = '';
        $.map(filter_search, function(filter, index) {
        if(filter)
        {
          filter_url   = filter_url+"&"+index+"=" + filter;
        }
        }); 

        if(filter_url !== '')
        {
          filter_url = '#!'+filter_url;
        }
        this.setState({filter_url}); 
        var order_id = this.props.order_id;
        this.props.dispatch(getOrderLevelBreakupAccountDetails(order_id));
  }


  getUrlParameter(sParam,url) {
        var sPageURL = decodeURIComponent(url.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }

 render()
 {
  if(this.props.order_details)
  {
    return (
      <div>
       <div className="contner head_margin side-collapse-container">
      
        <section className="cart_box account_statement">
         <h2 className="about_title">
          <Link to={Api.folder_path+'account/statement'+this.state.filter_url}>
          <i className="fa fa-arrow-left" aria-hidden="true"></i></Link>  &nbsp; 
          Order No : {this.props.order_no}</h2>

          <Summary is_positive_bal={this.props.breakup_is_positive_bal} total_debits={this.props.breakup_debit} total_credits={this.props.breakup_credit} balance={this.props.breakup_balance} />

          {this.props.order_details && this.props.order_details.length > 0 ?
           <List orders={this.props.order_details}  />  
           :
            <NoOrders />
           }         
         </section>
        

   </div>
       
   </div>)
}
else
{
  return(<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)
}

}}


function mapStateToProps(state){
  return {
    order_details: state.accountReducer.account_statement_breakup,
    breakup_debit: state.accountReducer.breakup_debit,
    breakup_credit: state.accountReducer.breakup_credit,
    breakup_balance: state.accountReducer.breakup_balance,
    breakup_is_positive_bal:state.accountReducer.breakup_is_positive_bal,
    actions: bindActionCreators(getOrderLevelBreakupAccountDetails)
  };
}
export default connect(mapStateToProps)(AccountStatementBreakup);