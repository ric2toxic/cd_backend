class ShippingPreferenceComponent extends React.Component{
	
	constructor(props){
		super(props);
		this.state={
			no_wsb_tape:'',
			no_invoice_with_shipment:'',
			courier_preference:'',
			apply_whole_order:0,
			all_courier_preference:'',
			loading:false,
			loading_msg:'Loading...',
			show_edit_button:'',
			suborder_ids:[]

		}

		this.handleInputChange 				 = this.handleInputChange.bind(this);
		this.handleInputChangeCourier 		 = this.handleInputChangeCourier.bind(this);
		this.clickOnShippingPreferenceChange = this.clickOnShippingPreferenceChange.bind(this);
		this.clickOnShippingPreferenceSubmit = this.clickOnShippingPreferenceSubmit.bind(this);
	}

	componentWillMount(){
		var encode =  window.btoa('customer_id='+this.props.customer_id+
	    							'&customer_access_token='+this.props.customer_access_token+
	    							'&order_id='+this.props.order_id+
	    							'&suborder_id='+this.props.suborder_id+
	    							'&update_preferences=0'
    							);
    	axios({
    		method:'GET',
    		url:'./api/customer_account/orders/shippingPreferencesForOrderDetail&data='+encode,
    		dataType:'json'
    	}).then(response => {
	    	if(response.data.statusCode == 900){
				window.location.href = this.props.logout;
				return false;
			}
    		this.setState({ no_wsb_tape:response.data.data.no_wsb_tape, 
    						no_invoice_with_shipment:response.data.data.no_invoice_with_shipment, 
    						courier_preference:response.data.data.courier_preference,
    						all_courier_preference:response.data.data.all_courier_preference,
    						show_edit_button:response.data.data.show_edit_button,
    						suborder_ids:response.data.data.suborder_ids
    					});
    	}).catch(error => {
			return false
		});
	}

	handleInputChange(event){
		this.setState({
			[event.target.name]:event.target.value == 1 ? 0 : 1
		});
	}

	handleInputChangeCourier(event){
		this.setState({
			[event.target.name]:event.target.value 
		});
	}
	
	clickOnShippingPreferenceSubmit(){
		this.setState({loading:true});

		var encode =  window.btoa('customer_id='+this.props.customer_id+
	    							'&customer_access_token='+this.props.customer_access_token+
	    							'&order_id='+this.props.order_id+
	    							'&suborder_id='+this.props.suborder_id+
	    							'&update_preferences=1'+
	    							'&no_wsb_tape='+this.state.no_wsb_tape+
								    '&no_invoice_with_shipment='+this.state.no_invoice_with_shipment+
								    '&courier_preference='+this.state.courier_preference+
								    '&apply_whole_order='+this.state.apply_whole_order
    							);
    	axios({
    		method:'GET',
    		url:'./api/customer_account/orders/shippingPreferencesForOrderDetail&data='+encode,
    		dataType:'json'
    	}).then(response => {
	    	if(response.data.statusCode == 900){
				window.location.href = this.props.logout;
				return false;
			}
    		this.setState({loading:false});
    		$('.shipping_preference_submit_btn').addClass('hidden');
			$('.shipping_preference_change_btn').removeClass('hidden');
			$('input[name="no_wsb_tape"]').attr('disabled',true);
			$('input[name="no_invoice_with_shipment"]').attr('disabled',true);
			$('input[name="apply_whole_order"]').attr('disabled',true);
			$('input[name="courier_preference"]').attr('disabled',true);
    	}).catch(error => {
			return false
		});
	}

	clickOnShippingPreferenceChange(){
		$('.shipping_preference_submit_btn').removeClass('hidden');
		$('.shipping_preference_change_btn').addClass('hidden');
		$('input[name="no_wsb_tape"]').attr('disabled',false);
		$('input[name="no_invoice_with_shipment"]').attr('disabled',false);
		$('input[name="apply_whole_order"]').attr('disabled',false);
		$('input[name="courier_preference"]').attr('disabled',false);
	}

	render(){
		let self = this;
		let no_wsb_tape_checked = (parseInt(this.state.no_wsb_tape) ? 'checked' : '');
		let no_invoice_with_shipment_checked = (parseInt(this.state.no_invoice_with_shipment) ? 'checked' : '');

		if(this.state.show_edit_button){
			return (
						<div className="col-sm-12 suborder_detail_left_subblock no_padding">
							<div className="suborder_detail_left_section">
								<div className="row shipping_preference_block">
									<div className="col-sm-12 shipping_preference_heading">
										<div className="col-sm-9 no_padding">
											<p className="bold_content">
												<i className="fa fa-truck truck_icon blue_color"></i> {this.props.language.title_shipping_preference}
											</p>
										</div>
										<div className="col-sm-3 no_padding">
											<button type="button" 
												    className="btn btn-sm shipping_preference_change blue_bg_color white_color shipping_preference_change_btn"
												    onClick={()=>this.clickOnShippingPreferenceChange()}>{this.props.language.text_change_button}
										  	</button>
										  	{
										  		this.state.loading ? 	<button type="button" 
																		    className="btn btn-sm shipping_preference_change green_bg_color white_color shipping_preference_submit_btn">{this.state.loading_msg}
																  		</button>
										  						   : 	<button type="button" 
																		    	className="btn btn-sm shipping_preference_change green_bg_color white_color shipping_preference_submit_btn hidden"
																		    	onClick={()=>this.clickOnShippingPreferenceSubmit()}>{this.props.language.text_submit_button}
																  		</button>
										  	}	  
										</div>	
									</div>
									<div className="col-sm-12 shipping_preference_type">
										<div className="checkbox">
									    	<label><input type="checkbox" 
									    				  className="dont_use_wholesalebox_packing_tape" 
									    				  id={"no_wsb_tape"+this.props.suborder_id}
									    				  data-suborder-id={this.props.suborder_id}
								   						  name="no_wsb_tape" 
								   						  value={parseInt(this.state.no_wsb_tape)}
								   						  onChange={this.handleInputChange}
								   						  disabled
								   						  checked={no_wsb_tape_checked}/>{this.props.language.text_no_wsb_tape} </label>
									    </div>
									    <div className="checkbox">
									    	<label><input type="checkbox" 
									    				  className="dont_use_wholesalebox_packing_tape"
									    				  id={"no_invoice_with_shipment"+this.props.suborder_id}
									    				  data-suborder-id={this.props.suborder_id}
								   						  name="no_invoice_with_shipment" 
								   						  value={parseInt(this.state.no_invoice_with_shipment)}
								   						  onChange={this.handleInputChange}
								   						  disabled
								   						  checked={no_invoice_with_shipment_checked}/> {this.props.language.text_no_inv_with_shipment} </label>
									    </div>
									    {
									    	this.state.suborder_ids !='' ? <div className="checkbox">
																		    	<label><input type="checkbox" 
																		    				  className="dont_use_wholesalebox_packing_tape"
																		    				  id={"apply_whole_order"+this.props.suborder_id}
																		    				  data-suborder-id={this.props.suborder_id}
																	   						  name="apply_whole_order" 
																	   						  value={parseInt(this.state.apply_whole_order)}
																	   						  onChange={this.handleInputChange}
																	   						  disabled
																	   						  /> {this.props.language.text_apply_whole_order} </label>
																	   			<p>[{this.state.suborder_ids}]</p>			  
																		    </div>
																		  :''  
									    }									    
									</div>
									{
										this.state.all_courier_preference !=''
											? 
												<div className="col-sm-12 ">
											   		<div className="shipping_preference_courier">
														<h4> Courier Partner Preference </h4>
														<ul className="courier_partner_types">
															{
																$.map(this.state.all_courier_preference,function(partner_preference, index){
																	return (											
																				<li>
																					<label className="radio-inline">
																						<input type="radio"
																							   className="dont_use_wholesalebox_packing_tape" 
																							   name="courier_preference"
																							   onChange={self.handleInputChangeCourier}
																							   value={partner_preference.courier_name.toString().toLowerCase()} 
																							   disabled
																							   checked={partner_preference.courier_name.toString().toLowerCase() === self.state.courier_preference.toString().toLowerCase()}
																							     /> {partner_preference.courier_name} 
																					</label>		     
																				</li>					
																			)
																})
															}
														</ul>
													</div>
													<div className="shipping_preference_note">
														<span>
															{this.props.language.note_shipping_preference}
														</span>
													</div>
												</div>
											: ''	
									} 
								</div>
							</div>
						</div>		

					)
		} else if((parseInt(this.state.no_wsb_tape)) || (parseInt(this.state.no_invoice_with_shipment)) || this.state.all_courier_preference !='') {
			return (
						<div className="col-sm-12 suborder_detail_left_subblock no_padding">
							<div className="suborder_detail_left_section">
								<div className="row shipping_preference_block">
									<div className="col-sm-12 shipping_preference_heading">
										<div className="col-sm-9 no_padding">
											<p className="bold_content">
												<i className="fa fa-truck truck_icon blue_color"></i> {this.props.language.title_shipping_preference}
											</p>
										</div>	
									</div>
									<div className="col-sm-12 shipping_preference_type">
										{
											(parseInt(this.state.no_wsb_tape)) 
												? 	<div className="checkbox">
												    	<label><input type="checkbox" 
												    				  className="dont_use_wholesalebox_packing_tape" 
												    				  id={"no_wsb_tape"+this.props.suborder_id}
												    				  data-suborder-id={this.props.suborder_id}
											   						  name="no_wsb_tape" 
											   						  value={parseInt(this.state.no_wsb_tape)}
											   						  onChange={this.handleInputChange}
											   						  disabled
											   						  checked={no_wsb_tape_checked}/>{this.props.language.text_no_wsb_tape}
								   						</label>
												    </div>
												: '' 
										}

										{
											(parseInt(this.state.no_invoice_with_shipment)) 
												?   <div className="checkbox">
												    	<label><input type="checkbox" 
												    				  className="dont_use_wholesalebox_packing_tape"
												    				  id={"no_invoice_with_shipment"+this.props.suborder_id}
												    				  data-suborder-id={this.props.suborder_id}
											   						  name="no_invoice_with_shipment" 
											   						  value={parseInt(this.state.no_invoice_with_shipment)}
											   						  onChange={this.handleInputChange}
											   						  disabled
											   						  checked={no_invoice_with_shipment_checked}/> {this.props.language.text_no_inv_with_shipment} </label>
												    </div>
												: ''
										}
										
									    
									    {
									    	(this.state.show_edit_button && this.state.suborder_ids !='') 
									    		? 	<div className="checkbox">
													   	<label><input type="checkbox" 
													  				  className="dont_use_wholesalebox_packing_tape"
													   				  id={"apply_whole_order"+this.props.suborder_id}
													   				  data-suborder-id={this.props.suborder_id}
																	  name="apply_whole_order" 
																	  value="0"
																	  onChange={this.handleInputChange}
																	  disabled
																	  /> {this.props.language.text_apply_whole_order}
													  	</label>
														<p>[{this.state.suborder_ids}]</p>			  
												    </div>
												: ''  
									    }									    
									</div>
									{
										this.state.all_courier_preference !=''
											? 
												<div className="col-sm-12 ">
											   		<div className="shipping_preference_courier">
														<h4> Courier Partner Preference </h4>
														<ul className="courier_partner_types">
															{
																$.map(this.state.all_courier_preference,function(partner_preference, index){
																	return (											
																				<li>
																					<label className="radio-inline">
																						<input type="radio"
																							   className="dont_use_wholesalebox_packing_tape" 
																							   name="courier_preference"
																							   onChange={self.handleInputChangeCourier}
																							   value={partner_preference.courier_name.toString().toLowerCase()} 
																							   disabled
																							   checked={partner_preference.courier_name.toString().toLowerCase() === self.state.courier_preference.toString().toLowerCase()}
																							     /> {partner_preference.courier_name} 
																					</label>		     
																				</li>					
																			)
																})
															}
														</ul>
													</div>
													<div className="shipping_preference_note">
														<span>
															{this.props.language.note_shipping_preference}
														</span>
													</div>
												</div>
											: ''	
									} 
								</div>
							</div>
						</div>	
					)
		} else {
			return null;
		}
		
	}
}