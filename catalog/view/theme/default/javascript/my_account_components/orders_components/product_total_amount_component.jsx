class ProductTotalAmountComponent extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){
		return(
				<div className="product_total_amount_block">
					<table className="table table-bordered">
						<tbody>
							{
								$.map(this.props.total_breakup_data,function(total_breakup,index){
									return (
												<tr className={index.toString().toLowerCase() === 'net_amount' ? "bold_content" : ''}>
													<td>{total_breakup.title}</td>
													<td style={{textAlign: "right"}}><RupeesSymbolWithAmountComponent amount={total_breakup.value} /></td>
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