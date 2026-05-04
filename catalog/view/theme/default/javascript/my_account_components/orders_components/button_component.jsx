class ButtonComponent extends React.Component {
	
	constructor(props) {
		super(props);
	}	

	render(){
		let color = this.props.button_color ? this.props.button_color : '';
		let bg_color = this.props.button_bg_color ? this.props.button_bg_color : '';
		let url =	this.props.button_url ? this.props.button_url : '';
		return (
	   	 	<a href={url} className={"btn btn-sm order_list_button word_spacing " + color + ' ' + bg_color} target="_blank">
	   	 		{this.props.button_icon ? 
   	 				<i className={this.props.button_icon}></i>
   	 				: ''
	   	 		} { this.props.button_label }
	   	 		
   	 		</a>
		)
	}
}
