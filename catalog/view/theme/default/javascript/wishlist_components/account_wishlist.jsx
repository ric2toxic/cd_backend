class AccountWishlist extends React.Component {
	
	constructor(props)
	{
		super(props);
		 this.state = {
		  fields: {},
          errors: {},
	      language: this.props.language,
	      international_store: this.props.international_store,
		    loading: true,
		    something_went_wrong: false,
		    logout:this.props.logout,
		    wishlist_data:false,
		    success_msg:false,
		    error_msg:false
       	}

       this.getWishlistData = this.getWishlistData.bind(this);
       this.remove          = this.remove.bind(this);	
	}

	componentDidMount()
	{
       
       this.getWishlistData();   
	}


   
	getWishlistData()
	{   
		var self = this;
	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/wishlist/getList&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

			if(response.data.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

			this.setState({ wishlist_data: response.data.data,
							loading:false,
						     something_went_wrong:false });
		});  
	}

	remove(remove_id)
	{
		var self = this;
	    var filters = '';
	    filters = filters+'remove='+remove_id;
        filters = filters+'&customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	
      axios({
			method:'GET',
			url:'./api/wishlist/remove&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

			if(response.data.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }
                    
			$("#wishlist-total-span").html(response.data.data.count);
			self.getWishlistData();
		});  
	}	

	render(){
    let self = this;

		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						   <div className="order_list_block">
			             	<div className="col-sm-3 customer_account_section">
		     	          	<LeftSection 
		     	          	   active="profile"
		     	          	   sub_active="My Wishlist"
		     				   language={this.props.language}
		     				    />
			             	</div>
			            	<div className="col-sm-9 my_account_page">
			   		        
			   		      <RightSection 
		     				    language={this.props.language}
		     				    loading={this.state.loading}
		     				    wishlist_data={this.state.wishlist_data}
		     				    remove={this.remove}
		     				   />
			            	</div>
			              </div>         

			</section>
		  </div>
		</div>
	)
 }
}