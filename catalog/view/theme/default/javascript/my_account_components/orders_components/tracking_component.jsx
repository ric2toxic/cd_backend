class TrackingComponent extends React.Component {
	
	constructor(props) {
		super(props);
	}

	render(){
		function createMarkup(data) { return {__html: data}; };
		let self = this;
		return (
	   	 	<div className="tracking_section">
	   	 	{	
   	 			$.map(this.props.suborders, function(suborder, index) {
	   	 			return (
	   	 				<div className="col-sm-12 order_summary">
	   	 					<div className="col-sm-3">
	   	 						<span className="suborder_no bold_content blue_color">{suborder.suborder_id}</span>				    			
	   	 					</div>
	   	 					<div className="col-sm-4 text-center">
	   	 						<span className="shipment_from bold_content">{suborder.shipment_from}</span>
	   	 					</div>
	   	 					<div className="col-sm-3">
	   	 						<span className="ord_amt bold_content">{self.props.language.label_amount}{<span dangerouslySetInnerHTML={createMarkup(suborder.total_amt)} />} </span>
	   	 					</div>
	   	 					<div className="col-sm-2">
	   	 						<div className="pull-right">
		   	 						<span 
										className={"btn btn-sm order_list_detail_button word_spacing blue_color white_bg_color"}
										onClick={()=>self.props.clickOnOrderDetailButton(suborder.order_id, suborder.suborder_id)}>
					   	 				 	{ self.props.language.label_ord_detail }											   	 		
						   	 		</span>
						   	 	</div>	
	   	 					</div>
				    		
				    		<div className="col-sm-12 progress_bar_block">

								<ProgressTrackingComponent suborder_id={suborder.suborder_id} 
														   progress_history={suborder.order_history} 
														   suborder_status={suborder.suborder_status}
														   color_hex_code={suborder.status_color_code}
														   status_progress={suborder.status_progress}
														   />

				    		</div>
				    		<div className="col-sm-12 no_padding">
				    		
				    			<div className="col-sm-10">
				    				{
			    						$.map(suborder.current_status_info, function(current_info, index){
			    							let msg = current_info.message ? current_info.message : '';
			    							let button = current_info.button ? current_info.button : '';
			    							return (<div className={ (msg !='' && button!='') ? "col-sm-9 shippment_status_block" 
					    																	  : (msg!='') ? "col-sm-12 shippment_status_block content_ellipsis"
				    																	 			      :  "col-sm-3 shippment_status_block" }>
			    										{
			    											msg ?
			    												<span className="shippment_status" data-toggle="tooltip" title={msg} >{msg}</span>
			    											: ''	
			    										}
			    										{	current_info.button.label ? 
										    											<span ><ButtonComponent 
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
			    					}
			    					
		    						
				    			</div>
				    			<div className="col-sm-2">
				    				<div className="pull-right">
				    					{
				    						suborder.show_download_invoice_link ? 
				    							<span>
				    								<a href={suborder.download_invoice_link} 
				    									className={"btn btn-sm order_list_button word_spacing white_color green_bg_color"}>
										   	 				<i className={"fa fa-download"}></i>&nbsp; 
									   	 				 	{ self.props.language.label_invoice }											   	 		
										   	 		</a>
										   	 	</span>	
				    						: ''	
				    					}				    					
				    				</div>			    						
				    			</div>
				    		</div>	
				    		
				    	</div>
					)
	   	 		})
	   	 	}
			</div>	   	 		
		)
	}
}
