class Slider extends React.Component {

   constructor(props)
   {
     super(props);
    this.state = {
      data: [],
      products: [],
      products_count:'-1'
    };
   } 

   componentDidMount()
  {
    var preferences='';
    preferences = getCookie('preferences');
    var custom_store_val = this.props.custom_store_val;

    if(this.props.category_id == 'latest')
    {
      var url = './api/home/latest_product&preferences='+preferences+'&custom_store='+custom_store_val;
    }
    else if(this.props.category_id == 'trending')
    {
      var url = './api/home/trending_product&preferences='+preferences+'&custom_store='+custom_store_val; 
    }
    else
    {
      var url = './api/home/category_product&category_id='+this.props.category_id;
    }

    axios({
    method:'get',
    url:url,
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data, products: response.data.data.products, products_count:response.data.data.products.length});

   });

  }

  render() {
    let productsData;
    const productItemInActiveData = [];
    let self = this;
    var active_item = parseInt(this.props.active_item);

    if(active_item == 4) { var active_class = "home-product-list2"; }
    else { var active_class = "home-product-list"; }

    var productItemActive = this.state.products.map(function(product, index) {
       if(index < active_item)
       {
        return (<SliderItem key={index} product={product} active_class={active_class} logged={self.props.logged}  />);
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
                     return (<SliderItem key={index} product={product} active_class={active_class}  />);
                   }  
                })
             }

         </div> )
         } 
   });
}

  if(this.state.products_count > 0){   
    
     if(this.props.category_id == 'trending') { var trending_products_cls = 'trending_products'; }
      else { var trending_products_cls = ''; }
      
    return (<div className={"home_category_section "+trending_products_cls}>

              {this.props.category_id == 'trending' ?
              <h2 className="page_title home_title"><span>Trending Products</span></h2>
              :
              <h2 className="page_title home_title"><span>New Arrivals</span></h2>
              }

              <div className="clearfix"></div>
             <div className="carousel slide multi-item-carousel" id={'category_products'+this.props.category_id}>
              <div className="carousel-inner related_product_slider">
              <div className="item active">
                    {productItemActive}
               </div>
                   {productItemActiveArray}
                </div>
              </div>
              <div className="home_slider_btn">
              <a className="" href={'#category_products'+this.props.category_id} data-slide="prev"><i className="fa fa-angle-left" aria-hidden="true" ></i></a>
              <a className="" href={'#category_products'+this.props.category_id} data-slide="next"><i className="fa fa-angle-right" aria-hidden="true"></i></a>
              </div>
              </div>
            );
      }
  else if(this.state.products_count == '-1'){   
    return (<LoadingProducts active_item={self.props.active_item} />);
      }      
    else 
      {
             if(this.props.category_id == 'trending') { var trending_products_cls = 'trending_products'; }
      else { var trending_products_cls = ''; }

        return (<section className={trending_products_cls}></section>)
      }  
  }
}

