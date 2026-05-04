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
                {!this.props.loading ?
                  !this.props.orders_breakup_page ? 
                  <div className="account_statement">
                  
                    {this.props.account_summary ?                       
                        <Summary language={this.props.language}  
                          account_summary={this.props.account_summary} 
                          something_went_wrong={this.props.something_went_wrong}
                          important_note={this.props.important_note}
                          />
                     :''}          


                   {this.props.payment_methods ?
                    <SearchBoxComponent language={this.props.language} 
                                        filter_search={this.props.filter_search} 
                                        clearSearch={this.props.clearSearch} 
                                        searchChangeValue={this.props.searchChangeValue} 
                                        searchChangeDateValue={this.props.searchChangeDateValue} 
                                        getStatementList={this.props.getStatementList}
                                        payment_methods={this.props.payment_methods} />
                     : ''}                   
               
                       
                       <OrderList language={this.props.language} 
                                    orders={this.props.orders}
                                    popular_tags={this.props.popular_tags}
                                    something_went_wrong={this.props.something_went_wrong}
                                    showMoreOrders={this.props.showMoreOrders}
                                    beyond_order_id={this.props.beyond_order_id}
                                    getOrderDetailPage={this.props.getOrderDetailPage}
                                    />
                  </div>
                  :
                   <OrderBreakup language={this.props.language} 
                                    orders_breakup={this.props.orders_breakup}
                                    popular_tags={this.props.popular_tags}
                                    breakup_order_no={this.props.breakup_order_no}
                                    something_went_wrong={this.props.something_went_wrong}
                                    getOrderDetailPage={this.props.getOrderDetailPage}
                                    breakup_debit={this.props.breakup_debit}
                                    breakup_credit={this.props.breakup_credit}
                                    breakup_balance={this.props.breakup_balance}
                                    is_positive_bal={this.props.breakup_is_positive_bal}
                                    />

                 : 
                  <OrderListLoadingRight />
                 }  
              </div>
            )
	}
}