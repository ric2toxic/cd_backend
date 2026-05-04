class ProgressTrackingComponent extends React.Component {
	
	constructor(props) {
		super(props);
		this.progressToggling = this.progressToggling.bind(this);
	}

	progressToggling(suborder_ids){
		$('#progress_bar_history'+suborder_ids).toggle();
	}

	render(){		
		let suborder_ids = this.props.suborder_id;
		return (
			<div className="progress_bar_display">
		   	 	<div className="progress" data-suborder-id={this.props.suborder_id} onClick={()=>this.progressToggling(suborder_ids)}>
			  		<div className="progress-bar" role="progressbar" style={{width:this.props.status_progress+'%', backgroundColor:this.props.color_hex_code }}>{this.props.suborder_status}</div>
				</div>  	 		
				<div className="row progress_bar_history" style={{display:"none"}}  id={'progress_bar_history'+this.props.suborder_id}>
					{
						$.map(this.props.progress_history, function(history, index){
							return(
									<div className="col-sm-12">
										<div className="col-sm-3">{history.label}</div>
										<div className="col-sm-3">{history.status_date}</div>
										<div className="col-sm-6">{history.comment  ? history.comment : <div>&nbsp;</div>}</div>
									</div>
								)
						})
					}
						
				</div>
			</div>	
		)
	}
}
