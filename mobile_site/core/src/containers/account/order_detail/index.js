import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { bindActionCreators } from 'redux'
import  './index.css'
import { getOrderInfoAndTotalAmountBreakup, getOrderProductDetailsBySuborderId, getShippingPreferencesForOrderDetail, ShippingPreferencesPopupOpen, reorder, rquestNewPaymentLink } from '../../../actions/AccountAction'
import Api from '../../../api/Api'
import $ from 'jquery'
import ShippingPreferences from './shipping_preferences'
import custom from '../../../custom/custom'

class OrderDetail  extends Component {
  
  constructor(props)
  {
      super(props);
      this.shipping_preferences_open = this.shipping_preferences_open.bind(this);
      this.reorder                   = this.reorder.bind(this);
      this.payment_link              = this.payment_link.bind(this);
      this.downloadAll               = this.downloadAll.bind(this);
  }

  componentWillMount()
  {  
        var order_id = this.props.order_id;
        var suborder_id = this.props.suborder_id; 
        this.props.dispatch(getOrderInfoAndTotalAmountBreakup(order_id, suborder_id));
        this.props.dispatch(getOrderProductDetailsBySuborderId(order_id, suborder_id));
        this.props.dispatch(getShippingPreferencesForOrderDetail(order_id, suborder_id));
  }

  downloadAll(product_detail)
  {
     custom.downloadImages(product_detail.product_id, product_detail.model);
  }

   reorder(order_product_id)
  {
     $('body').removeClass('loaded').addClass('loading');
    var order_id = this.props.order_id;
    var suborder_id = this.props.suborder_id; 
    this.props.dispatch(reorder(order_id, suborder_id, order_product_id));
  }

  payment_link()
  {
     $('body').removeClass('loaded').addClass('loading');
    var  formData = new FormData();
    formData.append("order_id", this.props.order_id);
    formData.append("suborder_id", this.props.suborder_id);
    formData.append("balance", this.props.order_detail_info.balance);
    formData.append("order_no", this.props.order_detail_info.order_no);
    formData.append("total", this.props.order_detail_info.total);
    
    this.props.dispatch(rquestNewPaymentLink(formData));
  }

  shipping_preferences_open()
  {
    this.props.dispatch(ShippingPreferencesPopupOpen(1)); 
    var self = this;
   setTimeout(function()
    {
      if(parseInt(self.props.shipping_preferences.no_wsb_tape, 10))
       {
         $("#no_wsb_tape").attr('checked',true);
       }

       if(parseInt(self.props.shipping_preferences.no_invoice_with_shipment, 10))
      {
        $("#no_invoice_with_shipment").attr('checked',true);
      }
      
      if(self.props.shipping_preferences.courier_preference)
      {
        $("input[name=courier_preference][value="+self.props.shipping_preferences.courier_preference+"]").attr('checked',true);
      } 

     }, 200);
  }  

 render(){

let self = this;

function change_tab(on_tab, off_tab)
{
   $("#"+on_tab).addClass("in").addClass("active");
   $("#"+off_tab).removeClass("in").removeClass("active");

    $("#"+on_tab+"_tab").addClass("active");
    $("#"+off_tab+"_tab").removeClass("active");
}

if(this.props.order_detail_info )
{
    return (
      <div>
      <div className="contner head_margin side-collapse-container">
      <section className="cart_box">
          <h2 className="about_title">
          <Link to={Api.folder_path+'account/order_history'}>
          <i className="fa fa-long-arrow-left" aria-hidden="true"></i></Link>  &nbsp; 
          Order Detail</h2>

        <div className="order_detail_tab_btn">
          <ul>
            <li id="order_details_content_tab" className="active" onClick={()=>change_tab('order_details_content', 'status_history_content')}>Details</li>
            <li id="status_history_content_tab" onClick={()=>change_tab('status_history_content', 'order_details_content')}>Status History</li>
          </ul>
        </div>
        <div className="tab-content" id="accordion">


          <div id="order_details_content" className="tab-pane fade in active">
              <div className="order_history_card_view">
                <div className="cart_table_title_seller" onClick={()=>$('#order_details').slideToggle()}>
                    <h3>Order Details</h3>
                    <i className="fa fa-angle-down arr_down pull-right pd_arrow_section" aria-hidden="true"></i> 
                </div>
                 <div id="order_details" className="collapse in">
                    <div className="filtar_section_content">
                      <ul>
                       <li><span>Suborder ID :</span> {this.props.order_detail_info.suborder_id}</li>
                        <li><span>Order Date :</span> {this.props.order_detail_info.order_date}</li>
                        <li><span>Amount :</span> {$('<div/>').html(this.props.order_detail_info.total_amt).text()}</li>
                      
                        <li><span>Shipping Preferences:</span> 
                        {this.props.shipping_preferences.show_edit_button ?
                          <button className="btn btn-primary" type="button" style={{marginLeft: '10px', padding:'1px 6px'}} onClick={this.shipping_preferences_open}>Edit</button>
                          : ''}</li>
                         
                        {parseInt(this.props.shipping_preferences.no_wsb_tape, 10) ?
                         <li> <i className="fa fa-check-circle" aria-hidden="true" style={{color:'green'}}></i>
                          <label style={{fontSize:'14px', fontWeight:'200', marginLeft:'8px', color:'green'}}>Do not use wholesalebox packing tape.</label></li>
                         :
                        <li> <i className="fa fa-circle-o" aria-hidden="true"></i>
                          <label style={{fontSize:'14px', fontWeight:'200', marginLeft:'8px'}}>Do not use wholesalebox packing tape.</label></li>
                        }

                        {parseInt(this.props.shipping_preferences.no_invoice_with_shipment, 10) ?
                         <li> <i className="fa fa-check-circle" aria-hidden="true" style={{color:'green'}}></i>
                          <label style={{fontSize:'14px', fontWeight:'200', marginLeft:'8px', color:'green'}}>Do not send invoice with shipment.</label></li>
                         :
                        <li> <i className="fa fa-circle-o" aria-hidden="true"></i>
                          <label style={{fontSize:'14px', fontWeight:'200', marginLeft:'8px'}}>Do not send invoice with shipment.</label></li>
                        }  
                        
                        {this.props.shipping_preferences.courier_preference ?
                       <li><strong>Courier Partner Preference :</strong> 
                         <label id="courier_partner_preference_label" style={{color:'green'}}>{this.props.shipping_preferences.courier_preference}</label></li>
                         :''}
                        
                         <li>
                       {this.props.order_detail_info.payment_info.show_payment_link ?
                              <a href={this.props.order_detail_info.payment_info.payment_link} target="_blank" className="order_payment_btn" rel="noopener noreferrer"> Pay here </a>
                           :''}
                           &nbsp; &nbsp;
                       {this.props.order_detail_info.payment_info.show_request_new_payment_link ?
                              <button onClick={self.payment_link} className="order_payment_btn request_payment_btn"> Request Payment Link </button>
                           :''}    
                        </li>  
                      </ul>
                    </div>
                 </div>
              </div>

              <div className="order_history_card_view">
                <div className="cart_table_title_seller"  onClick={()=>$('#bank_details').slideToggle()}>
                    <h3>Payment Detail</h3>
                    <i className="fa fa-angle-down arr_down pull-right pd_arrow_section" aria-hidden="true"></i> 
                </div>
                 <div id="bank_details" className="collapse">
                    <div className="filtar_section_content">
                       <ul>
                         <li><span>Payment Mode :</span> {this.props.order_detail_info.payment_mode}</li>
                         {this.props.order_detail_info.payment_info.upi_details ?
                             $.map(this.props.order_detail_info.payment_info.upi_details, function(upi_details, index) {
                               return(<li><span>{index} :</span> {upi_details}</li>)
                             })
                          : ''}
                      </ul>
                    </div>
                     {this.props.order_detail_info.payment_info.bank_details ?
                      <div className="filtar_section_content">
                       <b>Bank Details:</b>
                        <ul>
                         {this.props.order_detail_info.payment_info.bank_details ?
                             $.map(this.props.order_detail_info.payment_info.bank_details, function(bank_details, index) {
                               return(<li><span>{index} :</span> {$('<div/>').html(bank_details).text()}</li>)
                             })
                          : ''}
                      </ul>
                       </div>
                      :''}
                 </div>
              </div>

              <div className="order_history_card_view">
                <div className="cart_table_title_seller" onClick={()=>$('#shipping_details').slideToggle()} >
                    <h3>Shipping Address</h3>
                    <i className="fa fa-angle-down arr_down pull-right pd_arrow_section" aria-hidden="true"></i> 
                </div>
                 <div id="shipping_details" className="collapse">
                    <div className="filtar_section_content">
                     <ul>
                        <li><span>Name :</span>
                         {this.props.order_detail_info.shipping.firstname+' '+this.props.order_detail_info.shipping.lastname}</li>

                           <li><span>Address :</span>

                              {this.props.order_detail_info.shipping.address_1}

                              {this.props.order_detail_info.shipping.address_2?
                                ', '+this.props.order_detail_info.shipping.address_2:''}

                              {this.props.order_detail_info.shipping.city?
                                ', '+this.props.order_detail_info.shipping.city:''}

                              {this.props.order_detail_info.shipping.postcode?
                                ', '+this.props.order_detail_info.shipping.postcode:''}

                              {this.props.order_detail_info.shipping.zone?
                                ', '+this.props.order_detail_info.shipping.zone:''} 

                            </li>
                    </ul>
                    </div>
                 </div>
              </div>

              <div className="order_history_card_view">
                <div className="cart_table_title_seller"  onClick={()=>$('#payment_details').slideToggle()}>
                    <h3>Billing Address</h3>
                    <i className="fa fa-angle-down arr_down pull-right pd_arrow_section" aria-hidden="true"></i> 
                </div>
                 <div id="payment_details" className="collapse">
                    <div className="filtar_section_content">
                      <ul>
                        <li><span>Name :</span>
                         {this.props.order_detail_info.payment.firstname+' '+this.props.order_detail_info.payment.lastname}</li>

                           <li><span>Address :</span>

                              {this.props.order_detail_info.payment.address_1}

                              {this.props.order_detail_info.payment.address_2?
                                ', '+this.props.order_detail_info.payment.address_2:''}

                              {this.props.order_detail_info.payment.city?
                                ', '+this.props.order_detail_info.payment.city:''}

                              {this.props.order_detail_info.payment.postcode?
                                ', '+this.props.order_detail_info.payment.postcode:''}

                              {this.props.order_detail_info.payment.zone?
                                ', '+this.props.order_detail_info.payment.zone:''} 

                            </li>
                      </ul>
                    </div>
                 </div>
              </div>


              <div className="order_history_card_view">
                  <div className="cart_table_title_seller">
                      <h3>Product Details</h3>
                  </div>
              </div>
              {this.props.order_product_detail?
                 $.map(this.props.order_product_detail, function(product_detail, index) {
                    return(
                        <div key={index} className="order_history_card_view">
                            <div className="filtar_section_content">
                                 <div className="detail_fl"><img src={product_detail.image} alt ={product_detail.name} /></div>
                                 <div className="detail_rt">
                                  <ul>
                                    <li><span className="detail_width_100"><Link to={Api.folder_path+product_detail.href}>{product_detail.name}</Link></span></li>
                                    <li>{product_detail.model}</li>
                                    <li>{product_detail.comment}</li>
                                    <li><span className="detail_width_100">{$('<div/>').html(product_detail.formatted_price_per_piece).text()}</span></li>
                                  </ul>
                                 </div>
                                 <div className="clearfix"></div>
                                 <button onClick={()=>self.downloadAll(product_detail)} className="add_to_cart_full_btn" style={{display:'inline', width:'40%'}}><i className="fa fa-download" aria-hidden="true"></i> Download</button>
                                 <button onClick={()=>self.reorder(product_detail.order_product_id)} className="add_to_cart_full_btn"  style={{display:'inline', width:'40%', float:'right'}}><i className="fa fa-shopping-bag" aria-hidden="true"></i> Reorder</button>
                            </div>
                        </div>
                      )
                })
                :'' }
              
               <div id="order_details_content" className="tab-pane fade in active">
                <div className="order_history_card_view">
                  <div className="cart_table_title_seller" onClick={()=>$('#amount_total').slideToggle()}>
                    <h3>Order Total</h3>
                    <i className="fa fa-angle-down arr_down pull-right pd_arrow_section" aria-hidden="true"></i> 
                  </div>

                  <div id="amount_total" className="collapse in">
                    <div className="filtar_section_content">
                      <ul>
                      {this.props.total_breakup ?
                        $.map(this.props.total_breakup, function(total, index) {
                       return(<li><span style={{width:'60%'}}>{total.title} :</span> {$('<div/>').html(total.value).text()}</li>)
                       })
                     : ''}
                      </ul>
                    </div>
                 </div>
              </div>   
              </div>



              
              </div>

         
          <div id="status_history_content" className="tab-pane fade">    
         { 
          this.props.order_detail_info.order_history?

           <table className="status_history_table" border="1px">
           
              <thead>
              <tr>
               <td>Date</td>
               <td>Status</td>
              </tr> 
              </thead>
             <tbody> 
            {
              $.map(this.props.order_detail_info.order_history, function(details, index) {
                return(
                    <tr key={index+"_trID"} >
                        <td>{details.status_date}</td>
                        <td>{details.label}</td>
                    </tr>
               ) 
             })
              }
                
              </tbody>
              
            </table>
            :''
          }
          </div>
        </div>
     </section>
     <div className="clearfix"></div>
       <ShippingPreferences order_id={this.props.order_id} suborder_id={this.props.suborder_id} shipping_preferences={this.props.shipping_preferences} />
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
    order_detail_info: state.accountReducer.order_detail_info.order_info,
    total_breakup: state.accountReducer.order_detail_info.total_breakup,
    order_product_detail:state.accountReducer.order_product_detail,
    shipping_preferences:state.accountReducer.shipping_preferences,
    actions: bindActionCreators(getOrderInfoAndTotalAmountBreakup, getOrderProductDetailsBySuborderId, ShippingPreferencesPopupOpen, reorder, rquestNewPaymentLink)
  };
}
export default connect(mapStateToProps)(OrderDetail);