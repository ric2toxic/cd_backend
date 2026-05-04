class SuborderDetailHeading extends React.Component{

	constructor(props){
		super(props);
	}

	render(){
		return (
				<div className="col-sm-12 suborder_heading no_padding">
					<div className="col-sm-4">
						<p className="bold_content">{this.props.language.title_suborder_no} <span>{this.props.order_info_suborder_id}</span></p>
					</div>
					<div className="col-sm-4 text-center">
						<p className="bold_content"><span>{this.props.order_info_shipment_from}</span></p>
					</div>
					<div className="col-sm-4">
						<p className="pull-right bold_content">{this.props.language.title_order_date} <span>{this.props.order_info_order_date}</span></p>
					</div>
				</div>	
			)
	}
}