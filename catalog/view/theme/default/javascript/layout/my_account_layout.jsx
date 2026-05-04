class MyAccountLayout extends React.Component {

	constructor(props){
		super(props);
	}

	render() {
	    return (
          <app>
          <Header custom_store={this.props.custom_store} 
      			  language={this.props.header_language} 
      			  international_store={this.props.international_store} />

          <MyAccount language={this.props.myaccount_language} 
      				 international_store={this.props.international_store} 
      				 left_menu={this.props.left_menu} 
      				 filter_order_type_value={this.props.filter_order_type_value} 
      				 order_type_title={this.props.order_type_title} 
      				 menus={this.props.menus}
      				 popular_tags={this.props.popular_tags}
      				 common_order_data={this.props.common_order_data}/>

          <Footer language={this.props.footer_language} 
          		  international_store={this.props.international_store} />
          </app>
    	);
	}
}