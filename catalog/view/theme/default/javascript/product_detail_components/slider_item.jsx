
class SliderItem extends React.Component {

   constructor(props)
   {
     super(props);
     this.state = {
        image_available: false
      }
   } 

render() {

  let rating;
  let rating_class; 

  if(this.props.product.rating)
  {
    
    if(this.props.product.rating == 5) 
     {
       rating = 'Excellent Quality';
       rating_class = 'label-success';
     }
     else if(this.props.product.rating == 4)
     {
       rating = 'Good Quality';
       rating_class = 'label-warning';   
     }
     else if(this.props.product.rating <= 3)
     {
       rating = 'Average Quality';
       rating_class = 'label-danger'; 
     }
  }
function createMarkup(html) {
       return {__html: html};
     }

   	return (
           <div className={this.props.list_page ? 'c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid c-product-list__item' :
                           'c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid '+this.props.active_class
                            }>
                      <div className="c-product-card__img-placeholder">
                      {this.props.is_popup ? 
                          <a onClick={(e) => this.props.productSellerPopup(e, this)} href={this.props.product.href} className="c-product-card__img-placeholder-inner" target="_blank">
                            <span className="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">
                             <SlideImg width={this.props.product.img_releted_width} height={this.props.product.img_releted_height} class="largeImage c-img-lazy__img" src={this.props.product.image} main_image={this.props.product.original} />
                            </span>
                         </a>
                        :
                        <a href={this.props.product.href} className="c-product-card__img-placeholder-inner">
                            <span className="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">
                             <SlideImg width={this.props.product.img_releted_width} height={this.props.product.img_releted_height} class="largeImage c-img-lazy__img" src={this.props.product.image} main_image={this.props.product.original} />
                            </span>
                         </a>
                        } 
                      </div>
      
                       <div className="wishlist_btn_right" data-product-id={this.props.product.product_id}>
                       <button onClick={ () => wishlist_add(this.props.product.product_id, this, this.props.product) }  type="button" data-product-id={this.props.product.product_id} className="addtowishlist" id={'wishlist_'+this.props.product.product_id} data-toggle="tooltip" title="" data-original-title="Add to Wish List">
                        <i className=" fa fa-heart-o" id={'wishlist_heart_'+this.props.product.product_id}></i>
                      </button>
                      </div>


                      { this.props.product.real_pic ?
                      <div className="product_card_teg_banner">
                        <div className="card-header-opt">
                          <small>Real Pic available</small>
                        </div>                                            
                      </div>
                      : ''
                       }

                      <div className="c-product-card__price-block">
                          <div className="c-quick-buy  c-product-card__buy-button c-quick-buy_js_inited">
                           {this.props.is_popup ? 
                           <a onClick={(e) => this.props.productSellerPopup(e, this)}  href={this.props.product.href}  data-product-id={this.props.product.product_id} target="_blank" className="ellipsis">
                              <button className="c-quick-buy__button btn_margin c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">DETAIL VIEW</button>
                          </a>
                           : 
                          <a href={this.props.product.href}  data-product-id={this.props.product.product_id} className="ellipsis">
                              <button className="c-quick-buy__button btn_margin c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">DETAIL VIEW</button>
                          </a> 

                           }

                          {this.props.product.stock_status == 'out of stock' ?

                            <button className="c-quick-buy__button c-button c-button_size_big out_of_stock" id={'add_to_cart'+this.props.product.product_id}>
                            Out Of Stock
                            </button>
                          :
                           this.props.product.options.length == 0 ?
                            <button onClick={ () => cart_add(this.props.product.product_id, this.props.product.minimum) }  className="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited addtocart_new" id={'add_to_cart'+this.props.product.product_id}>
                            ADD TO CART
                            </button>
                           :
                          this.props.is_popup ?
                           <a href={this.props.product.href}  data-product-id={this.props.product.product_id}  className="ellipsis"  target="_blank">
                            <button  className="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">
                            ADD TO CART
                             </button>  
                            </a>  
                          :
                           <a href={this.props.product.href}  data-product-id={this.props.product.product_id}  className="ellipsis">
                            <button  className="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">
                            ADD TO CART
                             </button>  
                            </a>
                          }  


                          </div>
                      </div>
                      <div className="c-product-card__description">
                       {this.props.is_popup ?    
                        <a onClick={(e) => this.props.productSellerPopup(e, this)} href={this.props.product.href} target="_blank">{this.props.product.name} </a>
                        :
                        <a href={this.props.product.href}>{this.props.product.name} </a>
                      }

                       </div>

                   <div className="blue_bg">
                   {this.props.product.is_sor_enabled ?
                   <div ><img className="round_box" src={cdn_url+"sor_box.png"} />
                   <div className="buyback_text"><span>{this.props.product.sor_enabled_text}</span></div>
                   </div>
                   : ''}
                  </div>

                 <div className="c-product-card__price">
                   {this.props.product.special ? 
                     <div className="c-product-card__old-price" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></div>
                     : ''
                   }
                    
                    {this.props.product.percent_discount ? 
                      <span className="c-product-card__discount"> - {this.props.product.percent_discount}%</span>
                      : ''
                    }
                    
                    {this.props.product.special ? 
                     <span className="c-product-card__price-final" dangerouslySetInnerHTML={createMarkup(this.props.product.special)}></span>
                     : <span className="c-product-card__price-final" dangerouslySetInnerHTML={createMarkup(this.props.product.price)}></span>
                   }
                    
                      
                 </div>
                 
                       <div className="product-card_rating">
                        {this.props.product.rating ?
                         <span className={'label '+rating_class}>{rating}</span>
                          : ''}
                         
                         {this.props.product.margin_percentage ?
                         <span className="c-product-margin"> Your margin {this.props.product.margin_percentage}%</span>
                         : ''}
                       </div>  

               <div className="product_card_description">
                <span>{this.props.product.set_description} </span>
              </div>
      </div>
   		)
    }
   	}
