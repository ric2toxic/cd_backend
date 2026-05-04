import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import $ from 'jquery'
import LazyLoad from 'react-image-lazy-load'
import { cartData } from '../../../actions/HeaderAction'
import Api from '../../../api/Api'
import { cart_add } from '../../../actions/CartAction'
import { OptPopupOpen, LoginPopupOpen} from '../../../actions/LoginAction';
import custom from '../../../custom/custom'

class Product extends Component {

  constructor(props)
   {
     super(props);
     this.cart_add              = this.cart_add.bind(this);
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

   
  render(){
    
function createMarkup(html) {
       return {__html: html};
     }


 return (<div className="pd_list_card_view" id={"wishlist_parrent_div_"+this.props.product.product_id}>
             <div className="pd_list_img_box">
              <Link to={Api.folder_path+this.props.product.href}>
              <LazyLoad loaderImage originalSrc={this.props.product.image} imageProps={{
               src: Api.cdn_url+"dw=100,dh=100,q=90/placeholder.jpg",
                }} />
              </Link>
              </div>
              
             <div className="pd_list_title"><Link to={Api.folder_path+this.props.product.href}>{this.props.product.title.substr(0, 25)+'...'}</Link></div>
            
             {this.props.product.special ? 
                <div>
                  <div className="c-product-list__old-price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div>
                  <div className="pd_list_price" dangerouslySetInnerHTML={createMarkup(this.props.product.special)}></div>
                </div>
                : <div className="pd_list_price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div> 
              }

             {this.props.product.options && this.props.product.options.length === 0 ?
             <button style={{marginTop:'5px'}} onClick={this.cart_add} className="add_to_cart_full_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> ADD TO CART</button>
              :
             <Link style={{marginTop:'5px'}} to={Api.folder_path+this.props.product.href} className="add_to_cart_full_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> ADD TO CART</Link>
             } 
           </div>)

}

}

function mapStateToProps(state){
  return {
    actions: bindActionCreators(cart_add,cartData)
  };
}

export default connect(mapStateToProps)(Product);
