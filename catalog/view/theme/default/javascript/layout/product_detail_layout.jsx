
class ProductDetailLayout extends React.Component {
    constructor(props)
   {
     super(props);
   }

  render() {    
    return (
          <app>
          <Header custom_store={this.props.custom_store} language={this.props.header_language} international_store={this.props.international_store} home_page={0} />
          <ProductDetail custom_store_val = {this.props.custom_store_val} product_id = {this.props.product_id} customer_data = {this.props.customer_data} SITE_ENVIRONMENT={this.props.SITE_ENVIRONMENT} international_store={this.props.international_store} />
          <Footer language={this.props.footer_language} international_store={this.props.international_store} />
          <Otpform language={this.props.login_language} international_store={this.props.international_store} />
          <Login language={this.props.login_language} international_store={this.props.international_store} />
          <Register language={this.props.login_language} international_store={this.props.international_store} />
          <Success language={this.props.login_language} />
          </app>
    	);
  }
}

