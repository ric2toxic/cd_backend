import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { Link } from 'react-router-dom'
import { cart_add, addToWishlist } from '../../actions/CartAction'
import { OptPopupOpen, LoginPopupOpen} from '../../actions/LoginAction'
import LazyLoad from 'react-image-lazy-load'
import Api from '../../api/Api'
import custom from '../../custom/custom'
import $ from 'jquery'

class RecentlyProduct extends Component {

  constructor(props)
   {
     super(props);
     this.cart_add     = this.cart_add.bind(this);
     this.wishlist_add = this.wishlist_add.bind(this);
   } 

  cart_add()
  {
    var self =this;
    if(custom.getCookie("customer_mobile") === '')
   {
    this.props.dispatch(OptPopupOpen(1));
    setTimeout(function(){ $("#redirect_cart").val(self.props.product.product_id+'-'+self.props.product.minimum); }, 1000);
   }
   else if(custom.getCookie("register_user") === '1' && custom.getCookie("customer_id") === '')
   {
    this.props.dispatch(LoginPopupOpen(1));
    setTimeout(function(){ $("#redirect_cart1, #login_redirect_cart").val(self.props.product.product_id+'-'+self.props.product.minimum); }, 1000);
   }
   else
   {

    this.props.dispatch(cart_add(this.props.product.product_id, this.props.product.minimum));
   }
  }

  wishlist_add(e)
  {
    e.preventDefault();
    this.props.dispatch(addToWishlist(this.props.product.product_id));
  }

 render(){
            return(
                 <div className="col-xs-12 no-padding recently_viewed_list">
                 <div className="product_group">
                 <div className="col-xs-3"><Link to={Api.folder_path+this.props.product.href}>
                 <LazyLoad loaderImage originalSrc={this.props.product.image} imageProps={{
               src: "https://cdnimages.net/img/dw=100,dh=100,q=90/placeholder.jpg",
                }} />

                 </Link></div>
                 <div className="col-xs-9 r_v_p_detail">
                 <div className="recently_viewed_description"><p><Link to={Api.folder_path+this.props.product.href}>{this.props.product.title.substr(0, 25)+'...'}</Link></p></div>
                 
                 {this.props.product.special ?
                  <div>
                  <span className="recent_old_price">{$('<div/>').html(this.props.product.price).text()}</span>
                  <span>{$('<div/>').html(this.props.product.special).text()}</span>
                 </div>
                 :
                 <div>
                 <span>{$('<div/>').html(this.props.product.price).text()}</span>
                  </div>
                 }

                
                 <div className="button-group">
                 <button className="wishlist-btn btn-xs" onClick={this.wishlist_add}>ADD TO WISHLIST</button>
                
                 {this.props.product.stock_status === 'out of stock' ?
                  <button className="cart-btn btn-xs stock-btn"> Out Of Stock</button>
                  :
                  this.props.product.options && this.props.product.options.length > 0 ?
                  <Link to={Api.folder_path+this.props.product.href} className="cart-btn btn-xs" style={{padding:'2px 10px'}}> ADD TO CART</Link>
                  :
                  <button onClick={this.cart_add} className="cart-btn btn-xs"> ADD TO CART</button>
                  } 

                 </div>
                 </div>
                 </div>
                 </div>
                   )
   }
}

function mapStateToProps(state){
  return {
    actions: bindActionCreators(cart_add, addToWishlist, OptPopupOpen, LoginPopupOpen)
  };
}
export default connect(mapStateToProps)(RecentlyProduct);
