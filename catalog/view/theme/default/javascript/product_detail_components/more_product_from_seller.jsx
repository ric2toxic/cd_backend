class MoreProductFromSellerSlider extends React.Component {
    
    constructor(props){
        super(props);
        this.state = {
           data: [],
           products: [], 
           is_initial : 0
        };
    } 

    componentWillMount(){
        //alert('componentWillMount');
        axios({
            method:'get',
            url:'api/product/getRelatedProductsFromSeller/'+this.props.product_id,
            responseType:'json'
            })
           .then(response => {
                    this.setState({data: response.data, products: response.data.products, is_initial:1})
        });
    }

    componentDidMount(){
        //alert('componentDidMount');
    }

    componentWillUpdate()
    {   
        //alert('componentWillUpdate');
    }

    componentDidUpdate(){
        //alert('componentDidUpdate');
        $(".regular").slick({
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 5,
            slidesToScroll: 5,
            responsive: [
                {
                  breakpoint: 1300,
                  settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    infinite: true,
                  }
                },
                {
                  breakpoint: 1200,
                  settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    infinite: true,
                  }
                },
                {
                  breakpoint: 1024,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                  }
                },
                {
                  breakpoint: 600,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                  }
                },
                {
                  breakpoint: 480,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                }
            ]
        }); 
    }

    render() {
            
            var moreProductSeller = this.state.products.map(function(product, index) {
                return(<div key={index} className="home_categories_product index">
                        <WishlistForDetailPage product_id = {product.product_id} fill_heart = {product.fill_heart} />
                        <a href={product.href}>       
                            <img src={product.image_medium} alt={product.name} title={product.name} className="img-responsive" />
                        </a>

                        {/*<a href="#1">
                            <img src={cdn_url+"cache/catalog/056_ST/5013b-800x1200.jpg"} alt={product.name} title={product.name} className="img-responsive" />
                        </a>*/}
                        <h6><a href={product.href}>{product.name}</a></h6>
                        <p>{product.price} / Piece</p>
                        <ProductRating rating = {product.rating}/>
                        <div className="product_description_slider white">
                            <span className="product_set_description color_box">{product.set_description}</span>
                        </div>
                        <div className="clear"></div>
                        <div className="pd_description">
                            <div className="col-sm-6 nopadding pd_detail_slider_btn"><a href="">VIEW </a></div>
                            <div className="col-sm-6 nopadding pd_detail_slider_btn"><a href="">CART</a></div>
                        </div>
                    </div>
                );
            })

            

            return (
                    this.state.is_initial==0?(
                        <div></div>
                    ):(
                        <div>
                            <section className="col-sm-12">
                                <div className="slider_title"><h4 className="text_line">
                                    <span>MORE FROM THIS SELLER</span></h4>
                                </div>
                                <section className="regular col-sm-12 related_product_slider" id="related_products"> 
                                    {moreProductSeller}
                                </section>
                            </section>
                            <div className="clearfix"></div>
                        </div>
                    )
              
        );
    }
}
