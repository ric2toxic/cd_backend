class RequestPaymentLink extends React.Component {
	
	constructor(props) {
		super(props);
		this.state ={
			show_pay_button: false,
			set_new_payment_url: '',
			click_on_req_pay_link:false,
			payment_error_msg:'',
			loading:false,
			loading_msg:'Loading...'
		} 

		this.clickOnRequestPaymentLink = this.clickOnRequestPaymentLink.bind(this);
	}

	clickOnRequestPaymentLink(){
		this.setState({click_on_req_pay_link: true, loading:true});
		axios({
			method:'GET',
			url: this.props.request_paymet_link+'&customer_access_token='+this.props.customer_access_token,
			dataType:'json'
		})
		.then(response => {
			this.setState({loading:false});

			if(response.data.statusCode == 900){
				window.location.href = this.props.logout;
				return false;
			}
			
			if(response.data.data.url){
				this.setState({show_pay_button: true, set_new_payment_url: response.data.data.url})
			} else {
				this.setState({show_pay_button: false,payment_error_msg:response.data.message,set_new_payment_url:''})
			}
		})
        .catch(error => {
        	this.setState({loading:false});
			return false
		});
	}

	render(){
		if( this.state.loading ){
			return (
						<span className="blue_color"> {this.state.loading_msg} </span>
					)
		} else {
			if( this.state.click_on_req_pay_link ) {
				return (	
						(this.state.show_pay_button) ? <a href={this.state.set_new_payment_url} 
				    										className={"request_payment_link btn btn-sm green_color white_bg_color"} target="_blank">
									   	 				 	{this.props.language.text_pay_now}
										   	 			</a>
													  : <span> {this.state.payment_error_msg} </span>
		   	 		)
			} else {
				return (
					<div>
						{ (this.props.show_payment_link ? <span className="request_payment_link btn btn-sm white_bg_color"> <a href={this.props.payment_link_url} className="green_color" target="_blank"> {this.props.language.text_pay_here} </a></span> : '') }
						{ ( this.props.show_payment_link && this.props.show_request_new_payment_link ? <span className="payment_link_or"> OR </span> : '' ) }
						{ (this.props.show_request_new_payment_link ? <span className="request_payment_link btn btn-sm green_color white_bg_color" target="_blank" onClick={()=>this.clickOnRequestPaymentLink()}> {this.props.language.text_pay_req_link} </span> : '') }					
					</div>		
				)
			}
		}
		

		
	}
}