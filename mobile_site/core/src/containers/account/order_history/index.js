import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import  './index.css'
import { orderDetailData, orderDetailPaginationData } from '../../../actions/AccountAction'
import SuborderDetail from './suboreder_detail';
import NoOrders from '../../loading/no_orders';
import ReturnPopup from './return_popup';
import $ from 'jquery'

class OrderHistory  extends Component {

 constructor(props)
  {
     super(props);
      this.state = {
        returnPopupOpen: false,
        filter_order_no:false,
      };
      
      this.return_popup         = this.return_popup.bind(this);
      this.return_popup_close   = this.return_popup_close.bind(this);
      this.paginationData       = this.paginationData.bind(this);
      this.getUrlParameter      = this.getUrlParameter.bind(this);
      this.searchChangeValue    = this.searchChangeValue.bind(this); 
      this.get_order_data       =  this.get_order_data.bind(this);
      this.addFilterInHistoryState  = this.addFilterInHistoryState.bind(this);
 }

  componentDidMount()
  {  
     var self = this;
     var hash                             = window.location.hash;
     var filter_order_no                  = this.getUrlParameter('filter_order_no',hash);
     this.setState({filter_order_no});

     this.props.dispatch(orderDetailData(4, 0, filter_order_no));
     $(window).scroll(function(){
    if($(".btn_browse_more").hasClass( "page_auto_load" ))
      { 
        let pos_browse_more = $('.page_auto_load').offset().top;
        let scrollTop = $(window).scrollTop();
        let pos_area = pos_browse_more-5000;
         if (scrollTop > pos_area){
           self.paginationData();
         }
      }   
    });

  }

  get_order_data()
  {
     this.props.dispatch(orderDetailData(4, 0, this.state.filter_order_no));
     this.addFilterInHistoryState(this.state.filter_order_no);
  }

  searchChangeValue(event)
  {
     var filter_order_no = event.target.value;
     this.setState({filter_order_no});
  }

  return_popup()
  {
    this.setState({returnPopupOpen: 1});
  }

  return_popup_close()
  {
    this.setState({returnPopupOpen: 0}); 
  } 

  paginationData()
  { 
    if(this.props.order_details.beyond_order_id)
    {
     $(".btn_browse_more").removeClass("page_auto_load");
     this.props.dispatch(orderDetailPaginationData(4, this.props.order_details.beyond_order_id, this.state.filter_order_no));
    }
  }

  getUrlParameter(sParam,url) 
  {
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

   addFilterInHistoryState(filter_order_no)
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

      
        if(filter_order_no)
        {
          filter_string = filter_string+"&filter_order_no=" + filter_order_no;
        }
  

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

 render(){
 var self = this;
if(this.props.order_details)
{
    return (
      <div>
       <div className="contner head_margin side-collapse-container">
        <section className="cart_box">

          <h2 className="about_title">My Orders</h2>
           
           <input type="text" className="form-control" name="filter_order_no" onChange={this.searchChangeValue} placeholder="Order Number OR Invoice Number" value={this.state.filter_order_no ? this.state.filter_order_no: ''} />
           <button className="order_search" onClick={this.get_order_data}><i className="fa fa-search" aria-hidden="true"></i></button>

          { this.props.orders && this.props.orders.length > 0?
            this.props.orders.map((data, index) => {
            return(
          <div key={index} className="order_history_box">
             <div className="col-xs-8 nopadding order_history_text"><b>Order No. </b> <b>{data.order_no}</b></div>
             <div className="col-xs-4 nopadding order_history_amount">{$('<div/>').html(data.order_total).text()}</div>
             <div className="col-xs-8 nopadding order_history_text"><strong>Order Date</strong>{data.order_date}</div>
             <div className="col-xs-8 nopadding order_history_text"><strong>Payment Mode</strong>{data.payment_mode}</div>
             <div className="clearfix"></div>
             <div className="order_more_detai" onClick={()=>$('#more_order_details_'+data.order_id).slideToggle()}>Click to more details</div>
             <div className="clearfix"></div>
               <div id={"more_order_details_"+data.order_id} className="collapse">
                 <SuborderDetail return_popup={self.return_popup}  order_complete={self.props.order_details} suborders={data.suborders} product_order_id={data.order_id}/>
              </div>
          </div> 
          );
        }):<NoOrders />
        }

         <ReturnPopup returnPopupOpen={this.state.returnPopupOpen} return_popup_close={this.return_popup_close} />
         </section>
           <div className="btn_browse_more page_auto_load"></div>
         <div className="clearfix"></div>
       
   </div>
       
   </div>
)
}
else
{
  return(<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)
}

}}


function mapStateToProps(state){
  return {
    order_details: state.accountReducer.order_details,
    orders:state.accountReducer.orders,
    actions: bindActionCreators(orderDetailData, orderDetailPaginationData)
  };
}
export default connect(mapStateToProps)(OrderHistory);