import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { bindActionCreators } from 'redux'
import { withLastLocation } from 'react-router-last-location'
import Lightbox from 'react-image-lightbox'
import 'react-image-lightbox/style.css'
import $ from 'jquery'
import Helmet from 'react-helmet'
import custom from '../../custom/custom'
import SimpleImg from '../../component/simple_img'
import { Carousel } from 'react-responsive-carousel'
import { cartData, wishlistData } from '../../actions/HeaderAction'
import { product_detailData, related_productData} from '../../actions/ProductDetailAction'
import { cart_add, addToWishlist, cart_add_with_option } from '../../actions/CartAction'
import { OptPopupOpen, LoginPopupOpen} from '../../actions/LoginAction'
import RelatedProduct from './related_product'
import Options from './options'
import InputNumber from './input_number'
import Api from '../../api/Api'
import ApiError  from '../loading/api_error'
import 'react-responsive-carousel/lib/styles/carousel.min.css'
import  './index.css'
import { product_listData} from '../../actions/CategoryAction'
import Drawer from 'material-ui/Drawer'
import IWantDesign from '../login/i_want_design'
import AskQuestion from './ask_question'
import Share from './share';
import { DesignPopupOpen } from '../../actions/LoginAction';
import { QuestionPopupOpen } from '../../actions/ProductDetailAction'

class ProductDetail  extends Component {

   constructor(props)
   {
     super(props);
      this.question_popup = this.question_popup.bind(this);
      this.design_popup = this.design_popup.bind(this);
      this.addToCart = this.addToCart.bind(this);
      this.addToCartOption = this.addToCartOption.bind(this);
      this.retry    = this.retry.bind(this);
      this.wishlist_add = this.wishlist_add.bind(this);
      this.toggleDetailFilterDrawer = this.toggleDetailFilterDrawer.bind(this);
      this.closeImgsViewer = this.closeImgsViewer.bind(this);
      this.openImgsViewer  = this.openImgsViewer.bind(this);
      this.SharePopupOpen  = this.SharePopupOpen.bind(this);
      this.SharePopupClose  = this.SharePopupClose.bind(this);
      this.recentViewProduct = this.recentViewProduct.bind(this);
      this.downloadAll       = this.downloadAll.bind(this);
       
      this.state = {
          show_product_detail:false,
          detailFilterDrawerOpen:false,
          imageViewOpen: false,
          currImg: 0,
          SharePopup:false
       };

   } 
   
  componentDidMount()
  { 
    if(!this.props.product_detail_id && this.props.product_detail_id !== this.props.match.params.product_id)
    {
      var self = this;
      var response = this.props.dispatch(product_detailData(this.props.match.params.product_id));
      response.then(function(data) {
      $("html, body").animate({ scrollTop: 0 }, 800);
      self.props.dispatch(related_productData(self.props.match.params.product_id, data.seller_id));
      })
      .catch(function() {
          console.log('data fetch error');
      });
    }
  } 

  componentWillReceiveProps(){
    this.setState({
      show_product_detail:true
    });
  }

  componentDidUpdate(prevProps)
  {
    // $('html, body').animate({scrollTop : 0},100);
    if(typeof(this.props.product_detail) !== "undefined" &&  prevProps.product_detail !== this.props.product_detail){
        this.valueChange();
        this.recentViewProduct();
      }
  }

  downloadAll()
  {
     custom.downloadImages(this.props.product_detail.product_id, this.props.product_detail.model);
  }

  recentViewProduct()
  {
    var k='0';
      var unique_product_ky=[];
      var all_recent_product = [];
      var recentViewProduct="recentViewProduct";
      if(window.location.href.indexOf("/staging") > -1) {
         recentViewProduct="staging_recentViewProduct";
      }
       var all_recent_product_array = {
                 'product_id'  : this.props.product_detail.product_id,
                 'price'       : this.props.product_detail.price,
                 'special'     : this.props.product_detail.special,
                 'image'       : this.props.product_detail.pan_detail,
                 'title'       : this.props.product_detail.heading_title,
                 'href'        : this.props.product_detail.mobile_href,
                 'fill_heart'  : this.props.product_detail.fill_heart,
                 'minimum'     : this.props.product_detail.minimum,
                 'stock_status': this.props.product_detail.stock_status,
                 'options'     : this.props.product_detail.options,
                 'current'     : '1'
             };
             unique_product_ky.push(this.props.product_detail.product_id);
             all_recent_product[k] = all_recent_product_array;
             k++;

            if (localStorage.getItem(recentViewProduct)) {
                  var previousProduct = JSON.parse(localStorage.getItem(recentViewProduct));
                     $.each(previousProduct, function(index, value) {
                         var unique_key = value.product_id;
                         //=====check for dublicate Value========
                         if ($.inArray(unique_key, unique_product_ky) < '0') {
                             unique_product_ky.push(unique_key);
                              var all_recent_product_array = {
                                    'product_id'  : value.product_id,
                                    'price'       : value.price,
                                    'special'     : value.special,
                                    'image'       : value.image,
                                    'title'       : value.title,
                                    'href'        : value.href,
                                    'fill_heart'  : value.fill_heart,
                                    'minimum'     : value.minimum,
                                    'stock_status': value.stock_status,
                                    'options'     : value.options,
                                    'current'     : '0'
                     };
                             all_recent_product[k] = all_recent_product_array;
                             k++;

                         }
                         if (k === "30") {
                             return false;
                         }

                     });
                 }
                 localStorage.setItem(recentViewProduct, JSON.stringify(all_recent_product));
  }

  SharePopupOpen()
  {
    this.setState({
      SharePopup:true
    });
  }

  SharePopupClose()
  {
   this.setState({
      SharePopup:false
    }); 
  }

  closeImgsViewer()
  {
    this.setState({imageViewOpen: false, currImg: 0}); 
  }

  openImgsViewer(index, event)
  {
    this.setState({imageViewOpen: true, currImg: index}); 
  }

 design_popup()
 {
   var self = this;
   this.props.dispatch(DesignPopupOpen(1));
   setTimeout(function(){ 
      $('#want_design_product_id').val(self.props.product_detail.product_id);
      $('#want_design_warning').html("");
      if(custom.getCookie("customer_mobile") !== '')
      {
         $("#design_customer_mobile").hide();
      }
      else
      {
        $("#design_customer_mobile").show();
      }
    }, 200);
 }

question_popup()
 {
   var self = this;
   this.props.dispatch(QuestionPopupOpen(1));
   setTimeout(function(){ 
      $('#Question_product_id').val(self.props.product_detail.product_id);
      $('#ask_question_warning').html("");
      if(custom.getCookie("customer_mobile") !== '')
      {
         $("#question_customer_name, #question_telephone, #question_email").hide();
      }
      else
      {
        $("#question_customer_name, #question_telephone, #question_email").show();
      }
    }, 200);
 }

  valueChange(){
    this.setState({
      show_product_detail:true
    });
  }

  addToCart()
  {
   
    var self =this;
    var quantity = $("#option-input_quantity").val();
   if(quantity > 0) 
   {
     if(custom.getCookie("customer_mobile") === '')
     {
       this.props.dispatch(OptPopupOpen(1));
       setTimeout(function(){ $("#redirect_cart").val(self.props.product_detail.product_id+'-'+quantity); }, 1000);
     }
     else if(custom.getCookie("register_user") === '1' && custom.getCookie("customer_id") === '')
     {
       this.props.dispatch(LoginPopupOpen(1));
       setTimeout(function(){ $("#redirect_cart1, #login_redirect_cart").val(self.props.product_detail.product_id+'-'+quantity); }, 1000);
     }
      else
      {
       var response = this.props.dispatch(cart_add(self.props.product_detail.product_id, quantity, 0));
       response.then(function(data) {
        self.props.dispatch(cartData());
        })
       .catch(function() {
          console.log('data fetch error');
        });
      }

    }
    else 
      { 
        var success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Error</strong><hr class="message-inner-separator"><p>Please enter product quantity</p></div></div>';
        $(document.body).append(success_div);   
        $('#notification').fadeOut(2000);
         setTimeout(function() {
         $('#notification').remove();
         }, 2000);
      }

  }

  addToCartOption() 
  {   
    var self = this;
    this.toggleDetailFilterDrawer();
    if(custom.getCookie("customer_mobile") === '')
        { 
          this.props.dispatch(OptPopupOpen(1));
          setTimeout(function(){ $("#redirect_cart").val(1); }, 1000);
        }
    else if(custom.getCookie("register_user") === 1 && custom.getCookie("customer_id") === '')
       { 
         this.props.dispatch(LoginPopupOpen(1));
         setTimeout(function(){ $("#redirect_cart1, #login_redirect_cart").val(1); }, 1000);
       }
    else
    { 
      var response = this.props.dispatch(cart_add_with_option());
      response.then(function(data) {
        self.props.dispatch(cartData());
       })
      .catch(function() {
          console.log('data fetch error');
       });
    }
}

  retry()
  {
     var self = this;
     var response = this.props.dispatch(product_detailData(this.props.match.params.product_id));
    response.then(function(data) {
      self.props.dispatch(related_productData(self.props.match.params.product_id, data.seller_id));
    })
    .catch(function() {
          console.log('data fetch error');
     });
  }

  wishlist_add()
  {
    var self = this;
    var response = this.props.dispatch(addToWishlist(this.props.product_detail.product_id));
    response.then(function(data) {
        self.props.dispatch(wishlistData());
      })
    .catch(function() {
          console.log('data fetch error');
    });
  }
   
  toggleDetailFilterDrawer(){
    this.setState({detailFilterDrawerOpen: !this.state.detailFilterDrawerOpen});
    if(!this.state.detailFilterDrawerOpen){
    $('.detail_filter_drawer_div').parent().addClass('drawerShow');
    $('.detail_filter_drawer_div').parent().removeClass('drawerCollapse');
    }else{
    $('.detail_filter_drawer_div').parent().removeClass('drawerShow');
    $('.detail_filter_drawer_div').parent().addClass('drawerCollapse');
    }
  }


  render(){
   
   if(this.props.product_detail_id && this.props.product_detail_id !== this.props.match.params.product_id)
   {
     if(this.state.show_product_detail) { $('body').removeClass('loaded').addClass('loading'); }
     
     var self = this;
     var response = this.props.dispatch(product_detailData(this.props.match.params.product_id));
    response.then(function(data) {
      $("html, body").animate({ scrollTop: 0 }, 800);
      self.props.dispatch(related_productData(self.props.match.params.product_id, data.seller_id));
    })
    .catch(function() {
          console.log('data fetch error');
     });
   }


  let rating;
  let rating_class;
  let product_option;


 if(this.props.product_detail && this.props.product_detail_success === 1)
  {
     product_option = this.props.product_detail.options.map((option, index) => {
        if(index === 0)
        {
         return option
        }
        else
        {
          return false;
        }
     });

      product_option = $.grep(product_option,function(n){ return n === 0 || n });
  }

  if(this.props.product_detail.rating)
  {
    if(parseInt(this.props.product_detail.rating,10) === 5) 
     {
       rating = 'Excellent';
       rating_class = 'excellent';
     }
     else if(parseInt(this.props.product_detail.rating,10) === 4)
     {
       rating = 'Good';
       rating_class = 'good';   
     }
     else if(parseInt(this.props.product_detail.rating,10) <= 3)
     {
       rating = 'Average';
       rating_class = 'average'; 
     }
  }


 if(!this.state.show_product_detail && this.props.product_detail_id && this.props.product_detail_id !== this.props.match.params.product_id)
 {
  return (<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)

 }
  else if(!this.props.product_detail && this.props.product_detail_success === 0)
 {  
    return (<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)
 }
 else if(this.props.product_detail_success === -2)
 {  
    return (<ApiError retry={this.retry} />)
 }
 else
 {

   var href = this.props.product_detail.last_category_href; 
    if(this.props.product_detail.last_category_href.charAt(0) === "/")
     {
        href = this.props.product_detail.last_category_href.substr(1);  
     }



var lastLocation = Api.folder_path+href;

if(this.props.lastLocation && parseInt(this.props.lastLocation.pathname.search("p/"), 10)  !== 1 )
{
  if(this.props.lastLocation.pathname === Api.folder_path+'cart')
  {
    lastLocation = Api.folder_path+'cart';
  }
  else if(parseInt(this.props.lastLocation.pathname.search("account"), 10) > 0)
  {
    lastLocation = this.props.lastLocation.pathname;
  } 
  else if(this.props.lastLocation.pathname === Api.folder_path)
  {
    lastLocation = Api.folder_path;
  }
  else
  {
    lastLocation = Api.folder_path+this.props.path_keyword+this.props.category_filter_path;
  }
}

/*var prev_product = false;
var next_product = false;
if(this.props.product_list.length > 0){

  var product=[];
    for (var index = 0; index < this.props.product_list.length; index++) {

          product = this.props.product_list[index];
          if(parseInt(product.product_id, 10) === this.props.product_detail.product_id ){
            if(index === 0){
                  prev_product = false;
              if(this.props.product_list.length === 1){
                next_product=false;
              }else{
                next_product=this.props.product_list[1];
              }
            }
            else{
              prev_product=this.props.product_list[index-1];
              if(this.props.product_list.length === index+1){
                next_product=false;
              }
              else{
                next_product=this.props.product_list[index+1];
              }
            }
            if(index === this.props.product_list.length){
              next_product=false;
            }
             // console.log(prev_product);
             // console.log(next_product);
            break;
          }
         
         }
}*/

function createMarkup(html) {
       return {__html: html};
     }

function return_policy()
  {
    $('#return_policy_data').slideToggle();
    $(".fa-plus").toggleClass("fa-minus");
  }     

    return (
     <section> 
      <Helmet>
        <meta charSet="utf-8" />
        <title>{this.props.product_detail.meta_title}</title>
        <meta name="description" content={this.props.product_detail.meta_description} />
        <meta name="keywords" content={this.props.product_detail.meta_keywords} />
      </Helmet>

    <div className="contner">
     {this.props.product_detail ?
      <div className="pd_detail_area pd_detail_head_margin">
       
       <section className="image_view">
       
       

        <div className="detail_back_btn">
          <Link to={lastLocation}><i className="fa fa-long-arrow-left" aria-hidden="true"></i></Link>
        </div>
        <div className="image_slider">
        <div className="clip-prd-sell-type-detail clip-tag-bestseller">
          <p className="sell-type-txt pf-margin-0">{this.props.product_detail.pickup_city}</p>
        </div>

        { this.props.product_detail.exp_dispatch_days > 0 ?
                <div className="product_card_teg_banner">
                  <div className="card-header-opt">
                    <small>Available after {this.props.product_detail.exp_dispatch_days} days</small>
                  </div>                                            
                 </div>
                : ''
              }
             <Carousel className="detail_image_slider" showThumbs={false} showArrows={false} showIndicators={true}>
              { this.props.product_detail.images.map((image, index) => { 
                return (<div  className="detail_image" key={index} onClick={(e) => this.openImgsViewer(index, e)}>
                  <Link to="#" className="image_list">
                   <SimpleImg 
                      src={image.pan_detail}
                      className="img-responsive cursor"
                     />
                  </Link>
                </div>)
              })
            }
            </Carousel>

        </div>
        <div className="detail_image_btn">
          <ul>
           <li>
            <Link to="#" onClick={this.downloadAll}><i className="fa fa-download" aria-hidden="true"></i></Link>
           </li>
           <li><Link to="#" onClick={this.SharePopupOpen}><i className="fa fa-share-alt" aria-hidden="true"></i></Link></li>
           <li><Link to="#"><i className="fa fa-question" aria-hidden="true" onClick={this.question_popup}></i></Link></li>
           <li><Link to="#"  id={'wishlist_heart_'+this.props.product_detail.product_id} onClick={ this.wishlist_add }>
            <i className={this.props.product_detail.fill_heart} aria-hidden="true" />
           </Link></li>
            
          </ul>
          
        </div>
         
       </section>
       <div className="clearfix"></div>
          
       <div className="pd_detail_description">
       
           <h3 className="pd_detail_title" style={{marginTop:'0px'}}>{this.props.product_detail.heading_title}</h3>

         <div className="col-xs-12">
            <div className="pd_detail_code"><b>Model:</b> {this.props.product_detail.model}</div>
            <div className="pd_detail_code">
             <b>HSN:</b> {this.props.product_detail.hsn_code}
              
              <span style={{marginLeft:'10px'}} className={rating_class}>{rating}</span>
            </div> 

             {this.props.product_detail.is_sor_enabled ?
             <div className="blue_bg_detail">
                   <div ><img className="round_box" src={Api.cdn_url+"sor_box.png"} alt="sor_box" />
                   <div className="buyback_text"><span>{this.props.product_detail.sor_enabled_detail_text}</span></div>
                   </div>
              </div> 
              : ''}
            
            <div className="price_box">
                   {this.props.product_detail.special ? 
                    <div className="col-xs-12 nopadding">
                     <div className="c-product-card__old-price" dangerouslySetInnerHTML={createMarkup(this.props.product_detail.price)}></div>   
                    <div className="pd_detail_price" dangerouslySetInnerHTML={createMarkup(this.props.product_detail.special)}></div>
                    </div>
                    : 
                    <div className="col-xs-12 nopadding">
                     <div className="pd_detail_price" dangerouslySetInnerHTML={createMarkup(this.props.product_detail.price)}></div> 
                     </div>
                   }
                 
            </div>
         </div>

         

         <div className="clearfix"></div>
       </div>

       <div className="col-xs-12 nopadding pd_detail_set_description">
          <div className="control-group" style={{borderBottom:'0px'}}>
            <label className="control-label">Minimum Order  </label>
            <span>{this.props.product_detail.minimum} Set   </span>
          </div>
       </div>

       <div className="col-xs-12 nopadding pd_detail_set_description">
          <div className="control-group">
            <label className="control-label"> Set Description  </label>
          </div>
         <div className="control-group-data">
           <div dangerouslySetInnerHTML={createMarkup(this.props.product_detail.set_description)}></div>
           <div dangerouslySetInnerHTML={createMarkup(this.props.product_detail.description)}></div>
         </div>   
       </div>

       {product_option.length > 0 ? 
         product_option.map((options, i) => {
          return(
                <div className="col-xs-12 nopadding pd_detail_set_description">
                 <div className="control-group">
                  <label className="control-label">{options.name} </label>
                </div>
                 <div className="control-group-data">
                   {
                    options.product_option_value.map((option, index) => {
                    var divStyle = { background: option.name }
                    return(<span><div className={"color_code "+option.name.toLowerCase()} style={divStyle}>
                      {options.name === 'Color' ? '' : option.name }
                      </div></span>)})
                   }
                </div>
                </div>
                )})
           : ''

        }

       <div className="clearfix"></div>
       {this.props.product_detail.filters && this.props.product_detail.filters.length > 0 ?
       <div className="col-xs-12 nopadding pd_detail_set_description">
          <div className="control-group">
            <label className="control-label">Product Description </label>
          </div>
         <div className="control-group-data"> 
         <ul>
          { this.props.product_detail.filters.map((filter, index) => { 
          return(<li key={index}><Link to="#">{filter.filter_name}</Link></li>)
           })
           }
         </ul> 
        </div> 
       </div>
        : ''}
      </div>
       : '' } 
       <div className="clearfix"></div>

        <div className="col-xs-12 nopadding pd_detail_set_description">
          <div className="control-group" onClick={()=> return_policy() }>
            <label className="control-label"> Shipping Charge and Return Policy &nbsp;  &nbsp;
            <i className="fa fa-plus" aria-hidden="true"></i>
           </label>
          </div>
         <div className="control-group-data" id="return_policy_data">
            
            <p>{this.props.product_detail.language.shipping_charges_txt}</p>
            
            <p>{this.props.product_detail.language.delivery_txt}</p>

            <p>{this.props.product_detail.language.dispatch_txt}</p>

            <p>
              <b>{this.props.product_detail.language.courier_txt}</b><br />
                    {this.props.product_detail.language.courier_txt_1}<br />
                    {this.props.product_detail.language.courier_txt_2}
            </p>

            <p>
                 <b>{this.props.product_detail.language.surface_txt}</b> 
                 {this.props.product_detail.language.surface_txt_1}
            </p>
                
              {this.props.product_detail.seller_returnable === false ?
                 <p>{this.props.product_detail.language.return_policy_txt}</p>
              :''} 
                 
              {this.props.product_detail.seller_returnable === false ?
                  <p>For more information <Link to={Api.folder_path+"i/returns-policy/"}>click here</Link></p>
                : <div className="red_text"><strong>This item is non-returnable</strong></div> } 
         </div>   
       </div>

         <div className="clearfix"></div>

        {this.props.related_products && this.props.related_products.products.length ?
            <div className="recent_view_list">
            <div className="container no-padding head_line slider_header">
                   <h4> Related Products </h4>
                </div>
                <div className="scroll-x" style={{marginTop:'10px'}}>
                {this.props.related_products.products.map((product, index) => {
                 return (<RelatedProduct product={product} key={index} count={index} />) 
                  })
                }
                 </div>
            </div>
            : ''}

   </div>
 {product_option.length > 0 ? 
  <Drawer
          docked={false}
          open={this.state.detailFilterDrawerOpen}
        >
  <div className="detail_container detail_filter_drawer_div" id="filter_container">
  <div className="apply_filters">
          <Link className="clear-all option_btn" style={{ width:'50%' }} to="#" onClick={this.toggleDetailFilterDrawer}>CANCEL</Link>
          <button className="filter_apply_btn filter_container_close add_cart " style={{ width:'50%' }} onClick={this.addToCartOption} ><i className="fa fa-shopping-bag" aria-hidden="true"></i> ADD TO CART</button>
        </div>

   <div className="filter_box" id="product">
          <input type="hidden" name="product_id" id="product_id" value={this.props.product_detail.product_id} />
          <input type="hidden" name="for_detail_page" id="for_detail_page" value="0" />
          <input type="hidden" name="customer_id" id="customer_id"  />
          <input type="hidden" name="customer_access_token" id="customer_access_token" />
          <input type="hidden" name="cart_session_id" id="cart_session_id" />
          <div className="sort_filter_head">
           {
             product_option.map((option, index) => { 
             return( <span>Select {option.name} and Quantity</span>)
             })
           }
             <button type="button"  className="close close_filters filter_container_close close_filters_detail" onClick={this.toggleDetailFilterDrawer}>&times;</button>
          </div>
          <div className="filter_body">
          <table width="100%" className="table set_qty_table table-striped table-hover">
              <thead>
               <tr className="cart_table_title">
               <th width="40%">
                {
                  product_option.map((option, index) => { 
                  return( option.name )
                   })
                }
               </th>
               <th width="30%">Available </th>
               <th width="30%">Quantity</th></tr>
              </thead>
               {
                product_option.map((options, index) => {
                return (<Options product_option_name={options.name} product_option_id={options.product_option_id} options={options.product_option_value} key={index} />)
                })
             }
              </table>
          </div>
    </div>      
  </div>
  </Drawer>
  :''
  }

   <div className="detail_end"></div>
   <div className="pd_detail_btn_bottom">
   {this.props.product_detail ?
    this.props.product_detail.stock_status === 'out of stock' ?
    <ul>
        
        <li><button className="out_of_stock_btn sort_filter apply-selected"> Out Of Stock</button></li>
        <li><button onClick={this.design_popup} className="add_to_cart_btn sort_filter apply-selected">I Want Design</button> </li>
     </ul>
    :
    this.props.product_detail.options.length === 0 ?
     <ul>
        <li>
          <InputNumber name="quantity" name_max="quantity_max" minimum={this.props.product_detail.minimum} quantity={this.props.product_detail.quantity} product_option_value_id="quantity" from_options={false}/>
        </li>
        <li><button onClick={this.addToCart} className="add_to_cart_btn sort_filter apply-selected"><i className="fa fa-shopping-bag" aria-hidden="true"></i> &nbsp; ADD TO CART</button></li>
     </ul>
     :
     <ul>
       {
        product_option.map((option, index) => { 
          return(<li>
        <button onClick={this.toggleDetailFilterDrawer} className="add_to_cart_btn sort_filter apply-selected option_btn"><i className="fa fa-sliders" aria-hidden="true"></i> &nbsp; SELECT {option.name.toUpperCase()}</button>  
        </li>)
        })
       }
        <li><button id="button-cart"  className="add_to_cart_btn sort_filter apply-selected add_cart" onClick={this.addToCartOption}><i className="fa fa-shopping-bag" aria-hidden="true"></i> &nbsp; ADD TO CART</button></li>
     </ul>

     :  ''

   }
   </div>

   <IWantDesign />
   <AskQuestion />

   <Share SharePopup={this.state.SharePopup} url={Api.api_url+this.props.product_detail.mobile_href} product_name={this.props.product_detail.product_name} product_image={this.props.product_detail.pan_detail} SharePopupClose={this.SharePopupClose} />
  
  {this.state.imageViewOpen && (
          <Lightbox
            mainSrc={this.props.product_detail.images[this.state.currImg].original}
            nextSrc={this.props.product_detail.images[(this.state.currImg + 1) % this.props.product_detail.images.length].original}
            prevSrc={this.props.product_detail.images[(this.state.currImg + this.props.product_detail.images.length - 1) % this.props.product_detail.images.length].original}
            onCloseRequest={this.closeImgsViewer}
            onMovePrevRequest={() =>
              this.setState({
                currImg: (this.state.currImg + this.props.product_detail.images.length - 1) % this.props.product_detail.images.length,
              })
            }
            onMoveNextRequest={() =>
              this.setState({
                currImg: (this.state.currImg + 1) % this.props.product_detail.images.length,
              })
            }
            nextSrcThumbnail={this.props.product_detail.images[(this.state.currImg + 1) % this.props.product_detail.images.length].thumb}
            prevSrcThumbnail={this.props.product_detail.images[(this.state.currImg + this.props.product_detail.images.length - 1) % this.props.product_detail.images.length].thumb}

          />
        )
    }

   </section>
    )
  }  
    
  }
}

function mapStateToProps(state){
  return {
    path_keyword: state.categoryReducer.path_keyword,
    product_list: state.categoryReducer.product_list,
    category_filter_path: state.categoryReducer.category_filter_path,
    product_detail_id:state.product_detailReducer.product_detail_id,
    product_detail:state.product_detailReducer.product_detail,
    product_detail_success:state.product_detailReducer.product_detail_success,
    related_products:state.product_detailReducer.related_products,
    actions: bindActionCreators(product_listData,product_detailData, related_productData, cart_add, addToWishlist,cart_add_with_option,DesignPopupOpen,QuestionPopupOpen,wishlistData,cartData)
  };
}

export default withLastLocation(connect(mapStateToProps)(ProductDetail));

