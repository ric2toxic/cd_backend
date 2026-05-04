class Summary extends React.Component {
	
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
			return (
		           <div className="col-sm-12 no-padding my_account_heading">
                     <div className="col-sm-12 col-xs-12 col-md-12 col-lg-12 heading_title">
                       <p>Account Statement</p>
                    </div>

                    <div className="Total_summary_of_order">
                     <div className="col-sm-9">
                     <div className="col-sm-4 no-padding">
                      <p>No. of Orders</p>
                      <h4>{this.props.account_summary.total_order_count}</h4>
                     </div>
                     <div className="col-sm-4 no-padding">
                     <p>Total Debit</p>
                     <h4>{$('<div/>').html(this.props.account_summary.total_debits).text()}</h4>
                     </div>
                     <div className="col-sm-4 no-padding">
                     <p>Total Credit</p>
                      <h4>{$('<div/>').html(this.props.account_summary.total_credits).text()}</h4>
                     </div>
                   </div>
                 <div className="col-sm-3 no-padding" data-placement="bottom"  data-toggle="popover"  data-trigger="hover" data-content={this.props.important_note}>
   
                    <div className={this.props.account_summary.is_positive_bal ? "outstanding_bal" : "outstanding_bal negative_bal"}>
                       <p>Outstanding Balance &nbsp; <i className="fa fa-question-circle" aria-hidden="true"></i></p>
                       <h4>{$('<div/>').html(this.props.account_summary.balance).text()}</h4>
                     </div>
                 </div>
                 </div>
                </div>
			)
	}
}