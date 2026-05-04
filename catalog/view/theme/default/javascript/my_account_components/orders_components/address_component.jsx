class AddressComponent extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){
		return (
				<div className="col-sm-12 address_block">

					<AddressContentComponent title={this.props.language.title_billing_address}
											 address={this.props.payment_address} />

					<AddressContentComponent title={this.props.language.title_shipping_address}
											 address={this.props.shipping_address} />

					<div className="col-sm-4">
						{
    						(this.props.current_status_info !='') ? 
	    							$.map(this.props.current_status_info, function(current_info, index){

		    							let msg = current_info.message ? current_info.message : '';
		    							let button = current_info.button ? current_info.button : '';
		    							return (<div className="col-sm-12 no_padding shippment_status_block">
		    										{
		    											msg ?
		    												<span className="shippment_status pull-left bold_content" data-toggle="tooltip" title={msg} >{msg}</span>
		    											: ''	
		    										}
		    										{	current_info.button.label ? 
									    											<span className="pull-right"><ButtonComponent 
																    										button_label={current_info.button.label} 
																    										button_icon={current_info.button.icon} 
																    										button_url={current_info.button.url} 
																    										button_color={current_info.button.color}
																    										button_bg_color={current_info.button.bg_color} /> 
										    										</span>	
										    									  : ''
		    										}
			    									
			    									
												</div>)
		    						})
	    						: 
									this.props.payment_mode_info.payment_status ? 
																			    	<RequestPaymentLink
																				    		language={this.props.language}
																							show_payment_link={this.props.payment_mode_info.show_payment_link}
																					    	payment_link_url={this.props.payment_mode_info.payment_link}
																					    	show_request_new_payment_link={this.props.payment_mode_info.show_request_new_payment_link}
																					    	request_paymet_link={this.props.payment_mode_info.request_new_payment_link}
																					    	customer_access_token={this.props.customer_access_token}
																				   	 	/>
																				: ''	
																	
    					}
					</div>	
				</div>
			)
	}
}