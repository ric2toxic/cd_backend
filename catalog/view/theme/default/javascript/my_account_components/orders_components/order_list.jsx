class OrderList extends React.Component {
	
	constructor(props){
		super(props);		
	}
  

	render(){
		return (
			<div className="order_list_block">
				<div className="col-sm-3 customer_account_section">
		     		<LeftSection 
		     				left_menu={this.props.left_menu} 
		     				language={this.props.language} 
		     				clickOnFilterOrderType={this.props.clickOnFilterOrderType}
		     				order_type_title={this.props.order_type_title}
		     				default_ord_type_title={this.props.default_ord_type_title}/>
			   	</div>
			   	<div className="col-sm-9 my_account_page">
			   		<RightSection 
			   			language={this.props.language} 
			   			order_data={this.props.order_data}
			   			beyond_order_id={this.props.beyond_order_id}
			   			invOrOrdNoSearchSubmit={this.props.invOrOrdNoSearchSubmit}
			   			invOrOrdNoSearchChangeValue={this.props.invOrOrdNoSearchChangeValue}
			   			searched_value={this.props.searched_value}
			   			clearSearch={this.props.clearSearch}
			   			loading={this.props.loading}
			   			filter_order_type_value={this.props.filter_order_type_value}
			   			order_type_title={this.props.order_type_title}
			   			menus={this.props.menus}
			   			popular_tags={this.props.popular_tags}
			   			something_went_wrong={this.props.something_went_wrong}
			   			clickOnShowMoreOrders={this.props.clickOnShowMoreOrders}
			   			order_id={this.props.order_id}
			   			suborder_id={this.props.suborder_id}
			   			clickOnOrderDetailButton={this.props.clickOnOrderDetailButton}
			   			logout={this.props.logout}
			   			customer_access_token={this.props.customer_access_token}
			   		/>
			   	</div>
			</div>
	    )
	}
}