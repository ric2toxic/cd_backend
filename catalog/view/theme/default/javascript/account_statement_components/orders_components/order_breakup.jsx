class OrderBreakup extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			language: this.props.language,
		}
	}


    componentDidMount()
    {
     $('[data-toggle="popover"]').popover();
    }

	render(){
	if(this.props.orders_breakup)
	{
			return (
				<section>
         <div className="col-sm-2 no-padding">
          <button className="clear_all_btn"  onClick={()=>this.props.getOrderDetailPage(0)}><i className="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
                    
         </div>
				 <div className="col-sm-10 no-padding">
                    <div className="Total_summary_of_order">
                     <div className="col-sm-9">
                     <div className="col-sm-4 no-padding">
                      <p>Order Number</p>
                      <h4>{this.props.breakup_order_no}</h4>
                 </div>
                 <div className="col-sm-4 no-padding">
                     <p>Total Debit</p>
                     <h4>{$('<div/>').html(this.props.breakup_debit).text()}</h4>
                     </div>
                     <div className="col-sm-4 no-padding">
                     <p>Total Credit</p>
                      <h4>{$('<div/>').html(this.props.breakup_credit).text()}</h4>
                     </div>
                </div>
                 <div className="col-sm-3 no-padding">
                   <div className={this.props.is_positive_bal ? "outstanding_bal" : "outstanding_bal negative_bal"}>
                    <p>Order Balance</p>
                    <h4>{$('<div/>').html(this.props.breakup_balance).text()}</h4>
                </div>
                 </div>
                 </div>
                </div>

		         <div className="all_summary_of_order">
                   <table className="table table-striped">
                    <thead>
                       <tr>
                         <th scope="col">Particulars</th>
                         <th scope="col">Doc. No.</th>
                         <th scope="col">Date</th>
                         <th scope="col">Debit Amount</th>
                         <th scope="col">Credit Amount</th>
                         <th scope="col">Running Balance</th>
                       </tr>
                      </thead>
                   <tbody>
                      {$.map(this.props.orders_breakup, function(order, index) {
                        return (<tr>
                         <td className="col-sm-2"><div data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={order.particular} className="ellipsis_text">{order.particular}</div></td>
                         <td className="col-sm-2">
                          {order.download_link?
                            <a href={order.download_link}><i className="fa fa-download"></i></a>
                          :''
                          }
                           
                         <div data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={order.doc_ref} className="ellipsis_text">{ order.doc_ref}</div>  
                         </td>
                         <td className="col-sm-2">{order.date}</td>
                         <td className="col-sm-2">{$('<div/>').html(order.debit_amount).text()}</td>
                         <td className="col-sm-2">{$('<div/>').html(order.credit_amount).text()}</td>
                         <td className="col-sm-2">{$('<div/>').html(order.running_balance).text()} <div className="txn_type">{order.txn_type}</div></td>
                       </tr>)
                     })   
                   }
                   </tbody>
		        </table>
		        </div>
		    </section>
			)
		}
		else
		{
			return( <div className="my_account_order_list">
				     <div className="col-sm-12 no-padding">
                     <div className="account_statement_btn_group">
                       <button onClick={()=>this.props.getOrderDetailPage(0)}>Back</button>
                     </div>
                     </div>
				       {this.props.something_went_wrong  ? <SomethingWentWrong searched_value={this.props.searched_value} />
			   	 												  : <RecordNotFound language={this.state.language} popular_tags={this.props.popular_tags}/> }
			   	 	</div>)
		}	
	}
}