class OrderListComponent extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			language: this.props.language,
		}
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();   
		});
	}

  	componentDidUpdate(prevProps, prevState, snapshot){
  		$('.my_account_order_list .panel_block').click(function(){
			let index_value = $(this).attr('data-index-value');
			if($('#order_list'+index_value).hasClass('in')){
				$(this).find('.toggle_caret i').removeClass('fa-angle-up').addClass('fa-angle-down');
        	} else {
        		$(this).find('.toggle_caret i').removeClass('fa-angle-down').addClass('fa-angle-up');
        	}
      	}); 
  	}

	render(){
		function createMarkup(data) { return {__html: data}; };
		let self = this;
		if(!this.props.loading) {
			return (
		   	 	<div className="order_list_section">
		   	 		<div className="col-sm-12 my_account_order_list">
		   	 			{ 
			   	 			this.props.orders != '' ? 
			   	 				$.map(this.props.orders, function(order, index) {
			   	 					return (<div className="panel-group">
										<div className="panel panel-default">
										    <div className="panel-heading panel_block" data-index-value={index} data-toggle="collapse" href={"#order_list"+index}>
								        		<div className="col-sm-2">
								        			<p>{self.state.language.column_order_no} </p>
								        			<p className="blue_color bold_content"><b>{order.order_no}</b></p>
								        		</div>
								        		<div className="col-sm-2">
								        			<p>{self.state.language.column_order_date} </p>
											        <p className="bold_content">{order.order_date}</p>
								        		</div>
								        		<div className="col-sm-5">
								        			<p>{self.state.language.column_payment_mode} </p>
											        <p className="bold_content content_ellipsis" data-toggle="tooltip" title={order.payment_mode}>{order.payment_mode}</p>
								        		</div>
								        		<div className="col-sm-2">
								        			<p>{self.state.language.column_total_amount} </p>
											        <p className="bold_content">{<div dangerouslySetInnerHTML={createMarkup(order.order_total)} />}</p>
								        		</div>
								        		<div className="col-sm-1">
								        			<div className="toggle_caret">
								        				<i className="fa fa-angle-down"></i>
								        			</div>
								        		</div>
										    </div>
										    <div id={"order_list"+index} className="panel-collapse collapse order_summary_block">
											    <div className="panel-body">
												    {
												    	order.payment_status ? <div className="payment_status_and_link_section">
																			    	<div className="col-sm-6">
																			    		<span className="pending_status"> {self.state.language.label_payment_status} </span>
																			    	 	<span className="pending_status bold_content red_color">{order.payment_status}</span>
																		    	 	</div>
																			    	<div className="col-sm-6 text-right">
																			    		<RequestPaymentLink
																			    				language={self.state.language}
																			    				show_payment_link={order.show_payment_link}
																			    				payment_link_url={order.payment_link}
																			    				show_request_new_payment_link={order.show_request_new_payment_link}
																			    				request_paymet_link={order.request_new_payment_link}
																			    				logout={self.props.logout}
																			    				customer_access_token={self.props.customer_access_token}
																		    				 />
																		    		</div>
																		    	</div>
																		      : ''	
												    }											    	

											    	<TrackingComponent suborders={order.suborders} 
											    					   language={self.state.language}
											    					   clickOnOrderDetailButton={self.props.clickOnOrderDetailButton}/>
											    </div>
										    </div>
									  	</div>
									</div>)
			   	 				})	
			   	 			: ( self.props.something_went_wrong ) ? <SomethingWentWrong searched_value={this.props.searched_value} />
			   	 												  : <RecordNotFound language={this.state.language} menus={self.props.menus} popular_tags={self.props.popular_tags}/>
						}
						 
					</div>	
					{
						(this.props.orders != '' && this.props.beyond_order_id) ? 	<div className="col-sm-12 order_list_browse_more">
																						<div className="text-center">
																							<span className="btn btn-sm white_color blue_bg_color show_more_orders" onClick={()=>self.props.clickOnShowMoreOrders()}>
																								{self.state.language.text_show_more_order}
																							</span>
																						</div>  
																					 </div>
																				: 	''	 
					}
					
				</div>
			)
		} else {
			return (
		   	 	<div className="order_list_section">
		   	 		<div className="col-sm-12 my_account_order_list">
		   	 			<OrderListLoadingRight />
		   	 		</div></div>)
		}
	}
}