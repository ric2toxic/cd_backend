  class ProductCart extends React.Component { 
    constructor(props){
        super(props);
        this.state = {
           data: [],
           is_initial : 0,
           is_option : 0
        };
    } 
    
/*    componentWillMount(){
        if(this.props.options.length >= 1){ 
            this.setState({is_option:1}) 
        }
    }   */ 
  
    render() {

    function createMarkup(html) {
       return {__html: html};
     }
     
        return (
    		<div className="col-sm-5 contact_saction" id="product">
          <form>
                {!this.props.international_store ? 
                <div className="offer_detail">
                 <div className="col-sm-2 order_icon nopadding"><label><i className="fa fa-tags" aria-hidden="true"></i></label></div>
                  <div className="col-sm-10 nopadding">
                     <span><strong>2%</strong> discount on all prepaid order. </span>
                     <span> <strong>3%</strong> on 25,000 and above prepaid orders. </span>
                  </div>
                  <div className="clearfix"></div>
                </div>
                 : '' }

                <div className="payment_saction">
                   
                     {this.props.mrp > 0 ?
                    <div className="col-sm-12 nopadding">
                    <div className="saving_money">
                    <p className="col-sm-6 nopadding"><span className="mrp_title">MRP:</span> <br /><div dangerouslySetInnerHTML={createMarkup(this.props.format_mrp)} /></p>
                    <p className="col-sm-6 nopadding"><span className="mrp_title">Your Margin:</span> <br /> <div dangerouslySetInnerHTML={createMarkup(this.props.saving_money)} /></p>
                    </div>
                    </div>
                   :
                   ''
                  }



                  {this.props.special ? 
                  <div>
                    <h3 className="c-product-card__old-price" dangerouslySetInnerHTML={createMarkup(this.props.price)}></h3>   
                    <h3 dangerouslySetInnerHTML={createMarkup(this.props.special)}></h3>
                    </div>
                    : <h3 dangerouslySetInnerHTML={createMarkup(this.props.price)}></h3> 
                   }
                   <span> {this.props.text_withoutslash_piece} + GST {this.props.text_tax_rate} {/*{this.props.language.text_plus_cst} {this.props.language.text_tax_rate}*/} </span>

                </div>
                <div className="clearfix"></div>
                <IsSingleProduct 
                    language = {this.props.language} 
                    is_single = {this.props.is_single}  
                    custom_store_selling_price = {this.props.custom_store_selling_price} 
                    custom_store_product_href = {this.props.custom_store_product_href} 
                    text_tax_rate = {this.props.text_tax_rate}  
                />
                <OrderOptions minimum = {this.props.minimum} maximum = {this.props.quantity} is_option = {this.props.options.length} options = {this.props.options} api_call={this.props.api_call} is_popup = {this.props.is_popup} />


                 <div className="clearfix"></div>

                <ProductCartButton  getProductDeatil={this.props.getProductDeatil} server_error={this.props.server_error}
 product_id = {this.props.product_id} is_option = {this.props.options.length} quantity = {this.props.quantity} minimum = {this.props.minimum} stock_status = {this.props.stock_status}  language = {this.props.language} api_call={this.props.api_call}  is_popup = {this.props.is_popup} />

                <div className="clearfix"></div>
                <ShipRetrnPolicyDropDown international_store={this.props.international_store} language = {this.props.language} seller_returnable = {this.props.seller_returnable} />
            </form>    
            </div>
    	)
	}
}
class OrderOptions extends React.Component { 
    constructor(props){
        super(props);
            
    } 
    render() {  
         var blur_class;
         if(!this.props.api_call) { blur_class = 'blur'; } else { blur_class = ''; }
       return ( 
            this.props.options.length==0?( 
                <div  className={'set_quantity '+blur_class}>
                    <label>Set Quantity :</label>
                    <SetQuantity  minimum = {this.props.minimum} maximum = {this.props.maximum} /> 
                </div>
            ):(
                <SetQuantityBox is_popup = {this.props.is_popup} api_call={this.props.api_call} minimum = {this.props.minimum} is_option = {this.props.options.length} options = {this.props.options} />
            )
        )
    }    
}


class ProductCartButton extends React.Component { 
    constructor(props){
        super(props);
        this.state = {
           //is_stock : 1
        };
        this.addToCart = this.addToCart.bind(this);
    } 

    componentDidMount(prevProps, prevState) 
    {
        var customer_mobile = getCookie("customer_mobile");
        var register_user = getCookie("register_user")
        var customer_id = getCookie("customer_id")

        $('#want_designe').click(function(){ 

            /*if(getCookie("customer_mobile") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;     
                
            }
            else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;
            }
            else
            {
                $(".alert_msg").remove(); 
                $("#want_designe_popup").modal("show"); 
            }*/
            

            if(getCookie("customer_mobile") != '')
            {
              $("#design_customer_mobile").hide();
            }
           else
            {
              $("#design_customer_mobile").show();
            }

            $(".alert_msg").remove(); 
            $("#want_designe_popup").modal("show");

        });
        
    }
    
    addToCart(e, product_id, button){
        var product_id = this.props.product_id;
        var order_set_quantity = $('#order_set_quantity').val();
        if(this.props.is_option){
            cart_add_with_option(product_id, order_set_quantity)
        }else{
            cart_add(product_id, order_set_quantity, 0)   
        } 

        
    }

   
    render() {  

         if(this.props.server_error)
         {
           return (<ApiError retry={this.props.getProductDeatil} />)
         } 
         else
         {  
            return ( 
            this.props.stock_status == 'out of stock'?(
                    <div>
                        <div className="btn_addtocart col-sm-12 nopadding"> 
                            <button id="want_designe" type="button" data-loading-text="Loading..." className="btn"><i className="fa fa-shopping-cart"></i> I want this design</button>
                        </div>
                        

                        <div className="if_out_of_stock col-sm-12 nopadding">
                            <div className="detail_out_of_stock col-sm-12 ">OUT OF STOCK</div>
                        </div>
                    </div>
                ):( 
                    <div className="btn_addtocart col-sm-12 nopadding">  
                        <input type="hidden" name="product_id" value={this.props.product_id} />
                        <input type="hidden" name="for_detail_page" id="for_detail_page" value="0" /> 
                        {
                            this.props.api_call ?
                        <button onClick={this.addToCart.bind()}  type="button" id="detail-button-cart" data-product-id={this.props.product_id} data-loading-text="Loading..." className="btn btn-checkout">ADD TO CART</button>
                        :
                         <button type="button" id="detail-button-cart" data-product-id={this.props.product_id} data-loading-text="Loading..." className="btn btn-checkout">Please wait &nbsp; <i className="fa fa-circle-o-notch fa-spin"></i></button>
                        }
                         <div id="cart_added" className="alert alert-info"> </div>  
                        
                    </div>
                )
        )
       } 
    }
}



class IsSingleProduct extends React.Component {  
    constructor(props){
        super(props);
        this.state = {
           is_initial : 0
        };
    }   

    componentWillMount(){

        {/*
        this.props.custom_store_selling_price = '1500';
        this.props.custom_store_product_href = '#';
        */}
    
        if(this.props.custom_store_selling_price != '' && this.props.custom_store_product_href != ''){ 
            this.setState({is_initial:1}) 
        } 
    }   

    render() { 

        return (
            this.state.is_initial==0?(
                <div></div>
            ):( 
            <div className= "col-sm-12 store">    
                <SingleProduct 
                    language = {this.props.language} 
                    is_single = {this.props.is_single} 
                    custom_store_selling_price = {this.props.custom_store_selling_price} 
                    custom_store_product_href = {this.props.custom_store_product_href}
                    text_tax_rate = {this.props.text_tax_rate}  
                />
            </div>
            )
        )
    }
}
class SingleProduct extends React.Component { 
    constructor(props){
        super(props);   
    } 
    render() { 
        function createMarkup(html) {
       return {__html: html};
     }
        return (
            this.props.is_single==0?( 
                <div>
                    <div className="store_information">
                    <h2 className="store_title"><a href={this.props.custom_store_product_href} >Single piece is also available</a></h2>
                    <p className="store_price" dangerouslySetInnerHTML={createMarkup(this.props.custom_store_selling_price)}></p> 
                    <span className="store_tax">{/* this.props.language.text_withoutslash_piece */} Per Piece + GST {this.props.text_tax_rate}</span>
                    <div className="clearfix"></div>                  
                    </div>
                </div>
            ):( 
            <div>
                
                <div className="store_information">
                    <h2 className="store_title"><a href={this.props.custom_store_product_href} >WholeSale set also available</a></h2>
                    <p className="store_price" dangerouslySetInnerHTML={createMarkup(this.props.custom_store_selling_price)}></p> 
                    <span className="store_tax">{/* this.props.language.text_withoutslash_piece */} Per Piece + GST {this.props.text_tax_rate}</span>
                    <div className="clearfix"></div>
                </div> 
            </div>
            )
        )
    }
   
 

}
