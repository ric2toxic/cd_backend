import React, { Component } from 'react'
import Api from '../../api/Api'

class CartOffer extends Component {
  render() {
    return (
      <div className="col-xs-12 cart_offer" >

       <div className="wsb_offers">
           
          <div className="offer_heading">
            <h4>{this.props.language.text_wsb_offer}</h4>
          </div>

          <br/>
           {this.props.surface_shipping ?
           <p style={{borderBottom:'1px solid #fff', paddingBottom:'10px'}}><b>{this.props.language.text_free_surface_shipping_2}</b> <br /> <span style={{fontSize:'12px'}}>{this.props.language.text_free_surface_shipping_3} </span></p>
           :
           <p style={{borderBottom:'1px solid #fff', paddingBottom:'10px'}}><b>{this.props.language.text_free_surface_shipping_1}</b> <br /> <span style={{fontSize:'12px'}}>{this.props.language.text_free_surface_shipping_3} </span></p>
           }

          <div className="col-xs-3 nopadding">
             <div className="discount_image">
             </div>
           </div>

           <div className="col-xs-9 right-nopadding">
             <div className="discount_description">
             <div className="wsb_offer_subline">                           
               <p>{this.props.language.text_wsb_offer_line1}</p>
               <p>{this.props.language.text_wsb_offer_line2}</p>
             </div>
            </div>
            <p className="yellow_color wsb_offer_subline">{this.props.language.text_wsb_offer_line3}</p>
            </div>
        </div>

         

        <div className="app_cashback">
         <div className="col-xs-12 nopadding" >
           <b><p>{this.props.language.text_app_cashback_title}</p></b>
         </div>

         <div className="col-xs-12 nopadding" >
          <p>{this.props.language.text_app_cashback_body}</p>
         </div>

          <div className="col-xs-12 nopadding">
           <div className="col-xs-12 nopadding">
             <b><p>{this.props.language.text_app_download}</p></b>
           </div>
           <div className="col-xs-12 nopadding">
              <div className="col-xs-6 nopadding">
                <a href="https://play.google.com/store/apps/details?id=in.wholesalebox" target="_blank" rel="noopener noreferrer">
                  <img src={Api.cdn_url+"google-play-android-app.svg"} alt="Android app on google play" style={{width:'95%'}} />
                </a>
              </div>

              <div className="col-xs-6 nopadding">
                <a href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank" rel="noopener noreferrer">
                  <img src={Api.cdn_url+"ios_download.svg"} alt="ios app on app store" style={{width:'95%'}} className="pull-right" />
                </a>
              </div>
            </div>
          </div>
         </div>
      </div>
    );
  }
}

export default CartOffer;