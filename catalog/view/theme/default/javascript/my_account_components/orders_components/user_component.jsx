class UserComponent extends React.Component {
	
	constructor(props)	{
		super(props);		
	}

	render(){
	
		return (
			<div className="panel-group user_block" data-title-value={this.props.customer_datas['cust_name']}>
			  	<div className="panel panel-default">
				    <div className="panel-heading panel_block">
				    	<h4 className="panel-title">
			        			<i className="fa fa-user red_color"></i> 
			        		<span className="panel_title"> {this.props.language.text_hello}</span>
			        		<span className="bold_content">{this.props.customer_datas['cust_name']}</span>
				      	</h4>
				    </div>
			  	</div>
			</div>	   	 	
		)
	}
}