class ProductItem extends React.Component {

    constructor(props)
    {
     super(props);
     this.want_designe = this.want_designe.bind(this);
     this.state = {
        image_available: false
      }    
     
    } 

    componentDidMount()
    {
     $('[data-toggle="popover"]').popover();
    }
  
  want_designe(p)
  { 
     if(getCookie("customer_mobile") != '')
      {
         $("#design_customer_mobile_list").hide();
      }
      else
      {
        $("#design_customer_mobile_list").show();
      }
                
      $(".alert_msg").remove();
      $("#want_designe_popup").modal("show");
      if(p.props.product.product_id != '' && p.props.product.product_id > 0){
       $("#want_designe_product_id").val(p.props.product.product_id);
      }     
  }




  render() {
    var self = this;
    var image_count = this.props.product.images.length;
    var imageItem = this.props.product.images.map(function(image, index) {
        return (<li><ImageItem key={index} image={image} product_id={self.props.product.product_id} count={index} /></li>);
      })


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


 function category_prev(id)
 { 
  
  if($('#carousel-category_prev_'+id).attr('data') == 0)
  {
     var carousel = $('#carousel-category_'+id).carousel({
           vertical: true,
           group: 1,
           wrap:true
           });
     $('#carousel-category_prev_'+id).attr('data',1);
  }
   $('#carousel-category_'+id).carousel('prev');
 } 

 function category_next(id)
 {
  if($('#carousel-category_prev_'+id).attr('data') == 0)
  {
     var carousel = $('#carousel-category_'+id).carousel({
           vertical: true,
           group: 1,
           wrap:true
           });
     $('#carousel-category_prev_'+id).attr('data',1);
  }
   $('#carousel-category_'+id).carousel('next');
 } 
 function createMarkup(html) {
       return {__html: html};
     }
        return (

           <div className={this.props.list_page ? 'c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid c-product-list__item' :
                           'c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid home-product-list'
                            }>
                      <div className="c-product-card__img-placeholder">
                          {!this.props.hide_price ? 
                          <a onClick={(e) => this.props.clickHandlerPopup(e, this)}  href={this.props.product.href}  data-product_id={this.props.product.product_id} className="fancybox productInfoAjax c-product-card__img-placeholder-inner">
                            <span className="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">
                             <ListImg count={this.props.count} width={this.props.product.width} height={this.props.product.height} className="largeImage c-img-lazy__img" src={this.props.product.image} product_id={this.props.product.product_id} />
                            </span>
                          </a>
                          :
                          <a className="fancybox productInfoAjax c-product-card__img-placeholder-inner">
                            <span className="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">
                             <ListImg count={this.props.count} width={this.props.product.width} height={this.props.product.height} className="largeImage c-img-lazy__img" src={this.props.product.image} product_id={this.props.product.product_id} />
                            </span>
                          </a>
                          }
                      </div>
                      {image_count > 1 ?  
                         image_count > 5 ?
                      <div className={"c-product-card__gallery "+this.props.scroll_class}>
                        <div className="slide product_thumbs">
                            <div className="carousel-inner product_thumbs_box vertical carousel-category_vertical">
                               <ul id={'carousel-category_'+this.props.product.product_id}>
                               {imageItem}
                               </ul>
                            </div>
                            <a className="left carousel-control" id={'carousel-category_prev_'+this.props.product.product_id} data="0" onClick={()=>category_prev(this.props.product.product_id)}>
                              <i className="fa fa-chevron-up" aria-hidden="true"></i>
                            </a>
                            <a className="right carousel-control" id={'carousel-category_next_'+this.props.product.product_id}  data="0" onClick={()=>category_next(this.props.product.product_id)}>
                              <i className="fa fa-chevron-down" aria-hidden="true"></i>
                            </a>
                        </div>
                      </div>
                      : 
                      <div className={"c-product-card__gallery "+this.props.scroll_class}>
                        <div className="slide product_thumbs">
                            <div className="carousel-inner product_thumbs_box vertical carousel-category_vertical">
                               <ul id={'carousel-category_'+this.props.product.product_id}>
                               {imageItem}
                               </ul>
                            </div>
                        </div>
                      </div>
                      : ''
                       }

                       <div className="wishlist_btn_right" data-product-id={this.props.product.product_id}>
                       {!this.props.hide_price ? 
                       <button onClick={ () => wishlist_add(this.props.product.product_id, this,this.props.product) }  type="button" data-product-id={this.props.product.product_id} className="addtowishlist" id={'wishlist_'+this.props.product.product_id} data-toggle="tooltip" title="" data-original-title="Add to Wish List">
                        <i className={'fa '+this.props.product.fill_heart} id={'wishlist_heart_'+this.props.product.product_id}></i>
                       </button>
                       :
                        ''
                       }

                      </div>


                      { this.props.product.exp_dispatch_days > 0 ?
                      <div className="product_card_teg_banner">
                        <div className="card-header-opt">
                          <small>Available after {this.props.product.exp_dispatch_days} days</small>
                        </div>                                            
                      </div>
                      : ''
                       }
                     
                      {this.props.product.pickup_city != '' ?
                       <div className="product_card_city_banner">
                        <div className="card-header-opt">
                          <small>from {this.props.product.pickup_city}</small>
                        </div>                                            
                      </div>
                      : ''}

                      <div className="c-product-card__price-block">
                           
                          {!this.props.hide_price ?  

                            <div className="c-quick-buy  c-product-card__buy-button c-quick-buy_js_inited">
                              {this.props.product.stock_status == 'out of stock' ?
                               <button id="want_designe2" onClick={()=>this.want_designe(this)} type="button" className="btn btn-checkout"><i className="fa fa-shopping-cart"></i>I want this design</button>
                               : ''}

                                <a onClick={(e) => this.props.clickHandlerPopup(e, this)}  href={this.props.product.href}  data-product_id={this.props.product.product_id} className="fancybox productInfoAjax">
                                    <button className="c-quick-buy__button btn_margin c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">QUICK VIEW</button>
                                </a>
                               
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
                                <button onClick={(e) => this.props.clickHandlerPopup(e, this)}  href={this.props.product.href}  data-product_id={this.props.product.product_id}  className="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited addtocart_new">
                                    ADD TO CART
                                </button>
                              }
                            </div>
                          : 
                            ''
                           }  

                      </div>
                      <div className="c-product-card__description">
                        {!this.props.hide_price ? 
                        <a onClick={(e) => this.props.clickHandlerPopup(e, this)}  href={this.props.product.href}  data-product_id={this.props.product.product_id} className="fancybox productInfoAjax" data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.product.full_name}>{this.props.product.full_name.substr(0, 35)} </a>
                         :
                        <a className="fancybox productInfoAjax">{this.props.product.name} </a> 
                        }
                       </div>

                 <div className="blue_bg">
                   {this.props.product.is_sor_enabled ?
                   <div data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.product.sor_enabled_text}><img className="round_box" src={cdn_url+"sor_box.png"} />
                   <div className="buyback_text"><span>{this.props.product.sor_enabled_text}</span></div>
                   </div>
                   : ''}
                  </div>

                 {!this.props.hide_price ?      
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
                   : ''}
                 
                   <div className="product-card_rating">
                        {this.props.product.rating ?
                         <span className={'label '+rating_class}>{rating}</span>
                          : ''}
                         
                         {this.props.product.margin_percentage ?
                         <span className="c-product-margin"> Your margin {this.props.product.margin_percentage}%</span>
                         : ''}
                    </div>
                 

               <div className="product_card_description pd_description_hight">
                <span><a href="javascript:;" data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.product.set_description}> {this.props.product.short_set_description} </a>
                 <br />  
                 </span>
                 
              </div>
              <div className="previous_order_div">{this.props.product.previously_ordered?'Previously Ordered':''}</div>
      </div>
      )
    }
    }