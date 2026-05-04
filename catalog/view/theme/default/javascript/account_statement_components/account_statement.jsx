class AccountStatement extends React.Component {
	
	constructor(props)
	{
		super(props);
		 this.state = {
	        language: this.props.language,
	        international_store: this.props.international_store,
	        popular_tags:this.props.popular_tags,
	        beyond_order_id: false,
	        balance:false,
		    scrolling_value:1,
		    loading: true,
		    something_went_wrong: false,
		    logout:this.props.logout_url,
		    account_summary:false,
		    orders:false,
            orders_breakup:false,
            orders_breakup_page:false,
		    filter_search:{},
		    breakup_debit:false,
		    breakup_credit:false,
		    breakup_balance:false,
		    breakup_is_positive_bal:false,
		    important_note:'',
		    payment_methods:false,
		    scrollTop:0,
		    scrollTopFix:0
       	}

       this.addFilterInHistoryState 	   = this.addFilterInHistoryState.bind(this);
       this.getCustomerLevelAccountSummary = this.getCustomerLevelAccountSummary.bind(this);
       this.getOrderLevelAccountSummary    = this.getOrderLevelAccountSummary.bind(this);
       this.clearSearch 		           = this.clearSearch.bind(this);  
       this.showMoreOrders                 = this.showMoreOrders.bind(this);  
       this.searchChangeValue              = this.searchChangeValue.bind(this);
       this.searchChangeDateValue          = this.searchChangeDateValue.bind(this);
       this.getStatementList               = this.getStatementList.bind(this);
       this.getOrderLevelAccountSummaryPagination = this.getOrderLevelAccountSummaryPagination.bind(this); 
       this.getUrlParameter                = this.getUrlParameter.bind(this); 
       this.getOrderDetailPage             = this.getOrderDetailPage.bind(this); 
       this.getOrderBreakupLevelAccountSummary = this.getOrderBreakupLevelAccountSummary.bind(this);
       this.downloadReport                     = this.downloadReport.bind(this);
	}

	componentDidMount(){

		$('body').addClass('my_account_body'); 

		this.setState({ loading:true, 
						something_went_wrong:false});

         let filter_search            = this.state.filter_search;
         var filter_order_no          = this.getUrlParameter('filter_order_no');
		 var filter_invoice_no        = this.getUrlParameter('filter_invoice_no');
		 var filter_payment_method    = this.getUrlParameter('filter_payment_method');
		 var filter_ordered_date_from = this.getUrlParameter('filter_ordered_date_from');
		 var filter_ordered_date_to   = this.getUrlParameter('filter_ordered_date_to');
		 var filter_invoice_date_from = this.getUrlParameter('filter_invoice_date_from');
		 var filter_invoice_date_to   = this.getUrlParameter('filter_invoice_date_to');
		 var filter_order_id          = this.getUrlParameter('filter_order_id');
		 var breakup_order_no         = this.getUrlParameter('breakup_order_no');
		 var current_year             =  1;

         if(filter_order_no) { filter_search['filter_order_no'] = filter_order_no; current_year=0; }
         if(filter_invoice_no) { filter_search['filter_invoice_no'] = filter_invoice_no; current_year=0; }
         if(filter_payment_method) { filter_search['filter_payment_method'] = filter_payment_method; current_year=0; }
         if(filter_ordered_date_from) { filter_search['filter_ordered_date_from'] = filter_ordered_date_from; current_year=0; }
         if(filter_ordered_date_to) { filter_search['filter_ordered_date_to'] = filter_ordered_date_to; current_year=0; }
         if(filter_invoice_date_from) { filter_search['filter_invoice_date_from'] = filter_invoice_date_from; current_year=0; }
         if(filter_invoice_date_to) { filter_search['filter_invoice_date_to'] = filter_invoice_date_to; }
         if(filter_order_id) { filter_search['filter_order_id'] = filter_order_id; current_year=0; }
         if(breakup_order_no) { filter_search['breakup_order_no'] = breakup_order_no; current_year=0; }
		 
       
		 if(current_year)
		 {
		 	var d    = new Date();
            var year = d.getFullYear();
            filter_search['filter_invoice_date_from'] = year+'-01-01';
            filter_search['filter_invoice_date_to']   = year+'-12-31';
		 }

		 this.setState({filter_search});
		 this.addFilterInHistoryState(filter_search);

		if(!this.state.orders)
        {
	     this.getStatementList();
         var filters = '';
         filters = filters+'customer_id='+getCookie('customer_id');
         filters = filters+'&customer_access_token='+getCookie('customer_access_token');
         var encode =  window.btoa(filters);
	     //this.getCustomerLevelAccountSummary(encode);
	    }
    
     let self = this;
	 $(window).scroll(function(){
        let scrollTop = $(window).scrollTop();
         self.setState({scrollTop});
      }); 
		
	}

	getStatementList(download='', filter_search='')
	{
		if(filter_search == '')
		{
		   filter_search = this.state.filter_search;	
		}
		else
		{
		   this.setState({filter_search});	
		}
        var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        $.map(filter_search, function(filter, index) {
        	if(filter)
  			{
              filters = filters+'&'+index+'='+filter;
            }  	
        });	
     
        var encode =  window.btoa(filters);
        if(download=='download')
        {
        	this.downloadReport(encode);
        }
        else if(filter_search['filter_order_id'])
        {
           this.getOrderBreakupLevelAccountSummary(encode);	
        }
        else
        {
          this.getOrderLevelAccountSummary(encode);	
        } 
        
        this.addFilterInHistoryState(filter_search);
	}

	 showMoreOrders()
  	 {
    	$('.show_more_orders').text(this.state.language.text_loading);
    	let filter_search = this.state.filter_search;
        var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        if(this.state.beyond_order_id)
        {
          filters = filters+'&beyond_order_id='+this.state.beyond_order_id;	
        }
        if(this.state.balance)
        {
          filters = filters+'&balance='+this.state.balance;	
        }
        $.map(filter_search, function(filter, index) {
           if(filter)
  			{	
             filters = filters+'&'+index+'='+filter;
            } 	
        });
        var encode =  window.btoa(filters);
        this.getOrderLevelAccountSummaryPagination(encode);
    }

    getOrderDetailPage(order_id, breakup_order_no=false)
    {
      let filter_search = this.state.filter_search;	
      var loading = true;
      var orders_breakup_page = true;
       console.log(this.state.scrollTopFix);
      if(order_id == 0)
      {
      	 $("html, body").animate({ scrollTop: this.state.scrollTopFix }, 400); 
         filter_search['filter_order_id'] = false;
         filter_search['breakup_order_no'] = false;
         orders_breakup_page = false;
         this.setState({filter_search,orders_breakup_page});
         if(!this.state.orders)
         {
         	 loading = false;
         	 this.setState({loading});
             this.getStatementList(); 
         }
      }
      else
      {
      	 var scrollTopFix = this.state.scrollTop;
      	 console.log(scrollTopFix);
      	 this.setState({scrollTopFix});
      	 filter_search['filter_order_id'] = order_id;
      	 filter_search['breakup_order_no'] = breakup_order_no;
      	 if(this.state.orders_breakup)
         {
         	loading = false;
         }
         this.setState({filter_search,orders_breakup_page,loading});
         this.getStatementList(); 
         $("html, body").animate({ scrollTop: 0 }, 400);
      }
    }

   searchChangeValue(field, event)
   {     
   	    let filter_search = this.state.filter_search;
      	if(field == 'filter_payment_method')
      	{
      		var filter_payment_method = $("#filter_payment_method").val();
      		if(filter_payment_method)
      		{
      		  filter_search[field] = filter_payment_method.join();	
      		}
      		else
      		{
      			filter_search[field] = false;
      		} 
      		
      	}
      	else
      	{
           filter_search[field] = event.target.value;
      	}

      	if(field == 'filter_financial_year' || field == 'filter_quarterly')
      	{
      	  filter_search['filter_invoice_date_from'] = '';
   	      filter_search['filter_invoice_date_to']   = ''; 	
      	}

   	    var filter_financial_year = new Date().getFullYear();
        if(filter_search.filter_financial_year)
        {
          filter_financial_year = filter_search.filter_financial_year;
   	      filter_search['filter_invoice_date_from'] = filter_financial_year+'-04-01';
   	      filter_search['filter_invoice_date_to']   = (parseInt(filter_financial_year)+1)+'-03-31';
        }

        if(filter_search.filter_quarterly)
        {
          if(filter_search.filter_quarterly == 'Q1')
          {
             filter_search['filter_invoice_date_from'] = filter_financial_year+'-04-01';
   	         filter_search['filter_invoice_date_to']   = filter_financial_year+'-06-30';  	
          }
          if(filter_search.filter_quarterly == 'Q2')
          {
             filter_search['filter_invoice_date_from'] = filter_financial_year+'-07-01';
   	         filter_search['filter_invoice_date_to']   = filter_financial_year+'-09-30';  	
          }	
          if(filter_search.filter_quarterly == 'Q3')
          {
             filter_search['filter_invoice_date_from'] = filter_financial_year+'-10-01';
   	         filter_search['filter_invoice_date_to']   = filter_financial_year+'-12-31';  	
          }	
          if(filter_search.filter_quarterly == 'Q4')
          {
             filter_search['filter_invoice_date_from'] = (parseInt(filter_financial_year)+1)+'-01-01';
   	         filter_search['filter_invoice_date_to']   = (parseInt(filter_financial_year)+1)+'-03-31';  	
          }
        }
      
		this.setState({filter_search});
   }

   searchChangeDateValue(field, value)
   {
   	 let filter_search = this.state.filter_search;
   	 filter_search[field] = value;
   	 if(field == 'filter_invoice_date_from' || field == 'filter_invoice_date_to')
   	 {
   	 	filter_search['filter_quarterly']      = '';
   	 	filter_search['filter_financial_year'] = '';
   	 }
   	 this.setState({filter_search});
   }

  	clearSearch(){
  		$(".clear_all_btn").html('<i class="fa fa-refresh fa-spin"></i>');
  		var filter_search = {};
    	this.setState({filter_search});
        
        $(".multiselect-selected-text").html("Search by Payment Method");
    	setTimeout(function(){ 
    		$('#filter_payment_method').multiselect("deselectAll", false); 
    	}, 500);
    	
    	this.getStatementList('', filter_search);
  	}


	getCustomerLevelAccountSummary(encode)
	{
		axios({
			method:'GET',
			url:'./api/customer_account/accounts/getCustomerLevelAccountSummary&data='+encode,
			dataType:'json'
		})
		.then(response => {
			if(response.data.statusCode == 900){
				window.location.href = this.state.logout;
				return false;
			}
			
			if(response.data.statusCode == 200)
			{
			   this.setState({ important_note: response.data.important_note,
			                   payment_methods: response.data.payment_methods})
			   	
			  if((Object.keys(response.data.data).length) > 0){
				this.setState({ account_summary: response.data.data,})
		    	} else {
				this.setState({
					            account_summary:false,
					            loading:false,
								something_went_wrong:false })
			  }
			}
			else
			{
				this.setState({	loading:false, 
							something_went_wrong:true});
			}  

		})
		.catch(error => {
			this.setState({	loading:false, 
							something_went_wrong:true})
		});
	}

	getOrderLevelAccountSummary(encode)
	{ 
		if($(".clear_all_btn").html() == 'Clear All')
		{
		  $("#search_submit").html('<i class="fa fa-refresh fa-spin"></i>');	
		}
		
		$(".my_account_order_list").addClass("disable");
		axios({
			method:'GET',
			url:'./api/customer_account/accounts/getOrderLevelAccountSummary&data='+encode,
			dataType:'json'
		})
		.then(response => {
			$("#search_submit").html('Search');
			$(".clear_all_btn").html('Clear All');
			$(".my_account_order_list").removeClass("disable");
			if(response.data.statusCode == 900){
				window.location.href = this.state.logout;
				return false;
			}
			console.log("check");
			if(response.data.statusCode == 200)
			{

			  if((Object.keys(response.data.data.customer_summary).length) > 0)
			  {
                  if((Object.keys(response.data.data.orders).length) <  response.data.data.limit)
                  {
                  	 this.setState({ payment_methods: response.data.payment_methods,
                  	 	        orders: response.data.data.orders,
                  	 	        account_summary: response.data.data.customer_summary,
                  	 	        important_note: response.data.important_note,
				                beyond_order_id:false,
	            				balance:response.data.data.balance, 
								loading:false,
								something_went_wrong:false });
                  	 $('.show_more_orders').text(this.state.language.text_show_more_order);
                  }
                  else
                  {
                  	this.setState({ payment_methods: response.data.payment_methods,
                  		        orders: response.data.data.orders,
                  		        account_summary: response.data.data.customer_summary,
                  	 	        important_note: response.data.important_note,
				                beyond_order_id:response.data.data.beyond_order_id,
	            				balance:response.data.data.balance, 
								loading:false,
								something_went_wrong:false });
                  }
			  } else {
				this.setState({ payment_methods: response.data.payment_methods,
					            orders: false,
					            account_summary: false,
					            important_note:false,
								loading:false,
								something_went_wrong:false})
			  }
			}
			else
			{
			  this.setState({	loading:false, 
							something_went_wrong:true});
			}  

		})
		.catch(error => {
			console.log("error");
			this.setState({	loading:false, 
							something_went_wrong:true})
		});
	}

	getOrderLevelAccountSummaryPagination(encode)
	{   
    	axios({
	          method:'GET',
	          url:'./api/customer_account/accounts/getOrderLevelAccountSummary&data='+encode,
	          dataType:'json'
	        })
			.then(response => {
				if(response.data.statusCode == 900){
					window.location.href = this.state.logout;
					return false;
				}

			   if(response.data.statusCode == 200)
			   {
	          	   if((Object.keys(response.data.data).length) > 0)
	          	   {
	            	let new_order_data = response.data.data.orders;
	            	let old_data = this.state.orders;
	            	let marging_data = [...old_data,...new_order_data];

	            	this.setState({ orders:marging_data,
	            					beyond_order_id:response.data.data.beyond_order_id,
	            					balance:response.data.data.balance,
	            					something_went_wrong:false});

	            	$('.show_more_orders').text(this.state.language.text_show_more_order);
	            	} else {
	          		$('.show_more_orders').text(this.state.language.text_show_more_order);
	            	this.setState({beyond_order_id:'',
	            					something_went_wrong:false })
	            	}
	           }
	           else
	           {
	           	 $('.show_more_orders').text(this.state.language.text_show_more_order);
	            	this.setState({beyond_order_id:'',
	            					something_went_wrong:true });
	           }	
									          
	        })
	        .catch(error => {
				return false;
			});
	}

	getOrderBreakupLevelAccountSummary(encode)
	{   
      axios({
			method:'GET',
			url:'./api/customer_account/accounts/getOrderLevelBreakupAccountDetails&data='+encode,
			dataType:'json'
		})
		.then(response => {
			if(response.data.statusCode == 900){
				window.location.href = this.state.logout;
				return false;
			}
			if(response.data.statusCode == 200)
			{
				if((Object.keys(response.data.data.breakup).length) > 0)
	          	   {
		    	      this.setState({ orders_breakup: response.data.data.breakup,
		    	      	    breakup_debit: response.data.data.total_debit,
		    	      	    breakup_credit: response.data.data.total_credit,
		    	      	    breakup_balance: response.data.data.balance,
		    	      	    breakup_is_positive_bal: response.data.data.is_positive_bal,
							orders_breakup_page:true,
							loading:false,
							something_went_wrong:false });
		    	    }
		    	    else
		    	    {
		    	      this.setState({ orders_breakup: false, 
		    	      	        breakup_debit:false,
		    	      	        breakup_credit:false,
		    	      	        breakup_balance:false,
		    	      	        breakup_is_positive_bal:false,
		    	      	        orders_breakup_page:true,
								loading:false,
								something_went_wrong:false })	
		    	    }  
		    }
		    else
		    {
		        this.setState({
		       	            orders_breakup: false,
		       	            breakup_debit:false,
		    	      	    breakup_credit:false,
		    	      	    breakup_balance:false,
		    	      	    breakup_is_positive_bal:false,
		       	            orders_breakup_page:true,
							loading:false,
							something_went_wrong:true });
			}	
		
		})
		.catch(error => {
			this.setState({	loading:false, 
							something_went_wrong:true})
		});
	}

	downloadReport(encode)
	{
		window.location.href = './api/customer_account/accounts/downloadCsvForCustomerAccountsStatement&data='+encode;
      /*axios({
			method:'GET',
			url:'./api/customer_account/accounts/downloadCsvForCustomerAccountsStatement&data='+encode,
			dataType:'json'
		})
		.then(response => {
			if(response.data.statusCode == 900){
				window.location.href = this.state.logout;
				return false;
			}
			if(response.data.statusCode == 200)
			{
			  window.location.href = response.data.data.file_link;	
		    }
		    else
		    {
		       alert(response.data.message);
			}	
		
		})
		.catch(error => {
			this.setState({	loading:false, 
							something_went_wrong:true})
		});*/

	}

    addFilterInHistoryState(filter_search)
    {
  		let baseUrl = [location.protocol, '//', location.host, location.pathname].join('')+'?route=account/statement';
  		var filter_string 	= '';

  		$.map(filter_search, function(filter, index) {
  			if(filter)
  			{
               filter_string   = filter_string+"&"+index+"=" + filter;
  			}
         
        });	
       	window.history.pushState('', null, baseUrl+filter_string);
   	} 

   	getUrlParameter(sParam)
   	 {
       var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    } 	

	render(){
		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						   <div className="order_list_block">
			             	<div className="col-sm-3 customer_account_section">
		     	          	<LeftSection 
		     	          	   active="account_statement"
		     				   language={this.props.language} 
		     				    />
			             	</div>
			            	<div className="col-sm-9 my_account_page" style={{paddingTop:'10px'}}>
			   		        <RightSection 
			   			      language={this.props.language} 
			   			       loading={this.state.loading}
			   			       something_went_wrong={this.state.something_went_wrong}
			   			       orders={this.state.orders}
			   			       account_summary={this.state.account_summary}
			   			       popular_tags={this.state.popular_tags}
			   			       filter_search={this.state.filter_search}
			   			       clearSearch={this.clearSearch}
			   			       showMoreOrders={this.showMoreOrders}
			   			       searchChangeValue={this.searchChangeValue}
			   			       searchChangeDateValue={this.searchChangeDateValue}
			   			       getStatementList={this.getStatementList}
			   			       beyond_order_id={this.state.beyond_order_id}
			   			       orders_breakup_page={this.state.orders_breakup_page}
			   			       orders_breakup={this.state.orders_breakup}
			   			       breakup_order_no={this.state.filter_search ? this.state.filter_search.breakup_order_no: ''}
			   			       getOrderDetailPage={this.getOrderDetailPage}
			   			       breakup_debit={this.state.breakup_debit}
			   			       breakup_credit={this.state.breakup_credit}
			   			       breakup_balance={this.state.breakup_balance}
			   			       breakup_is_positive_bal={this.state.breakup_is_positive_bal}
			   			       important_note={this.state.important_note}
			   			       payment_methods={this.state.payment_methods}
			   		         />
			            	</div>
			              </div>
						</section>
					</div>
				 </div>
		       )
	}
}