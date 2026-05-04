class Slider extends React.Component {

   constructor(props)
   {
     super(props);
    this.state = {
      data: [],
      products: [],
      product_count: -1,
    };
   } 

   componentDidMount()
  {
    var w;
    var self = this;
    if(this.props.api_type == 'related')
      {
       var workerUrl = './api/product/getRelatedProducts/'+this.props.product_id+'/'+this.props.seller_id+'/'+this.props.custom_store_val;
      } 
     else
     {
      var workerUrl = './api/product/getRelatedProductsFromSeller/'+this.props.product_id+'/'+this.props.seller_id+'/'+this.props.custom_store_val;
     }

    w=new Worker("react_worker.js");
    w.postMessage({ "url": workerUrl});
    w.onmessage = function (event) {
                    var response = $.parseJSON(event.data.result); 
                    self.setState({data: response, products: response.products, product_count: response.products.length})
                    w.undefined;
                };
/*    axios({
    method:'get',
    url:url,
    responseType:'json'
    })
    .then(response => {
            this.setState({data: response.data, products: response.data.products, product_count: response.data.products.length})

     });*/
  }


  render() {
    let productsData;
    const productItemInActiveData = [];
    let self = this;
    var active_item = parseInt(this.props.active_item);

    if(active_item == 4) { var active_class = "home-product-list2"; }
    else { var active_class = "list_product_slider"; }
    

    var productItemActive = this.state.products.map(function(product, index) {
       if(index < active_item)
       {
        return (<SliderItem key={index} product={product} active_class={active_class} productSellerPopup={self.props.productSellerPopup}  is_popup={self.props.is_popup} />);
       }
      })



for(var i=active_item; i <= this.state.products.length; i=(i+active_item))
{
     var productItemInActive = this.state.products.map(function(product, index) {

        if(index >= i && index < (i*2))
        {
         return product;
        } 
      })
   productItemInActiveData.push({productItemInActive })
}

 if(productItemInActiveData.length > 0){

 if(this.state.products.length%active_item == 0) { var item_box = productItemInActiveData.length-1; }
 else { var item_box = productItemInActiveData.length; }  
  

  var productItemActiveArray = $.map(productItemInActiveData, function(value, index) {
      
        if(item_box > index)
        {
         return (<div className="item"> 
             {
               value.productItemInActive.map(function(product, index) {
                 if(product != undefined) {
                     return (<SliderItem key={index} product={product} active_class={active_class} productSellerPopup={self.props.productSellerPopup}  is_popup={self.props.is_popup} />);
                   }  
                })
             }

         </div> )
         } 
   });
}

  if(this.state.product_count > 0){   
    return (  <div className="home_category_section trending_products">
            {this.props.api_type == 'related' ?
            <div className="slider_title"><h4 className="text_line"><span>RELATED PRODUCTS</span></h4></div>
            : 
             <div className="slider_title"><h4 className="text_line"><span>MORE FROM THIS SELLER</span></h4></div>
            } 
 
             <div className="carousel slide multi-item-carousel" id={this.props.slider_id}>
              <div className="carousel-inner related_product_slider">
              <div className="item active">
                    {productItemActive}
               </div>
                 {productItemActiveArray}
              
                </div>
              </div>
              {this.state.product_count > 5 ?
              <div className="home_slider_btn">
              <a className="" href={'#'+this.props.slider_id} data-slide="prev"><i className="fa fa-angle-left" aria-hidden="true" ></i></a>
              <a className="" href={'#'+this.props.slider_id} data-slide="next"><i className="fa fa-angle-right" aria-hidden="true"></i></a>
              </div>
              : '' }
              </div>
            );
      }
    else if(this.state.product_count == 0)
      {
        return (
            <section></section>
               )
      } 
    else
     {
      return <LoadingProducts active_item={self.props.active_item} />
     }   
  }
}

