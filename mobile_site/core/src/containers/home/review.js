import React, { Component } from 'react'

class Review extends Component {

   
  render(){

 return(<div className="container-fluid Customer_review_outer">
          <div className="">
          <div className="container">
            <div className="customer_heading"><h3>What our Clients says?</h3></div>
            <div className="border_bottom"><span></span></div>
          <div className="Customer_review">
            <div className="">          
          <div id="myCarousel" className="carousel slide" data-ride="carousel">
            <div className="carousel-inner">
              <div className="item active">                
                  
                    <div className="col-sm-4">
                      <div className="profile_img"><img src="nirajharlalka.jpg" />
                      </div>
                      <div className="Profile_review">
                        <p><span>"</span> Nice Business app. Excellent pricing. Quality products at best price. excellent customer support from your sales representative Mr Musavvir Khan. overall experience is till Very good but minimum order value need to be reduce for regular buyers.<span>"</span></p>
                      </div>
                      <div className="shop_name">
                        <p>Niraj Harlalka</p>
                      </div>
                    </div>
                  
                  
                    <div className="col-sm-4 dotted_border">
                     
                      <div className="profile_img"><img src="haseebmohammad.jpg" />
                      </div>
                      <div className="Profile_review">
                        <p><span>"</span> Products quality is good and you can easily return if you don't like products. My name is Haseeb Mohammad thanks for all<span>"</span> </p>
                      </div>
                      <div className="shop_name">
                        <p>Haseeb Mohammad</p>
                      </div>
                    
                    </div>
                 
                  
                    <div className="col-sm-4">
                      <div className="profile_img"><img src="arjunkalawat.jpg" />
                      </div>
                      <div className="Profile_review">
                        <p><span>"</span> Decent Products , Price is Good , Good customer support , especially it's very useful to update the new Designs to your customers bcz new updates only stands your business for long terms so this is the best choice for every retailers, Nice app<span>"</span> </p>
                      </div>
                      <div className="shop_name">
                        <p>Arjun Kalawat</p>
                      </div>
                    </div>
          
              </div>                
              
              <div className="item">                
                  
                    <div className="col-sm-4">
                      <div className="profile_img"><img src="karthikn.jpg" />
                      </div>
                      <div className="Profile_review">
                        <p><span>"</span> It's a great platform for retailers, Retailers can easy to purchase their requirements through the WHOLESALE BOX APPLICATION especially it's very useful to update the new designs to your customers because new updates only stands your business for long terms so this is the best choice for every retailers.<span>"</span> </p>
                      </div>
                      <div className="shop_name">
                        <p>Karthik N</p>
                      </div>
                    </div>
                    
                    <div className="col-sm-4 dotted_border">
                      <div className="profile_img"><img src="no_image.jpg" />
                      </div>
                      <div className="Profile_review">
                        <p><span>"</span> Extremely good service...very efficient support team...cares for its customer...as for the price..very much reasonable for northeast india...very reliable ...honest...the items are exactly as described ..go ahead ..it has made life easy...<span>"</span> </p>
                      </div>
                      <div className="shop_name">
                        <p>Rupak Medhi</p>
                      </div>
                    </div>
                  
                    <div className="col-sm-4">
                      <div className="profile_img"><img src="mayankverma.jpg" />
                      </div>
                      <div className="Profile_review">
                        <p><span>"</span> Excellent Support by my aling executive Ms Divya Sharma she helping out for every new updates and sharing product on time excellent kudos Divya<span>"</span> </p>
                      </div>
                      <div className="shop_name">
                        <p>Mayank Verma</p>
                      </div>
                    </div>
                  
              </div>   


              <div className="item">                
                  
                    <div className="col-sm-4">
                      <div className="profile_img"><img src="kalyan.jpg" />
                      </div>
                      <div className="Profile_review">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/mL9U2Y5V-eM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                      </div>
                      <div className="video_shop_name">
                        <p>Madhu Readymade Designer Zone</p>
                      </div>
                    </div>
                  
                  
                    <div className="col-sm-4 dotted_border">
                      <div className="profile_img"><img src="Jain.jpg" />
                      </div>
                      <div className="Profile_review">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/urZHpooFuBk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                      </div>
                      <div className="video_shop_name">
                        <p>Jain Ladies Garments</p>
                      </div>
                    </div>
                  
                  
                    <div className="col-sm-4">
                      <div className="profile_img"><img src="nibedita.jpg" />
                      </div>
                      <div className="Profile_review">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/wows02JrdkU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                      </div>
                      <div className="video_shop_name">
                        <p>Now & Wow</p>
                      </div>
                    </div>
                  
              </div>   
              
              
              
            </div>
              <a className="left_arrow pull-left" href="#myCarousel" data-slide="prev">
                <span className="fa fa-arrow-left"></span>
              </a>
              <a className="right_arrow pull-right" href="#myCarousel" data-slide="next">
                <span className="fa fa-arrow-right"></span>
              </a>
          </div>          
        </div>
          </div>
        </div>
      </div>
    </div>)

}}


export default Review;
