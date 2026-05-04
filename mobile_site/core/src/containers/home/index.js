import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import $ from 'jquery'
import Footer from '../footer'
import Api from '../../api/Api'
import custom from '../../custom/custom'
import Banner from '../banner'
import Product from './product'
import HomeCategory from './home_category'
import HomeSubCategory from './home_sub_category'
import RecentlyProduct from './recently_product'
import { latestData, trendingData } from '../../actions/HomeAction';
import { OptPopupOpen, LoginPopupOpen} from '../../actions/LoginAction';
import { bannerData } from '../../actions/BannerAction';
import  './index.css'
import 'react-responsive-carousel/lib/styles/carousel.min.css'

class Home extends Component {

   constructor(props)
   {
      super(props);
      this.setPreferences         = this.setPreferences.bind(this);
      this.SubCategoryPopupOpen   = this.SubCategoryPopupOpen.bind(this);
      this.SubCategoryPopupClose  = this.SubCategoryPopupClose.bind(this);
      this.credit_application     = this.credit_application.bind(this);

     this.state = {
      menu_selected: custom.getCookie('preferences') ? custom.getCookie('preferences') : false,
      SubCategoryPopup:false,
      SubCategoryData:false,
      new_arrival_link:'v'
     }
   }

   componentDidMount()
   {
      if(custom.getCookie('preferences') === '')
      {
        if(this.props.menus && this.props.menus.menus && this.props.menus.menus.length > 0)
        {
         custom.createCookie('preferences', this.props.menus.menus[0].value, 7); 
         this.setState({menu_selected:this.props.menus.menus[0].value});
         this.setState({new_arrival_link:this.props.menus.menus[0].href});
         this.props.dispatch(bannerData());
         this.props.dispatch(latestData());
         this.props.dispatch(trendingData());
        }
        else
        {
         this.props.dispatch(bannerData());
         this.props.dispatch(latestData());
         this.props.dispatch(trendingData()); 
        }
      }
      else
      {
        let self = this;
        this.props.dispatch(bannerData());
        this.props.dispatch(latestData());
        this.props.dispatch(trendingData());

        if(this.props.menus && this.props.menus.menus && this.props.menus.menus.length > 0)
        {
          this.props.menus.menus.map((item, index) => {
           if(item.value === self.state.menu_selected)
           {
             self.setState({new_arrival_link:item.href});
             return true;
           }
           else
           {
            return false;
           }
         });
        }
      }

   }  


  credit_application()
  {
    this.props.dispatch(OptPopupOpen(1));
    setTimeout(function(){ $("#redirect_cart").val('credit'); }, 1000);
  }


  SubCategoryPopupOpen(e)
  {
    this.setState({
      SubCategoryPopup:true,
      SubCategoryData:e
    });
  }

  SubCategoryPopupClose()
  {
   this.setState({
      SubCategoryPopup:false
    }); 
  }

  setPreferences(value, href)
  {
    this.setState({menu_selected:value});
    this.setState({new_arrival_link:href});
    custom.createCookie('preferences', value, 7);

    this.props.dispatch(bannerData());
    this.props.dispatch(latestData());
    this.props.dispatch(trendingData());
  }

  generateCategory (item, index) { 
    if(item.images.length>0){
      var theString = item.name;
      var varTitle = $('<textarea />').html(theString).text();
      if(item.images && item.images.length>0){
       return <li key={index} className="nw_image_li"><Link to={Api.folder_path+item.href}><img src={item.images[0]['image']} alt={varTitle} className="img-responsive" /><div className="imageTitle"><h1>{varTitle}</h1></div></Link></li> 
      }else{
       return <li key={index}><Link to={Api.folder_path+item.href}><img src={item.image} alt={varTitle} className="img-responsive" /><span>{varTitle}</span></Link></li> 
      }
    }

  }
  
render(){
 
  let recentViewProduct="recentViewProduct";
  if(window.location.href.indexOf("/staging") > -1) 
    {
      recentViewProduct="staging_recentViewProduct";
    }
    
if(localStorage.getItem(recentViewProduct))
{
  var previousProduct = JSON.parse(localStorage.getItem(recentViewProduct));
  previousProduct = previousProduct.slice(0, 3);
}    

 if(this.props.menus)
 {
    return (
       <div className="contner head_margin" style={{marginTop:'92px'}}>
                <div className="navbar_header scroll-x">
                 <nav>
                  <ul style={{marginBottom:'-4px'}}>
                  {this.props.menus.menus && this.props.menus.menus.length ?
                   this.props.menus.menus.map((item, index) => {
                   return(<li key={index}><span id={"category_tab_"+item.value} onClick={()=>this.setPreferences(item.value, item.href)} className={this.state.menu_selected ? this.state.menu_selected === item.value ? 'active' : '' : index === 0 ? 'active' : ''}>{$('<div/>').html(item.link_title).text()}</span></li>)
                   })
                   : ''}
                   </ul>
                  </nav>
                 </div>
                 
                {this.props.menus.menus && this.props.menus.menus.length ?
                  this.props.menus.menus.map((item, index) => { 
                 return(<HomeCategory key={index} SubCategoryPopupOpen={this.SubCategoryPopupOpen} menus={item.children} menu_selected={this.state.menu_selected ? this.state.menu_selected !== item.value ? 'hide' : '' : index > 0 ? 'hide' : ''} />)
                 })
                 : ''} 
                    
                <div className="offeranddiscount scroll-x">      
                 <div className="offer_card col-xs-11 no-padding">
                    <ul>
                     <li className="offer_image col-xs-2"><img src={Api.cdn_url+"discount.png"} alt="" /></li>
                     <li className="offer_description col-xs-10">2% Discount
                     <br/><span>Get 2% discount on every prepaid order</span><br/> &nbsp;
                      </li>
                    </ul>
                 </div>

                  <div className="offer_card col-xs-11 no-padding">
                   <ul>
                      <li className="offer_image col-xs-2"><img src={Api.cdn_url+"offer.png"} alt="" /></li>
                      <li className="offer_description col-xs-10">Stock Discount
                      <br/><span>Buy stock at maximum possible discount</span><br/> &nbsp;
                      </li>
                    </ul>
                   </div>

                  <div className="offer_card col-xs-11 no-padding">
                   <ul>
                    <li className="offer_image col-xs-2"><img src={Api.cdn_url+"discount.png"} alt="" /></li>
                    <li className="offer_description col-xs-10">3% Discount
                    <br/><span>Get 3% discount on prepaid order <br />  of Rs. 25,000 & above</span>
                    </li>
                   </ul>
                  </div>

                  <div className="offer_card col-xs-11 no-padding">
                   <ul>
                      <li className="offer_image col-xs-2"><img src={Api.cdn_url+"offer.png"} alt="" /></li>
                      <li className="offer_description col-xs-10">App Cashback
                      <br/><span>Get 2% Cashback on delivery of order <br /> by App. available for 15 days</span>
                      </li>
                    </ul>
                   </div>
                 </div>
              
              {!this.props.international_store && !this.props.userlogin.is_dropshipper ?
              <div className="offer_image">
              {this.props.userlogin && this.props.userlogin.credit_application ?
               <a href={this.props.userlogin.credit_application} alt="credit application">
                 <img src={Api.cdn_url+"credit_banner_hindi_new.png"} alt="credit" />
               </a>
               : 
                 <img id="credit_banner" onClick={this.credit_application} src={Api.cdn_url+"credit_banner_hindi_new.png"} alt="credit" />
               }
              </div>
              : '' } 

              <Banner />
            
            {this.props.latest_product && this.props.latest_product.products.length ?
            <div className="new-arrival">
            
            <div className="container no-padding head_line slider_header">
                  <div className="col-xs-12">
                   <h4> New Arrivals</h4>
                  </div>
                  
            </div>

                <div className="scroll-x">
                {this.props.latest_product && this.props.latest_product.products ?
                  this.props.latest_product.products.map((product, index) => {
                 return (<Product product={product} key={index} count={index} />) 
                  })
                : ''}
                 </div>
            </div>
            : ''}

            {this.props.trending_product && this.props.trending_product.products.length ?
            <div className="new-arrival">
            <div className="container no-padding head_line slider_header">

                  <div className="col-xs-12">
                   <h4> Trending </h4>
                  </div>
                 
                </div>
                <div className="scroll-x">
                {this.props.trending_product && this.props.trending_product.products ?
                  this.props.trending_product.products.map((product, index) => {
                 return (<Product product={product} key={index} count={index} />) 
                  })
                 : ''}
                 </div>
            </div>
            : ''}

          {previousProduct && previousProduct.length ?
            <div className="recently_viewed">

            <div className="container no-padding head_line slider_header">
                  <div className="col-xs-9">
                   <h4> Recently Viewed Products</h4>
                  </div>
                  <div className="col-xs-3">
                  <Link to={Api.folder_path+"account/recent_view"} >
                   <button className="btn-xs col-xs-12">View All</button>
                   </Link>
                  </div>
            </div>

            <div className="container">
               {previousProduct.map((product, index) => {
                 return (<RecentlyProduct product={product} key={index} count={index} />) 
                  })
                }
            </div>
            </div>
            : ''}

          <div className="requirement col-xs-12">
           <h4>Didn't Get what you are looking for ?</h4>
           <Link to={Api.folder_path+"i/post-your-requirement"} >
           <button className="btn-xs post-btn right-border">Post Your Requirement</button><button className="btn-xs post-btn"><i className="fa fa-angle-double-right" aria-hidden="true"></i></button>
           </Link>
          </div>

       <HomeSubCategory SubCategoryData={this.state.SubCategoryData} SubCategoryPopup={this.state.SubCategoryPopup} SubCategoryPopupClose={this.SubCategoryPopupClose} />
       
       <Footer />
       </div>
    )
  }
   else
   {
     return (<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)
   }
  }
}


function mapStateToProps(state){
  return {
    menus: state.menuReducer.menus,
    latest_product: state.homeReducer.latest_product,
    userlogin: state.headerReducer.userlogin,
    international_store: state.headerReducer.userlogin.international_store,
    trending_product: state.homeReducer.trending_product,
    actions: bindActionCreators(latestData, trendingData, bannerData, OptPopupOpen, LoginPopupOpen)
  };
}
export default connect(mapStateToProps)(Home);
