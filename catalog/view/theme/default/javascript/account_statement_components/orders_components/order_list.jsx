class OrderList extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			language: this.props.language,
		}
	}

	render(){
		let self = this;
			return (
		         <div className="my_account_order_list all_summary_of_order">
		         {this.props.orders ? 
                   <table className="table table-striped">
                    <thead>
                       <tr>
                         <th scope="col">Order No</th>
                         <th scope="col" style={{textAlign:'center'}}>Order Date</th>
                         <th scope="col" style={{textAlign:'center'}}>Total Debit</th>
                         <th scope="col" style={{textAlign:'center'}}>Total Credit</th>
                         <th scope="col" style={{textAlign:'center'}}>Order Balance</th>
                         <th scope="col" style={{textAlign:'center'}}>Action</th>
                       </tr>
                      </thead>
                      <tbody>
                      {$.map(this.props.orders, function(order, index) {
                        return (<tr>
                         <td>{order.order_no}</td>
                         <td style={{textAlign:'center'}}>{order.ordered_date}</td>
                         <td style={{textAlign:'center'}}>{$('<div/>').html(order.total_debits).text()}</td>
                         <td style={{textAlign:'center'}}>{$('<div/>').html(order.total_credits).text()}</td>
                         <td style={{textAlign:'center'}}>{$('<div/>').html(order.balance).text()} {order.txn_type}</td>
                         <td style={{textAlign:'right'}}>
                         <div className="action_btn_group">
                         <button onClick={()=>self.props.getOrderDetailPage(order.order_id, order.order_no)}>Breakup</button>
                         <a target="_blank" href={order.detail_page_link}>Detail</a>
                        </div>
                        </td>
                       </tr>)
                     })   
                   }
		          </tbody>
		        </table>
		        :
		         ( self.props.something_went_wrong ) ? <SomethingWentWrong searched_value={this.props.searched_value} />
			   	 												  : <RecordNotFound language={this.state.language} popular_tags={self.props.popular_tags}/>
		        }

		        
		            <div className="col-sm-12">
		               {(this.props.orders != '' && this.props.beyond_order_id) ? 	
						<div className="text-center">
						<span className="btn btn-sm white_color blue_bg_color show_more_orders" onClick={this.props.showMoreOrders}>
							{self.state.language.text_show_more_order}
						</span>
						</div> 
						: ''}

						{(this.props.orders != '' && !this.props.beyond_order_id) ? 	 
						<div className="end_page" >
							No more orders found. Please change/remove Filters (if applied already), to get more order(s).
						</div> 
						: ''}
					</div>
		        </div>
			)
	}
}