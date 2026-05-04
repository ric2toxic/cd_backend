class RecordNotFound extends React.Component {
	
	constructor(props){
		super(props);
	}

	render(){
		return (
	   	 	<div className="row not_record_found">
	   	 		<div className="col-sm-12 text-center">
	   	 			<p><img src={cdn_url+"dekstopwithkurti.png"} alt={this.props.language.error_msg_dont_have_order} width="40%" height="20%" /></p>
	   	 			<h4>{this.props.language.error_msg_dont_have_order}</h4>
	   	 		</div>
	   	 		<div className="col-sm-12">
	   	 			<div className="popular_search">
	   	 				<h5 className="bold_content text-center">POPULAR SEARCHES:</h5>
	   	 				<ul>
	   	 				{
	   	 					this.props.popular_tags.map(function(tags, index){
	   	 						return (
	   	 								<li><a href={tags.href}>{tags.text}</a></li>
	   	 							)
	   	 					})
	   	 				}
	   	 				
	   	 				</ul>
	   	 			</div>
	   	 		</div>
	   	 	</div>
		)
	}
}