class AccountStatementLayout extends React.Component {

	constructor(props){
		super(props);
	}

	render() {
	    return (
          <app>
          <Header custom_store={this.props.custom_store} 
      			  language={this.props.header_language} 
      			  international_store={this.props.international_store} />

          <AccountStatement language={this.props.myaccount_language} 
      				 international_store={this.props.international_store} 
      				 logout_url={this.props.logout_url} 
      				 popular_tags={this.props.popular_tags}
      				 />

          <Footer language={this.props.footer_language} 
          		  international_store={this.props.international_store} />
          </app>
    	);
	}
}