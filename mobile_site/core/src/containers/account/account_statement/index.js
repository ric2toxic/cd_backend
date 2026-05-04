import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import  './index.css'
import { getOrderLevelAccountSummary, getOrderLevelAccountSummaryPagination, downloadReport } from '../../../actions/AccountAction'
import FilterPopup from './filter_popup';
import Summary    from './summary';
import List    from './list';
import $ from 'jquery'

class AccountStatement  extends Component {

 constructor(props)
  {
     super(props);
      this.state = {
        filterPopupOpen: false,
        filter_search:{},
        filter_url:''
      };
      
      this.filter_popup             = this.filter_popup.bind(this);
      this.filter_popup_close       = this.filter_popup_close.bind(this);
      this.paginationData           = this.paginationData.bind(this);
      this.getStatementList         = this.getStatementList.bind(this);
      this.addFilterInHistoryState  = this.addFilterInHistoryState.bind(this);
      this.getUrlParameter          = this.getUrlParameter.bind(this); 
      this.searchChangeValue        = this.searchChangeValue.bind(this);
      this.clearSearch              = this.clearSearch.bind(this);
      this.searchChangeDateValue    = this.searchChangeDateValue.bind(this);
      this.downloadReport           = this.downloadReport.bind(this);  
 }

  componentDidMount()
  {  
     let filter_search                         = this.state.filter_search;
     var hash                                  = window.location.hash;

        var filter_order_no          = this.getUrlParameter('filter_order_no',hash);
        var filter_invoice_no        = this.getUrlParameter('filter_invoice_no',hash);
        var filter_payment_method    = this.getUrlParameter('filter_payment_method',hash);
        var filter_ordered_date_from = this.getUrlParameter('filter_ordered_date_from',hash);
        var filter_ordered_date_to   = this.getUrlParameter('filter_ordered_date_to',hash);
        var filter_invoice_date_from = this.getUrlParameter('filter_invoice_date_from',hash);
        var filter_invoice_date_to   = this.getUrlParameter('filter_invoice_date_to',hash);
        var filter_order_id          = this.getUrlParameter('filter_order_id',hash);
        var breakup_order_no         = this.getUrlParameter('breakup_order_no',hash);
        var current_year             =  1;

        if(filter_order_no) { filter_search['filter_order_no'] = filter_order_no; current_year=0; }
        if(filter_invoice_no) { filter_search['filter_invoice_no'] = filter_invoice_no; current_year=0; }
        if(filter_payment_method) { filter_search['filter_payment_method'] = filter_payment_method; current_year=0; }
        if(filter_ordered_date_from) { filter_search['filter_ordered_date_from'] = filter_ordered_date_from; current_year=0; }
        if(filter_ordered_date_to) { filter_search['filter_ordered_date_to'] = filter_ordered_date_to; current_year=0; }
        if(filter_invoice_date_from) { filter_search['filter_invoice_date_from'] = filter_invoice_date_from; current_year=0; }
        if(filter_invoice_date_to) { filter_search['filter_invoice_date_to'] = filter_invoice_date_to; }
        if(filter_order_id) { filter_search['filter_order_id'] = filter_order_id; current_year=0; }
        if(breakup_order_no) { filter_search['breakup_order_no'] = breakup_order_no; current_year=0; }

    if(current_year)
     {
            var d    = new Date();
            var year = d.getFullYear();
            filter_search['filter_invoice_date_from'] = year+'-01-01';
            filter_search['filter_invoice_date_to']   = year+'-12-31';
     }

     this.setState({filter_search});
    
    if(!this.props.orders)
    {
      //this.props.dispatch(getCustomerLevelAccountSummary());
      this.getStatementList(); 
    }
     
    var self = this;
     $(window).scroll(function(){
    if($(".btn_browse_more").hasClass( "page_auto_load" ))
      { 
        let pos_browse_more = $('.page_auto_load').offset().top;
        let scrollTop = $(window).scrollTop();
        let pos_area = pos_browse_more-10000;
         if (scrollTop > pos_area){
           self.paginationData();
         }
      }   
      });
  }

  getStatementList()
  {
        this.setState({filterPopupOpen: 0}); 
        let filter_search = this.state.filter_search;
        this.props.dispatch(getOrderLevelAccountSummary(filter_search)); 
        this.addFilterInHistoryState(filter_search);
  }

  paginationData()
  { 
     let filter_search = this.state.filter_search;
     let beyond_order_id = this.props.beyond_order_id;
     let balance = this.props.balance;
     $(".btn_browse_more").removeClass("page_auto_load");
     this.props.dispatch(getOrderLevelAccountSummaryPagination(filter_search, beyond_order_id, balance));
  }

 downloadReport(encode)
  { 
    let filter_search = this.state.filter_search;
    this.props.dispatch(downloadReport(filter_search));
  }

  filter_popup()
  {
    this.setState({filterPopupOpen: 1});
  }

  filter_popup_close()
  {
    this.setState({filterPopupOpen: 0}); 
  }  

  addFilterInHistoryState(filter_search)
  {
      var final_url            = window.location;
      var filter_string       = '';
      if (window.location.hash) 
      {
        var url = window.location.href;
        var url_page = url.split("&");
        final_url = url_page[0].split("#!");

        if(final_url.length === 1 )
          {
            var url_page2 = url_page[1].split("#!");
            final_url = final_url+'&'+url_page2[0];
          }
        else
         {
            final_url = final_url[0];
          }
      }

       $.map(filter_search, function(filter, index) {
        if(filter)
        {
          filter_string   = filter_string+"&"+index+"=" + filter;
        }
      }); 

        if(filter_string)
        {
          var filter_url = '#!'+filter_string;
          this.setState({filter_url}); 
          window.history.pushState('', null, final_url+'#!'+filter_string);
        }
        else
        {
          window.history.pushState('', null, final_url);
        }
        
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

  searchChangeValue(field, event)
   {     
        let filter_search = this.state.filter_search;
        if(field === 'filter_payment_method')
        {
          var option_value=[];
          $.map(event, function(option, index) 
          {
            if(option.value)
            {
               option_value.push(option.key);
            }
          });
          filter_search[field] = option_value.join();   
        }
        else
        {
           filter_search[field] = event.target.value;
        } 
       
   
        var filter_financial_year = new Date().getFullYear();
        if(filter_search.filter_financial_year)
        {
          filter_financial_year = filter_search.filter_financial_year;
          filter_search['filter_invoice_date_from'] = filter_financial_year+'-04-01';
          filter_search['filter_invoice_date_to']   = (parseInt(filter_financial_year)+1)+'-03-31';
        }

        if(filter_search.filter_quarterly)
        {
          if(filter_search.filter_quarterly === 'Q1')
          {
             filter_search['filter_invoice_date_from'] = filter_financial_year+'-04-01';
             filter_search['filter_invoice_date_to']   = filter_financial_year+'-06-30';    
          }
          if(filter_search.filter_quarterly === 'Q2')
          {
             filter_search['filter_invoice_date_from'] = filter_financial_year+'-07-01';
             filter_search['filter_invoice_date_to']   = filter_financial_year+'-09-30';    
          } 
          if(filter_search.filter_quarterly === 'Q3')
          {
             filter_search['filter_invoice_date_from'] = filter_financial_year+'-10-01';
             filter_search['filter_invoice_date_to']   = filter_financial_year+'-12-31';    
          } 
          if(filter_search.filter_quarterly === 'Q4')
          {
             filter_search['filter_invoice_date_from'] = (parseInt(filter_financial_year)+1)+'-01-01';
             filter_search['filter_invoice_date_to']   = (parseInt(filter_financial_year)+1)+'-03-31';    
          }
        }
      
       this.setState({filter_search});
   }

   searchChangeDateValue(field, value)
   {
     let filter_search = this.state.filter_search;
     filter_search[field] = value;
     if(field === 'filter_invoice_date_from' || field === 'filter_invoice_date_to')
     {
      filter_search['filter_quarterly']      = '';
      filter_search['filter_financial_year'] = '';
     }
     this.setState({filter_search});
   }

  clearSearch()
  {
      this.setState({filter_search:{}});
  }


 render()
 {
  console.log(this.props);
  //var self = this;
  if(this.props.account_summary)
  {
    return (
      <div>
       <div className="contner head_margin">
        <section className="cart_box account_statement">

           <h2 className="about_title">Account Statement</h2>

              <div className="col-sm-12 no-padding">
                <div className="Total_summary_of_order filter_section">
                     <div className="col-xs-6" style={{borderRight:'1px solid #ccc'}}>
                      <p onClick={this.downloadReport}><i className="fa fa-download"></i> Download Report</p>
                     </div>
                    <div className="col-xs-6">
                     <p onClick={this.filter_popup}><i className="fa fa-filter" aria-hidden="true"></i> Filter and Search</p>
                    </div>
                </div>
              </div>

           
           {this.props.account_summary && this.props.account_summary.customer_id ? 
           <Summary account_summary={this.props.account_summary} important_note={this.props.important_note} />
           : ""}
           
           
            <FilterPopup filter_search={this.state.filter_search} 
                         filterPopupOpen={this.state.filterPopupOpen} 
                      filter_popup_close={this.filter_popup_close}
                      searchChangeValue={this.searchChangeValue}
                      getStatementList={this.getStatementList}
                      clearSearch={this.clearSearch}
                      searchChangeDateValue={this.searchChangeDateValue}
                      payment_methods = {this.props.payment_methods} />


            <List orders={this.props.orders} filter_url={this.state.filter_url}  />          

         </section>
           <div className="btn_browse_more page_auto_load"></div>
         <div className="clearfix"></div>
       
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
    orders:state.accountReducer.statement_orders,
    account_summary:state.accountReducer.account_summary,
    important_note:state.accountReducer.important_note,
    payment_methods:state.accountReducer.payment_methods,
    beyond_order_id:state.accountReducer.beyond_order_id,
    balance:state.accountReducer.balance,
    actions: bindActionCreators(getOrderLevelAccountSummary, getOrderLevelAccountSummaryPagination, downloadReport)
  };
}
export default connect(mapStateToProps)(AccountStatement);