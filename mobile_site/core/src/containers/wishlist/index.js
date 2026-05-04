   import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import {GridList} from 'material-ui/GridList';
import { wishlistData } from '../../actions/HeaderAction'
import { wishListProduct, wishListClearAllProduct } from '../../actions/AccountAction'
import NoProduct from '../loading/no_product'
import Product from './product'
import  './index.css'
import ScrollToTop from 'react-scroll-up';
import $ from 'jquery'


class Wishlist  extends Component {

   constructor(props)
   {
     super(props);
     this.wishlist_all_clear    = this.wishlist_all_clear.bind(this);
   } 

  componentWillMount()
  { 
    this.props.dispatch(wishListProduct());
  }

  wishlist_all_clear(event)
  {
    var self =this;
    $('body').removeClass('loaded').addClass('loading');
    var response = this.props.dispatch(wishListClearAllProduct(this.props.wishlist_product_detail.products));
       response.then(function(data){
          self.props.dispatch(wishlistData());
          $('body').removeClass('loading').addClass('loaded');
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

    
   if(this.props.wishlist_product_detail)
   {
     let product_item = '';
     if(this.props.wishlist_product_detail.products.length > 0)
      {   
          product_item = this.props.wishlist_product_detail.products.map((product, index) => { 
              return (<Product product={product} key={index} count={index} />)
           })
      }
     else
      {
        product_item = <NoProduct />
      } 

    return (
     <div className="contner head_margin wishlist_head_margin side-collapse-container">
      <section id="cart_box">
      <h2 className="about_title">My Short List ({this.props.wishlist_product_detail.products.length})</h2>
       <section id="wishlist_Carousel">
        {
          this.props.wishlist_product_detail.products.length > 0?
              <div className="rootDiv">
                <GridList cellHeight={385} className="gridList">
                  {product_item}
                </GridList>
              </div>
          : <div className="wishlistPading">
              {product_item}
            </div>   
        }
       </section>
       <ScrollToTop showUnder={300} >
       <div className="scroll_top">^</div>
      </ScrollToTop>

       {this.props.wishlist_product_detail.products.length ?
       <div className="wishlist_down_btn" id="place_order_button" onClick={this.wishlist_all_clear}>Clear All</div> 
       : ''}
      </section>
      </div>
      )
  }
  else
  {
    return(<div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>)
  }
  }
}

function mapStateToProps(state){
  return {
    wishlist_product_detail: state.accountReducer.wishlist_product_detail,
    actions: bindActionCreators(wishListProduct, wishListClearAllProduct, wishlistData)
  };
}

export default connect(mapStateToProps)(Wishlist);

