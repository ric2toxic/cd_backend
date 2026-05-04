import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import $ from 'jquery'
import SimpleImg from '../../component/simple_img'
import { cartData, wishlistData } from '../../actions/HeaderAction'
import { cart_add, addToWishlist } from '../../actions/CartAction'
import { OptPopupOpen, LoginPopupOpen} from '../../actions/LoginAction';
import custom from '../../custom/custom'
import Api from '../../api/Api'

class RelatedProduct extends Component {

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

    var response = this.props.dispatch(cart_add(this.props.product.product_id, this.props.product.minimum));
    response.then(function(data) {
        self.props.dispatch(cartData());
      })
    .catch(function() {
          console.log('data fetch error');
    });
   }
  }

  wishlist_add(e)
  {
    e.preventDefault();
    var self = this;
    var response = this.props.dispatch(addToWishlist(this.props.product.product_id));
    response.then(function(data) {
        self.props.dispatch(wishlistData());
      })
    .catch(function() {
          console.log('data fetch error');
    });
  }
   
  render(){

  var sor_enabled_text_1 = '';
  var sor_enabled_text_2 = '';
 if(this.props.product.is_sor_enabled)
 {
   var sor_enabled_text = this.props.product.sor_enabled_text.split("within");
       sor_enabled_text_1 = sor_enabled_text[0]+" within";
       sor_enabled_text_2 = sor_enabled_text[1];
 }
 
function createMarkup(html) {
       return {__html: html};
     }
     
 return (
     <div className="col-xs-5 no-padding product_card">
 	      <div className="pd_list_card_view">

            <div className="clip-prd-sell-type clip-tag-bestseller">
                <p className="sell-type-txt pf-margin-0">{this.props.product.pickup_city}</p>
            </div>

            { this.props.product.exp_dispatch_days > 0 ?
                <div className="product_card_teg_banner">
                  <div className="card-header-opt">
                    <small>Available after {this.props.product.exp_dispatch_days} days</small>
                  </div>                                            
                 </div>
                : ''
              }

             <div className={this.props.product.img_vertical ? "pd_list_img_big_box" : "pd_list_img_box" }>
              <Link to={Api.folder_path+this.props.product.href}>
              
                <SimpleImg 
                 src={this.props.product.image}
                />

              </Link>
              </div>
                <div className="pd_card_cart_op">
                    <ul>
                        <li>
                        <Link to="#" className="full-radius heart" id={'wishlist_heart_'+this.props.product.product_id} onClick={this.wishlist_add}><i className={this.props.product.fill_heart} aria-hidden="true"></i></Link>
                         </li>
                    </ul>
             </div>
             <div className="pd_list_title"><Link to={Api.folder_path+this.props.product.href}>{this.props.product.name}</Link></div>
             
             {this.props.product.special ? 
                <div>
                  <div className="c-product-list__old-price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div>   
                  <div className="pd_list_price" dangerouslySetInnerHTML={createMarkup(this.props.product.special)}></div>
                </div>
                : <div className="pd_list_price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div> 
              }

              <div className="blue_bg">
                   {this.props.product.is_sor_enabled ?
                   <div ><img className="round_box" src={Api.cdn_url+"sor_box.png"} alt="sor box" />
                   <div className="buyback_text"><div>{sor_enabled_text_1}</div> <div>{sor_enabled_text_2}</div></div>
                   </div>
                   : ''}
              </div>
           
            {this.props.product.stock_status === 'out of stock' ?
              <button style={{marginTop:'6px'}} className="add_to_cart_full_btn out_of_stock_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> &nbsp; Out Of Stock</button>
             :
              this.props.product.options.length === 0 ?
             <button style={{marginTop:'6px'}} onClick={this.cart_add} className="add_to_cart_full_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> &nbsp; ADD TO CART</button>
              :
             <Link style={{marginTop:'6px'}} to={Api.folder_path+this.props.product.href} className="add_to_cart_full_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> &nbsp; ADD TO CART</Link>
             } 
           </div>
           </div>)

}

}

function mapStateToProps(state){
  return {
    actions: bindActionCreators(cart_add, addToWishlist, OptPopupOpen, LoginPopupOpen, wishlistData, cartData)
  };
}
export default connect(mapStateToProps)(RelatedProduct);


