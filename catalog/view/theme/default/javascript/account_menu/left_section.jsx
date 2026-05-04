class LeftSection extends React.Component {
	
	constructor(props)
	{
		super(props);
		this.state = {
			left_menu: false,
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
      	axios({
		    method:'get',
		    url:'./api/header/userlogin',
		    responseType:'json'
		   })
		   .then(response => {
		      this.setState({customer_datas: response.data.data});
		   });

		axios({
		    method:'get',
		    url:'./api/customer_account/profile/left_menu',
		    responseType:'json'
		   })
		   .then(response => {
		      this.setState({left_menu: response.data.data});
		   });
		      
    }

	render(){
		let count = 0;
		let self = this;
		return (
	   	 	<div className="left_section">
		   	 	<div className="panel-group user_block" data-title-value={this.state.customer_datas['cust_name']}>
			  	<div className="panel panel-default">
				    <div className="panel-heading panel_block">
				    	<h4 className="panel-title">
			        			<i className="fa fa-user red_color"></i> 
			        		<span className="panel_title"> {this.props.language.text_hello}</span>
			        		<span className="bold_content">{this.state.customer_datas['cust_name']}</span>
				      	</h4>
				    </div>
			  	</div>
			</div>
		   	 	
		   	 	{this.state.customer_datas.is_seller 
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
                 
               {this.state.left_menu ?
                <div>  
		   	 	<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.orders.title} 
	 								icon={this.state.left_menu.orders.icon}
	 								url=''
	 								active="" />
 						<MenuList title={this.state.left_menu.orders.title}
 										sub_menu={this.state.left_menu.orders.sub_menu}
 										active=""
 										 />
 					</div>
 				</div>	

	   	 		<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.profiles.title} 
 									icon={this.state.left_menu.profiles.icon}
 									url=''
 									active="" />
 						<MenuList title={this.state.left_menu.profiles.title}
									 sub_menu={this.state.left_menu.profiles.sub_menu}
									 active={this.props.active == 'profile' ? 'in' : ''}
									 sub_active={this.props.sub_active} />
 					</div>
 				</div>

 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.single_list.account_statement.title} 
	 								icon={this.state.left_menu.single_list.account_statement.icon}
   	 								url={this.state.left_menu.single_list.account_statement.url}
   	 								active={this.props.active == 'account_statement' ? 'active' : ''} />
 					</div>
 				</div>	   	 		
		   	 	
 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.single_list.credit_application.title} 
	 								icon={this.state.left_menu.single_list.credit_application.icon}
   	 								url={this.state.left_menu.single_list.credit_application.url}
   	 								active={this.props.active == 'credit_application' ? 'active' : ''} />
 					</div>
 				</div>

 				{this.state.left_menu.single_list.product_feed.is_dropshipper ?
 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.single_list.product_feed.title} 
	 								icon={this.state.left_menu.single_list.product_feed.icon}
   	 								url={this.state.left_menu.single_list.product_feed.url}
   	 								active={this.props.active == 'product_feed' ? 'active' : ''}/>
 					</div>
 				</div>
 				: ''}

 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.single_list.credit_note.title} 
	 								icon={this.state.left_menu.single_list.credit_note.icon}
   	 								url={this.state.left_menu.single_list.credit_note.url}
   	 								active={this.props.active == 'credit_note' ? 'active' : ''} />
 					</div>
 				</div>

 				<div className="panel-group">
	  				<div className="panel panel-default">
 						<MenuHeader title={this.state.left_menu.single_list.support.title} 
	 								icon={this.state.left_menu.single_list.support.icon}
   	 								url={this.state.left_menu.single_list.support.url}
   	 								active={this.props.active == 'support' ? 'active' : ''} />
 					</div>
 				</div>
		   	 	
		   	 	<div className="panel-group">
		  			<div className="panel panel-default">
   	 					<MenuHeader title={this.state.left_menu.single_list.logout.title} 
   	 								icon={this.state.left_menu.single_list.logout.icon}
   	 								url={this.state.left_menu.single_list.logout.url}
   	 								active="" />
   	 				</div>
   	 			</div>
   	 			</div>
   	 			: ''}

			</div>
		)
	}
}