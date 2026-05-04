import React, { Component } from 'react';
import axios from 'axios';

class Aboutus extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 


	render() {
		return (
      <div className="container-fluid width_fix">
			    <div className="row">
      <div className="col-sm-12">
        <div className="row">
         <div className="col-sm-12"><img src="image/wholesalebox_about.jpg" alt="wholesalebox about" className="img-responsive about_img" /></div>
         <section className="col-sm-12 contact_sanction">
           <h2 className="page_title about_title"><span>About Us</span></h2>
             <div className="col-sm-8">
              <div className="row">
               <h3>WholesaleBox is a marketplace for wholesale buying and selling across India.</h3>
               <p className="about_content_text">We are connecting manufacturers or big wholesalers directly to retailers. We are a bunch of technology and business people from IIMs, IITs, NITs, etc, who are working to bring efficiency in the whole distribution system and help retailers get more variety at their doorsteps. They can take advantage of our simple web interface to buy products for their retail outlets and get those without having to travel to different cities / manufacturing hubs or to buy at much higher prices from the wholesalers near them. We've put the entire wholesale buying process online to enable manufacturers & brands and retailers to drive incremental revenue, cut costs, improve their customer experience and analyze performance through data analytics.</p>
              </div>  
             </div>
             <div className="col-sm-4 pull-right"><img src="image/wholesalebox_about_2.jpg" alt="wholesalebox about" className="img-responsive about_img" /></div>
          </section>
          <section className="col-sm-12 contact_sanction">
           <h2 className="page_title about_title"><span>Lowest Factory Price Assured</span></h2>
             <div className="col-sm-4"><img src="image/wholesalebox_about_graph.jpg" alt="wholesalebox about" className="img-responsive about_img" /></div>
             <div className="col-sm-8">
              <div className="row">
               <h3>We bring you the best prices as we operate at very low margins.</h3>
               <p className="about_content_text">If you compare us with a normal Wholesaler nearby you. Buying from us, you can save 25%-35% of the procurement cost on every order. We have more than 15000+ designs on our website for retailers with convenience of e-commerce like COD, Home Delivery, Easy Returns, Value for Money with respect to Quality, Personal Buying Assistance, Volume Discounts, Credit Facilities, and the most important one, Guaranteed Lowest Prices!!! Shop at WholesaleBox, so that you can offer great pricing to your customers. In overall, buying at WholesaleBox will increase your profit margins at the convenience of your fingertips. </p>
              </div>  
             </div>
          </section>
          <section className="col-sm-12 contact_sanction">
           <h2 className="page_title about_title"><span>How We Work</span></h2>
             <div className="col-sm-8">
              <div className="row">
               <h3>WholesaleBox is an online platform where retailers/shopkeepers can buy hasslefree.</h3>
               <p className="about_content_text">And they reduce their procurements costs at better quality, from the convenience of his/her home/shop. From WholesaleBox, a retailer can order products in bulk for reselling and the order will be delivered at his/her chosen place. The ordering process is pretty simple. Sign Up (if first time), or Login the website using your mobile number or email address. Select designs you wish to place the order for, and add them to your cart. Use filters, sorts, and search to get the desired products of your choice from more than 20000 to choose from. Once selection is complete, go to cart, click checkout and follow the steps to place the order with WholesaleBox and get Door Delivery done. Easy returns policy is provided post delivery of the Goods. Please review the Returns and Cancellation Policy.</p>
              </div>  
             </div>
             <div className="col-sm-4 pull-right"><img src="image/wholesalebox_about_how_we_work.jpg" alt="wholesalebox about" className="img-responsive about_img" /></div>
          </section>
          <section className="col-sm-12 contact_sanction">
           <h2 className="page_title about_title"><span>Co-Founders</span></h2>
               <h3 className="text_center">We do everything with our core values of honesty, hard work and trust.</h3>
               <div className="about_team">
                <ul>
                  <li>
                    <img src="image/wholesalebox_rohit_dangayach.jpg" alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Rohit Dangayach</span>
                  </li>
                  <li>
                    <img src="image/wholesalebox_chandan_agarwal.jpg" alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Chandan Agarwal</span>
                  </li>
                  <li>
                    <img src="image/wholesalebox_rakesh_shekhawat.jpg" alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Rakesh Shekhawat</span>
                  </li>
                  <li>
                    <img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive about_team_photo" />
                    <span>Madhur Maheshwari</span>
                  </li>
                  
                </ul>
                <div className="clearfix"></div> 
             </div>
          </section>
          <section className="col-sm-12 contact_sanction">
           <h2 className="page_title about_title"><span>Our Team</span></h2>
               <div className="our_team">
                <ul>
                  <li><img src="image/wholesalebox_rohit_dangayach.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_chandan_agarwal.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rakesh_shekhawat.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rohit_dangayach.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_chandan_agarwal.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rakesh_shekhawat.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rohit_dangayach.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_chandan_agarwal.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rakesh_shekhawat.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rohit_dangayach.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_chandan_agarwal.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_rakesh_shekhawat.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                  <li><img src="image/wholesalebox_madhur_maheshwari.jpg" alt="wholesalebox about" className="img-responsive" /></li>
                </ul>
                <div className="clearfix"></div> 
             </div>
          </section>    
         <div className="clearfix"></div>
        </div>   
      </div>
  </div></div>
		);
	}
}

export default Aboutus;
