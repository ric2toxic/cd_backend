class RelatedProductSlider extends React.Component {

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
            url:'api/product/getRelatedProducts/'+this.props.product_id,
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
        $(".regular_related").slick({
            dots: false,
            infinite: true,
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
            
            var relatedProduct = this.state.products.map(function(product, index) {
                return(<div key={index} className="home_categories_product index">
                        <WishlistForDetailPage product_id = {'product.product_id'} price_value = {product.price_value} fill_heart = {product.fill_heart} piece_in_set = {product.piece_in_set} category_id = {product.category_id} seller_id = {product.seller_id}/>
                        <a href={product.href}>
                            <img src={product.image_medium} alt={product.name} title={product.name} className="img-responsive" />
                        </a>
                        
                        {/*<a href="#1">
                            <img src={cdn_url+"cache/catalog/056_ST/5012b-800x1200.jpg"} alt={product.name} title={product.name} className="img-responsive" />
                        </a>*/}
                        <h6><a href={product.href}>{product.name}</a></h6>
                        <p>{product.price} / Piece</p>
                        <ProductRating rating = {product.rating}/>
                        <div className="product_description_slider white">
                            <span className="product_set_description color_box">{product.set_description}</span>
                        </div>
                        <div className="clear"></div>
                        <div className="pd_description">
                            <div className="col-sm-6 nopadding pd_detail_slider_btn"><a href="">QUICK VIEW </a></div>
                            <div className="col-sm-6 nopadding pd_detail_slider_btn"><a href="">ADD TO CART</a></div>
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
                                <span>{this.props.language.text_related} </span></h4>
                            </div>
                            <section className="regular_related col-sm-12 related_product_slider" id="related_products">  
                                {relatedProduct}
                            </section>
                        </section>
                        <div className="clearfix"></div>
                        </div>
                    )
              
        );
    }
}
