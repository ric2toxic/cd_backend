class ReturnsLayout extends React.Component {

	constructor(props){
		super(props);
	}

	render() {
	    return (
          <app>
          <Header custom_store={this.props.custom_store} 
      			  language={this.props.header_language} 
      			  international_store={this.props.international_store} />

          <Returns language={this.props.myaccount_language} 
      				 international_store={this.props.international_store} 
      				 logout={this.props.logout_url} 
               master_return_id={this.props.master_return_id}
               order_no={this.props.order_no}
      				 />

          <Footer language={this.props.footer_language} 
          		  international_store={this.props.international_store} />
          </app>
    	);
	}
}