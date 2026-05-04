class PaymentModeInfoComponent extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){
		return(
				<div className="payment_mode_info_block">
					<ul>
						<li>
							<span>{this.props.language.label_payment_mode} </span>
							<span> {this.props.payment_mode_info.payment_mode} </span>
						</li>
						{	

							$.map(this.props.upi_details,function(upi_detail, index){
								return (<li>
											<span>{index} : </span>
											<span className="bold_content"> {upi_detail}</span>
										</li>
										)
							})
						}
						
					</ul>

					<table className="table table-bordered">
						<caption><label>{this.props.language.label_bank_details}</label></caption>
						<tbody>
							{
								$.map(this.props.bank_details,function(bank_details, index){
									return (<tr>
												<td width="30%">{index}</td>
												<td className="bold_content"><div dangerouslySetInnerHTML={{__html: bank_details}} /></td>
											</tr>
											)
								})
							}
							
						</tbody>
					</table>	
				</div>
			)
	}
}