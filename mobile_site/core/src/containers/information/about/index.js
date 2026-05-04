import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Api from '../../../api/Api'
import Helmet from 'react-helmet';
import  './index.css'

import { aboutusData } from '../../../actions/InformationAction';

class About  extends Component {

  componentDidMount()
  {   
     this.props.dispatch(aboutusData());
  } 

 render(){
    return (
         <div className="contner head_margin side-collapse-container">
         <Helmet title={this.props.about_us.title} />
         <h2 className="about_title">About Us</h2>

         <section className="col-xs-12" style={{marginTop:'10px'}}>
            <div className="col-xs-12"><img src={Api.cdn_url+"wholesalebox_about.jpg"} alt="wholesalebox about" className="img-responsive about_img" /></div>
            <div className="col-xs-12">
              <div className="row">
               
                <h2 className="about_title">WholesaleBox is a marketplace for wholesale buying and selling across India.</h2>
            
               <p className="about_content_text">Wholesalebox has factories producing excellent selection of products as sellers who list their ready inventory stock for sale to shopkeepers across the country. We do this to eliminate the wholesalers and traders in between to get a 20-25% lower price for the shopkeepers by sourcing directly form manufacturers along with our tech enabled curation to provide fast selling designs. We are a bunch of technology and business people from IIMs, IITs, NITs, etc, who are working to bring efficiency in the whole distribution system and help retailers get more variety at their doorsteps. They can take advantage of our simple app or web interface to buy products for their retail outlets and get those without having to travel to different cities / manufacturing hubs or to buy at much higher prices from the wholesalers near them. We have put the entire wholesale buying process online to enable manufacturers & brands and retailers to drive incremental revenue, cut costs, improve their customer experience and analyze performance through data analytics.</p>

               <p className="about_content_text">For the shopkeepers, they only need to deal with one entity Wholesalebox to take care of entire order and returns experience while for the factories also it’s a similar seamless experience where they only need to bill and coordinate with only Wholesalebox and we take care of the rest while everyone does their business with ease.</p>

              </div>  
             </div>
          </section>

           <div className="clearfix"></div>

          <section className="col-sm-12 contact_sanction">
           <h2 className="about_title">Lowest Factory Price Assured</h2>
             <div className="col-xs-12"><img src={Api.cdn_url+"wholesalebox_about_graph.jpg"} alt="wholesalebox about" className="img-responsive about_img" /></div>
             <div className="col-xs-12">
              <div className="row">
               <h4>We bring you the best prices as we operate at very low margins.</h4>
               <p className="about_content_text">If you compare us with a normal Wholesaler nearby you. Buying from us, you can save 25%-35% of the procurement cost on every order. We have more than {this.props.about_us.total_products}+ designs on our website for retailers with convenience of e-commerce like COD, Home Delivery, Easy Returns, Value for Money with respect to Quality, Personal Buying Assistance, Volume Discounts, Credit Facilities, and the most important one, Guaranteed Lowest Prices!!! Shop at WholesaleBox, so that you can offer great pricing to your customers. In overall, buying at WholesaleBox will increase your profit margins at the convenience of your fingertips.  </p>
              </div>  
             </div>
          </section>

           <div className="clearfix"></div>

          <section className="col-sm-12 contact_sanction">
           <h2 className="about_title">How We Work</h2>
             <div className="col-xs-12"><img src={Api.cdn_url+"wholesalebox_about_how_we_work.jpg"} alt="wholesalebox about" className="img-responsive about_img" /></div>
             <div className="col-xs-12">
              <div className="row">
               <h4>WholesaleBox is an online platform where retailers/shopkeepers can buy hasslefree.</h4>
               <p className="about_content_text">And they reduce their procurements costs at better quality, from the convenience of his/her home/shop. From WholesaleBox, a retailer can order products in bulk for reselling and the order will be delivered at his/her chosen place. The ordering process is pretty simple. Sign Up (if first time), or Login the website using your mobile number or email address. Select designs you wish to place the order for, and add them to your cart. Use filters, sorts, and search to get the desired products of your choice from more than {this.props.about_us.total_products}+ to choose from. Once selection is complete, go to cart, click checkout and follow the steps to place the order with WholesaleBox and get Door Delivery done. Easy returns policy is provided post delivery of the Goods. Please review the Returns and Cancellation Policy.</p>
              </div>  
             </div>
          </section>

           <div className="clearfix"></div>

          <section className="col-sm-12 contact_sanction">
           <h2 className="about_title">Co-Founders</h2>
               <h4 className="text_center">We do everything with our core values of honesty, hard work and trust.</h4>
               <div className="about_team">
                <ul>
                  <li>
                    <img src={Api.cdn_url+"wholesalebox_rohit_dangayach.jpg"} alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Rohit Dangayach</span>
                  </li>
                  <li>
                    <img src={Api.cdn_url+"wholesalebox_chandan_agarwal.jpg"} alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Chandan Agarwal</span>
                  </li>
                  <li>
                    <img src={Api.cdn_url+"wholesalebox_rakesh_shekhawat.jpg"} alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Rakesh Shekhawat</span>
                  </li>
                  <li>
                    <img src={Api.cdn_url+"wholesalebox_madhur_maheshwari.jpg"} alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Madhur Maheshwari</span>
                  </li>
                  
                </ul>
                <div className="clearfix"></div> 
             </div>
          </section>
         <div className="clearfix"></div>
       
   </div>
)}}


function mapStateToProps(state){
  return {
    about_us: state.informationReducer.about_us,
    actions: bindActionCreators(aboutusData)
  };
}
export default connect(mapStateToProps)(About);