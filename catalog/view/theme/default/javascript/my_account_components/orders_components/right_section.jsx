class RightSection extends React.Component {
	
	constructor(props){
		super(props);		
		this.state = {
      language: this.props.language,
		}
	}
  

	render(){
      return(
              <div className="right_section">
                <div className="col-sm-12 my_account_heading">
                  <div className="col-sm-3 heading_title">
                  {
                    !this.props.filter_order_type_value ? <p>{this.props.language.heading_title}</p>
                                                        : <p>{this.props.order_type_title}</p>
                  }
                  </div>
                  {
                    <SearchBoxComponent language={this.props.language} 
                                        invOrOrdNoSearchSubmit={this.props.invOrOrdNoSearchSubmit} 
                                        clearSearch={this.props.clearSearch}
                                        searched_value={this.props.searched_value}
                                        invOrOrdNoSearchChangeValue={this.props.invOrOrdNoSearchChangeValue}/>
                  }    
                </div>        
                
                <OrderListComponent language={this.props.language} 
                                    orders={this.props.order_data} 
                                    beyond_order_id={this.props.beyond_order_id} 
                                    loading={this.props.loading} 
                                    menus={this.props.menus}
                                    popular_tags={this.props.popular_tags}
                                    something_went_wrong={this.props.something_went_wrong}
                                    searched_value={this.props.searched_value}
                                    clickOnOrderDetailButton={this.props.clickOnOrderDetailButton}
                                    clickOnShowMoreOrders={this.props.clickOnShowMoreOrders}
                                    logout={this.props.logout}
                                    customer_access_token={this.props.customer_access_token}
                                    /> 
              </div>
            )
	}
}