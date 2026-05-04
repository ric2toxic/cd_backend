import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import Api from '../../../api/Api'
import { OptPopupOpen } from '../../../actions/LoginAction';
import { update_Dropshipper } from '../../../actions/AccountAction';
import $ from 'jquery'
import  './index.css'


class Dropshipper  extends Component {

 constructor(props)
 {    
       super(props);
      this.login_popup = this.login_popup.bind(this);
      this.update_dropshipper = this.update_dropshipper.bind(this);
 } 

  login_popup()
 {
   this.props.dispatch(OptPopupOpen(1));
 }

 update_dropshipper()
 {
   $('body').removeClass('loaded').addClass('loading');
  this.props.dispatch(update_Dropshipper());
 }

 render(){
    return (
         <div className="contner head_margin side-collapse-container">
         <h2 className="about_title">Dropshipper</h2>

         <section className="col-xs-12 nopadding">
            <div className="col-xs-12">
                <p className="dropshipper_heading_list">Dropshipping is a practice of selling a product to your customers that you don’t have physically in stock. It’s a new trend of marketing and distribution of products.<br /><br />We can make this possible by shipping the stock directly to your customer with your name
              </p>
             </div>

             <div className="col-xs-12">
             <img src={Api.cdn_url+"dropshipper.png"} alt="dropshipper" className="img-responsive about_img" />
             </div>
          </section>
          
          <div className="clearfix"></div>

          <section className="col-sm-12 nopadding">
           <h2 className="about_title">Benefits of Dropshipping:</h2>
             <div className="col-xs-12">
            
             <ul  className="dropshipper_text_benefits">
              <li>Unlike traditional business, you do not have to source products in bulk before you sell anything. Therefore, you aren't facing the risk of having dead stock.</li>

              <li>No need of inventory management or maintaining your own warehouse.</li>

              <li>Normally, when you buy from wholesalers, you have to buy in large quantities. But with Wholesalebox.in, you get low wholesale prices even on small quantities.</li>

              <li>Another major benefit is that you can start your business without any need for start-up capital, as our company will be managing the stock for you. At Wholesalebox.in, we have everything in stock that we list online. Only once you get an order, you pay us for shipping it out.</li>

              <li>You don't have to pick, pack, and ship orders as we will do that on your behalf. So you can completely focus on your sales and just communicate the sales to us and we will take it from there. And as said, we have everything in stock, this means, your orders are dispatched within a working day.</li>

              <li>And the best part, the whole transaction is completely anonymous. Customer wouldn’t know that it came from us!</li>
          </ul>
             </div>
          </section>

          <div className="clearfix"></div>

          <section className="col-sm-12 nopadding">
           <h2 className="about_title">How do I become a dropshipper with Wholesalebox.in ?</h2>
             <div className="col-xs-12">
              
              
          
              {this.props.userlogin.logged ?
                !this.props.userlogin.is_seller ?
                <ul className="dropshipper_text_benefits">
                <li>Your dropshipper account activated when you click on become a dropshipper.<br />

                <button onClick={this.update_dropshipper} className="btn deliver_btn">Become a Dropshipper</button>
                    </li>
                 </ul>
                 :''

                :

                <ul className="dropshipper_text_benefits">
                  <li>To Register please do the following</li>
                  <li>Register an account with www.wholesalebox.in
                   <br />

                  
                  <button onClick={this.login_popup} className="btn deliver_btn">Sign UP</button>

                  </li>
            
                    <li>You will receive an e-mail confirmation when your dropshipper account is activated.</li>
                 </ul>
              }  
              </div>  
          </section>


          <div className="clearfix"></div>

          <section className="col-sm-12 nopadding">
           <h2 className="about_title">More about dropshipping?</h2>
             <div className="col-xs-12">
              <ul  className="dropshipper_text_benefits">
              <li>If you want to know anything else, please drop us an e-mail at 
              <Link to={Api.folder_path+"i/contact-us"}> info@wholesalebox.in </Link>
              </li>
             </ul>
             </div>
          </section>

          <div className="clearfix"></div>

          <section className="col-sm-12 nopadding">
           <h2 className="about_title">Note:</h2>
             <div className="col-xs-12">
              <ul  className="dropshipper_text_benefits">
              <li>Promotional offers, such as 2% discount, Free Shipping, etc., are not valid on dropshipping orders.</li>
              <li> Dropshipping orders will be sent only by Air couriers. If the weight of the package exceeds 5 kg, Surface courier option is also available.</li>
              <li>No returns will be entertained on dropshipping orders. Only in the case of manufacturing defects or product description mismatch, returns request will be entertained. <strong>Returns will be on the sole discretion of Wholesalebox.</strong></li>
             </ul>
             </div>
          </section>

         <div className="clearfix"></div>
       
   </div>
)}}


function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    actions: bindActionCreators(OptPopupOpen,update_Dropshipper)
  };
}
export default connect(mapStateToProps)(Dropshipper);