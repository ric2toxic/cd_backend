class ProductInfo extends React.Component {
    
    constructor(props){
        super(props);
        this.state = {
           data: [],
           is_initial : 0
        };
          
    }

    render() { 
        return (
            <div className="col-sm-7 padding_left_right product_db_section">
                <ProductInfoTitle heading_title = {this.props.heading_title} 
                model = {this.props.model} 
                hsn_code = {this.props.hsn_code} 
                previously_ordered={this.props.previously_ordered}
                is_sor_enabled = {this.props.is_sor_enabled}
                sor_enabled_text = {this.props.sor_enabled_text}
                sor_enabled_detail_text = {this.props.sor_enabled_detail_text}
                />
                <ProductRateDes 
                    language = {this.props.language} 
                    product_id = {this.props.product_id}
                    product_quantity =  {this.props.product_quantity}
                    product_price_value =  {this.props.product_price_value}
                    product_piece_in_set =  {this.props.product_piece_in_set}
                    seller_id =  {this.props.seller_id}
                    category_ids =  {this.props.category_ids}
                    customer_data = {this.props.customer_data}   
                    rating = {this.props.rating}    
                    fill_heart = {this.props.fill_heart}     
                    minimum = {this.props.minimum}
                    set_description = {this.props.set_description}
                    options = {this.props.options} 
                    description = {this.props.description} 
                    tags = {this.props.tags} 
                    discounts = {this.props.discounts} 
                    filters = {this.props.filters}
                    seller_returnable = {this.props.seller_returnable}
                    exp_dispatch_days = {this.props.exp_dispatch_days}   
                />
            </div>
        )
    }
}
