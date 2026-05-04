import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import {GridList} from 'material-ui/GridList'
import NoProduct from '../../loading/no_product'
import { userloginData } from '../../../actions/HeaderAction';
import Product from './product'
import  './index.css'
import ScrollToTop from 'react-scroll-up';


class RecentView  extends Component {
  
  componentDidMount()
   {
    this.props.dispatch(userloginData());
   }

  render(){

  let recentViewProduct="recentViewProduct";
  let product_item;
  let previousProduct;

  if(window.location.href.indexOf("/staging") > -1) 
    {
      recentViewProduct="staging_recentViewProduct";
    }
    
   if(localStorage.getItem(recentViewProduct))
   {
     previousProduct = JSON.parse(localStorage.getItem(recentViewProduct));
     product_item = previousProduct.map((product, index) => {
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
      <h2 className="about_title">Recently Viewed</h2>
       <section id="wishlist_Carousel">
        {previousProduct && previousProduct.length > 0?
              <div className="rootDiv">
                <GridList cellHeight={290} className="gridList">
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
      </section>
      </div>
      )
  
  }
}

function mapStateToProps(state){
  return {
    actions: bindActionCreators(userloginData)
  };
}
export default connect(mapStateToProps)(RecentView);

