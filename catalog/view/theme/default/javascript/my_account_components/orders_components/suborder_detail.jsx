class SuborderDetail extends React.Component{

	constructor(props){
		super(props);
		this.state={
			language:this.props.language,
			customer_id:this.props.customer_id,
			customer_access_token:this.props.customer_access_token,
			order_id:this.props.order_id,
			suborder_id:this.props.suborder_id,
			suborder_product_data:[],
			order_info:[],
			payment_mode_info:[],
			bank_details:[],
			upi_details:[],
			total_breakup_data:[],
			payment_address:[],
			shipping_address:[],
			current_status_info:[],
			progress_history:[],
			suborder_status:'',
			color_hex_code:'',
			status_progress:'',
			left_data:false,
			right_data:false,
			show_like_dislike_btn:false
		}
	}

	componentDidMount(){
		$(window).scrollTop(0);
		var suborder_products = [];
		var encode =  window.btoa('customer_id='+this.state.customer_id+
	    							'&customer_access_token='+this.state.customer_access_token+
	    							'&order_id='+this.state.order_id+
	    							'&suborder_id='+this.state.suborder_id
    							);
    	axios({
    		method:'GET',
    		url:'./api/customer_account/orders/getOrderInfoAndTotalAmountBreakup&data='+encode,
    		dataType:'json'
    	}).then(response => {
    		if(response.data.statusCode == 900){
				window.location.href = this.props.logout;
				return false;
			}
    		this.setState({ order_info:response.data.data.order_info,
    						payment_mode_info:response.data.data.order_info.payment_info,
    						bank_details:response.data.data.order_info.payment_info.bank_details,
    						upi_details:response.data.data.order_info.payment_info.upi_details,
    						total_breakup_data:response.data.data.total_breakup,
    						payment_address:response.data.data.order_info.payment,
    						shipping_address:response.data.data.order_info.shipping,
    						current_status_info:response.data.data.order_info.current_status_info,
    						progress_history:response.data.data.order_info.order_history,
    						suborder_status:response.data.data.order_info.suborder_status,
							color_hex_code:response.data.data.order_info.status_color_code,
							status_progress:response.data.data.order_info.status_progress,
							show_like_dislike_btn:response.data.data.order_info.show_like_dislike_btn,
                            left_data:true,
                            right_data:true
    					});

    		axios({
	    		method:'GET',
	    		url:'./api/customer_account/orders/getOrderProductDetailsBySuborderId&data='+encode,
	    		dataType:'json'
	    	}).then(response => {
	    		if(response.data.statusCode == 900){
					window.location.href = this.props.logout;
					return false;
				}
	    		this.setState({ suborder_product_data:response.data.data, 
	    					    right_data:true
	    					 });
	    	}).catch(error => {
				return false
			});

    	}).catch(error => {
			return false
		});
	}

	render(){
		return (
				<div className="suborder_detail_block">
					<div className="col-sm-4 suborder_detail_left_block">
                       {
                   		 	this.state.left_data ?
												   	<div className="left_data">  
														<div className="col-sm-12 suborder_detail_left_subblock no_padding">
															<div className="suborder_detail_left_section">
																<SuborderBackBotton language={this.state.language}
																					clickOnBackButtonOnSuborderDetail={this.props.clickOnBackButtonOnSuborderDetail}
																					order_info={this.state.order_info}/>
															</div>						
														</div>
														
														<div className="col-sm-12 suborder_detail_left_subblock no_padding">
															<div className="suborder_detail_left_section">
																<ProductTotalAmountComponent total_breakup_data={this.state.total_breakup_data}/>
															</div>	
														</div>
														


														<div className="col-sm-12 suborder_detail_left_subblock no_padding">
															<div className="suborder_detail_left_section">
																
																<PaymentModeInfoComponent language={this.state.language}
																													  payment_mode_info={this.state.payment_mode_info}
																													  bank_details={this.state.bank_details}
																													  upi_details={this.state.upi_details}
																													  current_status_info={this.state.current_status_info}/>
																
															</div>							  
														</div>
														
														
														<ShippingPreferenceComponent language={this.state.language}
																					 customer_id={this.state.customer_id}
																					 customer_access_token={this.state.customer_access_token}
																					 order_id={this.state.order_id}
																					 suborder_id={this.state.suborder_id}
																					 logout={this.props.logout}
																					/>
													</div>
											   	  :
												  	<OrderDetailLoadingLeft />
					   }				

					</div>

					<div className="col-sm-8 suborder_detail_right_block">
					    {
					   		this.state.right_data ?
														<div className="suborder_detail_right_section">
															<SuborderDetailHeading language={this.state.language}
																				   order_info_suborder_id={this.state.order_info.suborder_id}
																				   order_info_shipment_from={this.state.order_info.shipment_from}
																				   order_info_order_date={this.state.order_info.order_date}/>
															
															<AddressComponent language={this.state.language}
																			  payment_address={this.state.payment_address}
																			  shipping_address={this.state.shipping_address}
																			  order_info={this.state.order_info}
																			  current_status_info={this.state.current_status_info}
																			  payment_mode_info={this.state.payment_mode_info}
																			  bank_details={this.state.bank_details}
																			  customer_access_token={this.props.customer_access_token}
																			  logout={this.props.logout}
																			  />

															
															<div className="col-sm-12 progress_bar_block">
																<ProgressTrackingComponent 
																							suborder_id={this.state.suborder_id}
																							progress_history={this.state.progress_history}
																							suborder_status={this.state.suborder_status}
																							color_hex_code={this.state.color_hex_code}
																							status_progress={this.state.status_progress}
																						/>						
															</div>
															
															<SuborderProductListComponent language={this.state.language}
																		  				  suborder_product_data={this.state.suborder_product_data}
																		  				  customer_access_token={this.props.customer_access_token}
																		  				  logout={this.props.logout}
																		  				  show_like_dislike_btn={this.state.show_like_dislike_btn}/>
														</div>
							                        :
													  <OrderDetailLoadingRight />
                        }
					</div>
				</div>
			)
	}
}