class RightSection extends React.Component {
	
	constructor(props){
		super(props);		
		this.state = {
         language: this.props.language,
		}

	}

	render(){
    var self = this;
    if(!this.props.loading)
    {
      return(<div className="right_section">
                <div className="row">
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-11 heading_title">
                      My Wishlist
                    </div>
                    </div>
                </div>

                 {this.props.wishlist_data && this.props.wishlist_data.products.length > 0 ? 
                 this.props.wishlist_data.products.map(function(product, index) {
                
                 return(<div className="col-sm-4">
                  <div className="address_card" style={{height:'445px'}}>
                     <div className="delete_icon">
                      <a href="javascript:;" onClick={()=>self.props.remove(product.product_id)}><i className="fa fa-trash" aria-hidden="true"></i></a>
                     </div>

                     <div className="c-product-card__img-placeholder">
                       <a href={product.href} className="c-product-card__img-placeholder-inner">
                       <span className="c-product-card__img wishlist_image">
                         <SlideImg src={product.image} class="" />
                        </span>
                      </a>  
                     </div>

                     <div className="shop_address">
                        <p style={{textAlign:'center', fontSize:'14px'}}> 
                        {product.name} <br />
                        <span dangerouslySetInnerHTML={{ __html: product.price }} />
                        </p>
                     </div>   
                       

                      <div className="address_edit_btn col-sm-12">
                      {product.stock_status == 'out of stock' ?
                        <button><i className="fa fa-hand-paper-o" aria-hidden="true"></i> OUT OF STOCK</button>
                       :
                        product.options.length > 0 ?
                        <a href={product.href}>
                        <button><i className="fa fa-cart-plus" aria-hidden="true"></i> ADD TO CART</button>
                        </a>
                        :
                        <button onClick={ () => cart_add(product.product_id, product.minimum) } ><i className="fa fa-cart-plus" aria-hidden="true"></i> ADD TO CART</button>
                        }
                      </div>
                </div>
                </div>) }) : 

                <div className="row not_record_found">
                        <div className="col-sm-12 text-center">
                        <p><img src={cdn_url+"dekstopwithkurti.png"} width="40%" height="20%" /></p>
                        <h4> No Matching Products Found! </h4>
                        </div>
                </div>

                }

              </div>)
    }
    else
    {
     return(<div className="right_section">
                 <OrderListLoadingRight />
            </div>
            ) 
    }
	}
}