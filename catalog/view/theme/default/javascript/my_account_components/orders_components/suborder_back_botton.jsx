class SuborderBackBotton extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){
		return (
				<div className="suborder_detail_back_button">
					<span className="btn btn-sm white_color blue_bg_color"
						  onClick={()=>this.props.clickOnBackButtonOnSuborderDetail()}>
				 		<i className="fa fa-arrow-left"></i> {this.props.language.text_button} </span>

				 	
				 	{
						this.props.order_info.show_download_invoice_link ? <span className="pull-right">
																				<p>
																					<a href={this.props.order_info.download_invoice_link } className="btn btn-sm white_color green_bg_color">
																						<i className="fa fa-download"></i> {this.props.language.label_invoice_download}
																					</a>
																			   </p>
																			</span>   
																		  : '' 
					}	
				</div>
			)
	}
}