import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import $ from 'jquery'
import LazyLoad from 'react-image-lazy-load'
import { cartData, wishlistData } from '../../actions/HeaderAction'
import { cart_add } from '../../actions/CartAction'
import { wishListProduct, wishListRemoveProduct } from '../../actions/AccountAction'
import Api from '../../api/Api'

class Product extends Component {

  constructor(props)
   {
     super(props);
     this.wishlist_remove       = this.wishlist_remove.bind(this);
     this.cart_add              = this.cart_add.bind(this);
   } 

  cart_add()
  {
     var self =this;
     var response = this.props.dispatch(cart_add(this.props.product.product_id, this.props.product.minimum));
     response.then(function(data) {
        self.props.dispatch(cartData());
      })
    .catch(function() {
          console.log('data fetch error');
    });
  }


wishlist_remove(event)
  {
    var self =this;
    var product_id = this.props.product.product_id
    $('body').removeClass('loaded').addClass('loading');
    var response = this.props.dispatch(wishListRemoveProduct(this.props.product.product_id));
       response.then(function(data){
          self.props.dispatch(wishlistData());
          $('body').removeClass('loading').addClass('loaded');
          $("#wishlist_parrent_div_"+product_id).parent("div").remove();
          if(data.data.count===0){
            self.props.dispatch(wishListProduct()); 
           }
          var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+ data.message+'</p></div></div>';
          $(document.body).append(success_div);   
          $('#notification').fadeOut(2000);
          setTimeout(function() {
                    $('#notification').remove();
          }, 2000);
       
        });
    event.preventDefault();
  }
   
  render(){

  let rating;
  let rating_class; 

  if(this.props.product.rating)
  {
    if(parseInt(this.props.product.rating,10) === 5) 
     {
       rating = 'Excellent';
       rating_class = 'excellent';
     }
     else if(parseInt(this.props.product.rating,10) === 4)
     {
       rating = 'Good';
       rating_class = 'good';   
     }
     else if(parseInt(this.props.product.rating,10) <= 3)
     {
       rating = 'Average';
       rating_class = 'average'; 
     }
  }

  function createMarkup(html) {
       return {__html: html};
     }

  var sor_enabled_text_1 = '';
  var sor_enabled_text_2 = '';
 if(this.props.product.is_sor_enabled)
 {
   var sor_enabled_text = this.props.product.sor_enabled_text.split("within");
       sor_enabled_text_1 = sor_enabled_text[0]+" within";
       sor_enabled_text_2 = sor_enabled_text[1];
 }
 
 return (<div className="pd_list_card_view" id={"wishlist_parrent_div_"+this.props.product.product_id}>
             <div className="pd_list_img_box">
              <Link to={Api.folder_path+this.props.product.href}>
               <LazyLoad loaderImage originalSrc={this.props.product.image} imageProps={{
               src: Api.cdn_url+"dw=100,dh=100,q=90/placeholder.jpg",
                }} />
              </Link>
              </div>
                <div className="pd_card_cart_op">
                    <ul>
                        <li><Link to="#" className="full-radius" onClick={(event) => this.wishlist_remove(event)} id={'wishlist_heart_'+this.props.product.product_id}><i className="fa fa fa-times" aria-hidden="true"></i></Link>
                        </li>
                    </ul>
             </div>
             <div className="pd_list_title"><Link to={Api.folder_path+this.props.product.href}>{this.props.product.name}</Link></div>
             
             <div className="pd_list_reting">
             {$.trim(rating)!==''?
             <span className={rating_class}>{rating}</span>
             : ''}
             </div>
             
              {this.props.product.special ? 
                <div>
                   <div className="c-product-list__old-price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div>
                  <div className="pd_list_price" dangerouslySetInnerHTML={createMarkup(this.props.product.special)}></div>
                </div>
                : <div className="pd_list_price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div> 
              }

              <div className="blue_bg">
                   {this.props.product.is_sor_enabled ?
                   <div ><img className="round_box" src={Api.cdn_url+"sor_box.png"} alt="sor_box" />
                   <div className="buyback_text"><div>{sor_enabled_text_1}</div> <div>{sor_enabled_text_2}</div></div>
                   </div>
                   : ''}
              </div>
             
             <div className="pd_list_info">{this.props.product.set_description}  </div>
             {this.props.product.options.length === 0 ?
             <button onClick={this.cart_add} className="add_to_cart_full_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> ADD TO CART</button>
              :
             <Link to={Api.folder_path+this.props.product.href} className="add_to_cart_full_btn"><i className="fa fa-shopping-bag" aria-hidden="true"></i> ADD TO CART</Link>
             } 
           </div>)

}

}

function mapStateToProps(state){
  return {
    actions: bindActionCreators(cart_add,wishListRemoveProduct,wishListProduct,cartData,wishlistData)
  };
}

export default connect(mapStateToProps)(Product);
