class MyAccount extends React.Component {
	
	constructor(props)
	{
		super(props);
		 this.state = {
	        language: this.props.language,
	        international_store: this.props.international_store,
	        left_menu: this.props.left_menu,
	        filter_order_type_value: this.props.filter_order_type_value,
	        order_type_title:this.props.order_type_title,
	        default_ord_type_title:'All Orders',
	        orders:[],
	        searched_value:this.props.common_order_data.searching_value,
	        beyond_order_id: false,
		    scrolling_value:1,
		    loading: false,
		    something_went_wrong: false,

		    order_id:this.props.common_order_data.order_id,
		    suborder_id:this.props.common_order_data.suborder_id,

		    suborders_data:[],
		    logout:left_menu.single_list.logout.url
			
       	}

       // this.handleScroll 				= this.handleScroll.bind(this);
       this.clearSearch 				= this.clearSearch.bind(this); 
       this.invOrOrdNoSearchSubmit 		= this.invOrOrdNoSearchSubmit.bind(this); 	
       this.invOrOrdNoSearchChangeValue = this.invOrOrdNoSearchChangeValue.bind(this); 	
       this.clickOnFilterOrderType 		= this.clickOnFilterOrderType.bind(this);
       this.clickOnShowMoreOrders		= this.clickOnShowMoreOrders.bind(this);
       this.clickOnOrderDetailButton	= this.clickOnOrderDetailButton.bind(this);
       this.clickOnBackButtonOnSuborderDetail	= this.clickOnBackButtonOnSuborderDetail.bind(this);
	}

	componentDidMount(){
		$('body').addClass('my_account_body'); 
		this.setState({ loading:true, 
						something_went_wrong:false});

		if(!this.state.order_id && !this.state.suborder_id){
			this.defaultGetOrderList();
		}
	}

	defaultGetOrderList(){
		var encode =  window.btoa('customer_id='+this.getCookies().customer_id+
	  								'&customer_access_token='+this.getCookies().customer_access_token+
	  								'&filter_order_or_invoice_no='+this.state.searched_value+
	    							'&order_type_filter='+this.state.filter_order_type_value+
	  								'&offset=5');
			
		axios({
			method:'GET',
			url:'./api/customer_account/orders/getOrderList&data='+encode,
			dataType:'json'
		})
		.then(response => {
			if(response.data.statusCode == 900){
				window.location.href = this.state.logout;
				return false;
			}
			if((Object.keys(response.data.data).length) > 0){
				this.setState({ orders: response.data.data.orders, 
								beyond_order_id: response.data.data.beyond_order_id,
								loading:false,
								something_went_wrong:false })
			} else {
				this.setState({ orders: [], 
								beyond_order_id: '',
								loading:false,
								something_went_wrong:false })
			}
		})
		.catch(error => {
			this.setState({	loading:false, 
							something_went_wrong:true})
		});
	}

	getCookies(){

	    var pairs = document.cookie.split(";");
	    var cookiesObj = {};
	    
	    for (var i = 0; i < pairs.length; i++) {
	      var pair = pairs[i].split("=");
	      cookiesObj[(pair[0] + '').trim()] = unescape(pair[1]);
	    }
	    return cookiesObj;
	}

	invOrOrdNoSearchChangeValue(eve){
		this.setState({searched_value : eve.target.value});
	}

	invOrOrdNoSearchSubmit(searching_value){
	    this.setState({	searched_value: searching_value,
	    				something_went_wrong: false});
	  
	    var encode =  window.btoa('customer_id='+this.getCookies().customer_id+
	    							'&customer_access_token='+this.getCookies().customer_access_token+
	    							'&filter_order_or_invoice_no='+searching_value+
	    							'&order_type_filter='+this.state.filter_order_type_value+
	    							'&offset=5');
			axios({
				method:'GET',
				url:'./api/customer_account/orders/getOrderList&data='+encode,
				dataType:'json'
			})
			.then(response => {
				if(response.data.statusCode == 900){
					window.location.href = this.state.logout;
					return false;
				}
				if((Object.keys(response.data.data).length) > 0){
					this.setState({ orders: response.data.data.orders,
									beyond_order_id: response.data.data.beyond_order_id,
									loading:false,
									something_went_wrong:false })
				} else {
					this.setState({ orders: [],
									beyond_order_id:'',
									loading:false,
									something_went_wrong: false })
				}
				
			})
			.catch(error => {
				this.setState({	something_went_wrong:true})
			});
			this.addFilterInHistoryState(this.state.filter_order_type_value, '','',searching_value);
  	}

  	clearSearch(){
    	this.setState({searched_value:''});
    	this.invOrOrdNoSearchSubmit('');
  	}

  	filterOrderTypeAddInUrl(order_type_value){
        this.setState({filter_order_type_value:order_type_value});
		this.addFilterInHistoryState(order_type_value, '','','');
  	} 


	clickOnFilterOrderType(order_type_value,order_type_title){
    	this.setState({ filter_order_type_value: order_type_value, 
    				   	order_type_title: order_type_title,
    					searched_value:'',
    					orders:'',
    					loading:true,
    					something_went_wrong:false});

    	// filter order type value added in url
    	this.filterOrderTypeAddInUrl(order_type_value);


    	var encode =  window.btoa('customer_id='+this.getCookies().customer_id+
	  								'&customer_access_token='+this.getCookies().customer_access_token+
	  								'&order_type_filter='+order_type_value+
	  								'&offset=5');
		axios({
			method:'GET',
			url:'./api/customer_account/orders/getOrderList&data='+encode,
			dataType:'json'
		})
		.then(response => {
			if(response.data.statusCode == 900){
				window.location.href = this.state.logout;
				return false;
			}
			if((Object.keys(response.data.data).length) > 0){
				this.setState({ orders: response.data.data.orders, 
								beyond_order_id: response.data.data.beyond_order_id,
								loading:false,
								something_went_wrong:false })
			} else {
				this.setState({ orders: [], 
								beyond_order_id: '',
								loading:false,
								something_went_wrong:false})
			}
		})
        .catch(error => {
			this.setState({	something_went_wrong:true})
		});
    }

    clickOnShowMoreOrders(){
    	$('.show_more_orders').text(this.state.language.text_loading);
    	var encode =  window.btoa('customer_id='+this.getCookies().customer_id+
	    							'&customer_access_token='+this.getCookies().customer_access_token+
	    							'&beyond_order_id='+this.state.beyond_order_id+
	    							'&filter_order_or_invoice_no='+this.state.searched_value+
	    							'&order_type_filter='+this.state.filter_order_type_value+
	    							'&offset=3');
    	axios({
	          method:'GET',
	          url:'./api/customer_account/orders/getOrderList&data='+encode,
	          dataType:'json'
	        })
			.then(response => {
				if(response.data.statusCode == 900){
					window.location.href = this.state.logout;
					return false;
				}
	          	if((Object.keys(response.data.data).length) > 0){
	            	
	            	let new_order_data = response.data.data.orders;
	            	let old_data = this.state.orders;
	            	let marging_data = [...old_data,...new_order_data];

	            	this.setState({ orders:marging_data,
	            					beyond_order_id:response.data.data.beyond_order_id,
	            					something_went_wrong:false});

	            	$('.show_more_orders').text(this.state.language.text_show_more_order);
	          	} else {
	          		$('.show_more_orders').text(this.state.language.text_show_more_order);
	            	this.setState({ orders: [],
	            					beyond_order_id:'',
	            					something_went_wrong:false })
	          	}
									          
	        })
	        .catch(error => {
				return false;
			});

    }

    // for order details
    clickOnOrderDetailButton(order_id, suborder_id){    	
    	this.setState({order_id: order_id, suborder_id: suborder_id});
    	this.addFilterInHistoryState(this.state.filter_order_type_value, order_id, suborder_id, this.state.searched_value);

    }

   	clickOnBackButtonOnSuborderDetail(){
   		this.setState({order_id: '', suborder_id: ''});
   		this.addFilterInHistoryState(this.state.filter_order_type_value, '','',this.state.searched_value);
   		if(this.state.orders!=''){
   			this.setState({orders:this.state.orders});
   		} else { 
   			this.defaultGetOrderList();
   		}
   	}


     addFilterInHistoryState(order_type_value, order_id, suborder_id, searching_value=''){

  		let baseUrl = [location.protocol, '//', location.host, location.pathname].join('')+'?route=account/order';
  		
  		var filter_string 	= '';

  		if(order_type_value!='')
  		{
  			filter_string   = filter_string+"&filter_order_type_value=" + order_type_value;
  		}

  		if(order_id!='' && suborder_id!=''){
  			filter_string   = filter_string+"&order_id=" + order_id + "&suborder_id="+ suborder_id;
  		}

  		if(searching_value!=''){
  			filter_string   = filter_string+"&searching_value=" + searching_value;
  		} 
  		
       	window.history.pushState('', null, baseUrl+filter_string);
   	}  	

	render(){
		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						{
							!this.state.order_id && !this.state.suborder_id ? 
																			<OrderList left_menu={this.state.left_menu}
																					   language={this.state.language}
																					   clickOnFilterOrderType={this.clickOnFilterOrderType}
																					   order_type_title={this.state.order_type_title}
																					   default_ord_type_title={this.state.default_ord_type_title}

																					   order_data={this.state.orders}
																					   beyond_order_id={this.state.beyond_order_id}
																			   		   invOrOrdNoSearchSubmit={this.invOrOrdNoSearchSubmit}
																			   		   invOrOrdNoSearchChangeValue={this.invOrOrdNoSearchChangeValue}
																			   		   searched_value={this.state.searched_value}
																			   		   clearSearch={this.clearSearch}
																			   		   loading={this.state.loading}
																			   		   filter_order_type_value={this.state.filter_order_type_value}
																			   		   order_type_title={this.state.order_type_title}
																			   		   menus={this.props.menus}
																			   		   popular_tags={this.props.popular_tags}
																			   		   something_went_wrong={this.state.something_went_wrong}
																			   		   clickOnOrderDetailButton={this.clickOnOrderDetailButton}
																			   		   clickOnShowMoreOrders={this.clickOnShowMoreOrders}
																			   		   logout={this.state.logout}
																			   		   customer_access_token={this.getCookies().customer_access_token} />
																			: 
																				<SuborderDetail language={this.state.language}
																								order_id={this.state.order_id}
																								suborder_id={this.state.suborder_id}
																								customer_id={this.getCookies().customer_id}
																								clickOnBackButtonOnSuborderDetail={this.clickOnBackButtonOnSuborderDetail}
																								logout={this.state.logout}
																								customer_access_token={this.getCookies().customer_access_token}
																								/>   		   
						}
						   	
						</section>
						<div class="col-sm-6 col-md-6" id="my_account_notification" style={{display: "none;"}}>
    					</div>
					</div>
				 </div>
		       )
	}
}