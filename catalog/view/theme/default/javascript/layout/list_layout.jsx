
class ListLayout extends React.Component {
  
  constructor(props)
   {
     super(props);
   }

  render() {

    return (
          <app>
          <Header language={this.props.header_language} hide_search={this.props.hide_price} custom_store={this.props.custom_store} international_store={this.props.international_store} search={this.props.search} />
          <List custom_store_val = {this.props.custom_store_val} csv_req={this.props.csv_req} hide_price={this.props.hide_price} international_store={this.props.international_store} language={this.props.category_language} customer_data = {this.props.customer_data} category_info={this.props.category_info} price_filter={this.props.price_filter} options={this.props.options} rating_filter={this.props.rating_filter} filters={this.props.filters} sorts={this.props.sorts} order={this.props.order} path={this.props.path} search={this.props.search} stock_filter={this.props.stock_filter} search_sale={this.props.search_sale} SITE_ENVIRONMENT={this.props.SITE_ENVIRONMENT} loc={this.props.loc} custom_title={this.props.custom_title} is_custom={this.props.is_custom} store_product={this.props.store_product} purchase_days={this.props.purchase_days} store_code={this.props.store_code} />
          <Footer language={this.props.footer_language} international_store={this.props.international_store} />
          <Otpform language={this.props.login_language} international_store={this.props.international_store} />
          <Login language={this.props.login_language} international_store={this.props.international_store} />
          <Register language={this.props.login_language} international_store={this.props.international_store} />
          <Success language={this.props.login_language} />
          </app>
    	);
  }
}
