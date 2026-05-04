class SomethingWentWrong extends React.Component {
	
	constructor(props){
		super(props);
	}

	render(){
		return (
	   	 	<div className="row">
	   	 		<div className="col-sm-12 text-center">
	   	 			<p><img src={cdn_url+"attention.png"} alt="You don't have record yet!" /></p>
	   	 			<h4>Oops! Something went wrong!</h4>
	   	 		</div>
	   	 		<div className="col-sm-12">
	   	 			<div className="something_error_message text-center">
	   	 				{
	   	 					this.props.searched_value ? <h4>ops! Order number {this.props.searched_value} doesn't know exit! Please check again</h4>
	   	 											  : <h4>ops! Orders doesn't exit! Please check again</h4>	
	   	 				}
	   	 					
	   	 					<h4>or</h4>
	   	 					<h4>Call on +91-8696491421 or Email us at info@wholesalebox.in</h4>
	   	 			</div>
	   	 		</div>
	   	 	</div>
		)
	}
}