class ProductRateDes extends React.Component {

    constructor(props) {
    super(props);
        this.state = {
            tags: [],
            is_initial : 0
        };


    }


  
    render() {
         
        var tags = this.props.tags.map(function(tag, i){
                return (
                <li key={i}><a href={tag.href}>{tag.tag}</a></li>
                );
        });

        var filters = this.props.filters.map(function(filter, i){
                return (
                    <li key={i}>{filter.group_name}: {filter.filter_name}</li> 
                );
        });


        var options = this.props.options.map(function(option, i){
                return (<p key={i}>{option.name}: <OptionValue key={i} optionsval = {option.product_option_value} optionsname = {option.name}/></p>
                    
                );
        });
       
        
      return (
        <section className="rating_box">
                  
                 <ProductRating rating = {this.props.rating}/> 
                
                <div className="col-sm-2 detail_wishlist_btn">
                    <div className="button_add_wishlist pull-right" data-product-id={this.props.product_id}>
                        <WishlistForDetailPage product_id = {this.props.product_id} fill_heart = {this.props.fill_heart} product_quantity = '1' price_value = {this.props.product_price_value} piece_in_set = {this.props.product_piece_in_set} seller_id = {this.props.seller_id}  category_id = {this.props.category_ids} />
                    </div>    
                </div>

                 <div className="clearfix"></div>
                 {this.props.seller_returnable == true ?
                  <div className="red_text"><strong>This item is non-returnable</strong></div>
                 : ''
                 }
                
                <div className="col-lg-12 nopadding">
                  
                  { this.props.exp_dispatch_days > 0 ?
                      <div className="card-header-opt">
                      <br />
                          <span className="green_text">Available after {this.props.exp_dispatch_days} days</span>
                        </div>   
                      : ''
                       }

                  <h3 className="product_description">Description</h3>

                  <section className="pt_description_contant">
                    <span className="minimum-order">Minimum Order: {this.props.minimum} Set</span>
                    
                    <p>{this.props.set_description}</p>
                    {options}
                    <p dangerouslySetInnerHTML={{__html:this.props.description}} ></p>   
                    
                    <div className="description_tag_line">
                        <ul>{filters}</ul>
                    </div>
                    
                    <div className="clearfix"></div>
                  </section>
                    

                </div>
                
                <div className="col-sm-12 nopadding">
                   <p></p> 
                  <ProductDiscount discounts = {this.props.discounts}/> 
                </div>

                {/*
                <div className="col-sm-12 nopadding">
                   <p></p> 
                  <ProductFilter filters = {this.props.filters}/>    
                </div>
                */}

                <div className="clearfix"></div>
                <div className="col-sm-12 nopadding">
                    <ProductAskQuesPopup 
                        language = {this.props.language}
                        product_id = {this.props.product_id} 
                        customer_data = {this.props.customer_data} 
                    />            
                  
                
                </div>
              </section>
      )
  }
}

class OptionValue extends React.Component {
    constructor(props) {
    super(props);
        this.state = {
            is_initial : 0
        };
    }

    render() {

        if(this.props.optionsname !== 'Color'){ 
            var optionsVal = this.props.optionsval.map(function(optval, k){
                    return (<span key={k}>{optval.name}, </span>);
            });
        }else{

            
            var optionsVal = this.props.optionsval.map(function(optval, k){

                var colorStyle = {
                    background: optval.name
                }
                return (<span key={k} className={'color_code ' + optval.name}></span>);
            });
        }

        
            
        return (<span>{optionsVal}</span>

        )
    }
}
