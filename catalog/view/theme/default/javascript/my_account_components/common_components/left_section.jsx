class LeftSection extends React.Component {
	
	constructor(props)
	{
		super(props);
		this.state = {
			left_menu: this.props.left_menu,
			customer_datas: ''
		}
		
	}
	componentDidMount() {
	
		$('.customer_account_section .panel_block').click(function(){
			let title_value = $(this).attr('data-title-value');
			if($('#collapse'+title_value).hasClass('in')){
				$(this).find('.toggle_caret i').removeClass('fa-angle-down').addClass('fa-angle-right');
        	} else {
        		$(this).find('.toggle_caret i').removeClass('fa-angle-right').addClass('fa-angle-down');
        	}
      	}); 
      	//$('.toggle_caret').first().parent().parent().trigger("click");
      	$('#order_list_section .panel_block').trigger("click");

      	axios({
		    method:'get',
		    url:'./api/header/userlogin',
		    responseType:'json'
		   })
		   .then(response => {
		      this.setState({customer_datas: response.data.data});
		   });
    }

	render(){
		let count = 0;
		let self = this;
		return (
	   	 	<div className="left_section">
		   	 	<UserComponent customer_datas={this.state.customer_datas} language={this.props.language}/>
		   	 	
		   	 	{
		   	 		this.state.customer_datas.is_seller 
	   	 				? <div className="panel-group user_block">
						  	<div className="panel panel-default">
							    <a href={this.state.customer_datas['manufacturer_dashboard_link']}>
							    	<div className="panel-heading panel_block">
								    	<h4 className="panel-title">
						        			<i className="fa fa-dashboard red_color"></i> 
							        		<span className="panel_title"> {this.props.language.text_go_to_seller_dashboard}</span>
								      	</h4>
								    </div>
								</a>    
						  	</div>
						  </div>
						: ''  
		   	 	}

		   	 	<div className="panel-group" id="order_list_section">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.orders.title} 
	 								icon={this.state.left_menu.orders.icon}
	 								url='' />
 						<ListWithoutUrl title={this.state.left_menu.orders.title}
 										sub_menu={this.state.left_menu.orders.sub_menu}
 										clickOnFilterOrderType={this.props.clickOnFilterOrderType}
 										order_type_title={this.props.order_type_title}
 										default_ord_type_title={this.props.default_ord_type_title} />
 					</div>
 				</div>	

	   	 		<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.profiles.title} 
 									icon={this.state.left_menu.profiles.icon}
 									url='' />
 						<ListWithUrl title={this.state.left_menu.profiles.title}
									 sub_menu={this.state.left_menu.profiles.sub_menu} />
 					</div>
 				</div>
                
                {this.state.left_menu.single_list.account_statement.show ? 
 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.single_list.account_statement.title} 
	 								icon={this.state.left_menu.single_list.account_statement.icon}
   	 								url={this.state.left_menu.single_list.account_statement.url} />
 					</div>
 				</div>
 				: ''}	   	 		
		   	 	
	   	 		{/*<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.single_list.credit_note.title} 
	 								icon={this.state.left_menu.single_list.credit_note.icon}
   	 								url={this.state.left_menu.single_list.credit_note.url} />
 					</div>
 				</div>*/}
 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.single_list.credit_application.title} 
	 								icon={this.state.left_menu.single_list.credit_application.icon}
   	 								url={this.state.left_menu.single_list.credit_application.url} />
 					</div>
 				</div>

 				{this.state.left_menu.single_list.product_feed.is_dropshipper ?
 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.single_list.product_feed.title} 
	 								icon={this.state.left_menu.single_list.product_feed.icon}
   	 								url={this.state.left_menu.single_list.product_feed.url} />
 					</div>
 				</div>
 				: ''}
	   	 		
	   	 		<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.single_list.credit_note.title} 
	 								icon={this.state.left_menu.single_list.credit_note.icon}
   	 								url={this.state.left_menu.single_list.credit_note.url}
   	 								active={this.props.active == 'credit_note' ? 'active' : ''} />
 					</div>
 				</div>


 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<ListHeader title={this.state.left_menu.single_list.support.title} 
	 								icon={this.state.left_menu.single_list.support.icon}
   	 								url={this.state.left_menu.single_list.support.url}
   	 								active={this.props.active == 'support' ? 'active' : ''} />
 					</div>
 				</div>

		   	 	
		   	 	<div className="panel-group">
		  			<div className="panel panel-default">
   	 					<ListHeader title={this.state.left_menu.single_list.logout.title} 
   	 								icon={this.state.left_menu.single_list.logout.icon}
   	 								url={this.state.left_menu.single_list.logout.url} />
   	 				</div>
   	 			</div>	
			</div>
		)
	}
}