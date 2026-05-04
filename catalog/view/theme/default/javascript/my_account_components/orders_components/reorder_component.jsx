class ReorderComponent extends React.Component{

	constructor(props){
		super(props);
		this.state={
			loading:false
		}
		this.clickOnReorderButton = this.clickOnReorderButton.bind(this);
	}

	clickOnReorderButton(ord_prod_id){
		this.setState({loading:true});
		axios({
			method:'GET',
			url:this.props.reorder_url+'&customer_access_token='+this.props.customer_access_token,
			type:'json'
		})
		.then(response=>{
			this.setState({loading:false});

			if(response.data.statusCode == 900){
				window.location.href = this.props.logout;
				return false;
			}
			$('#suborder_prod_reorder'+ord_prod_id).removeClass('blue_bg_color').addClass('green_bg_color');
			$('#suborder_prod_reorder'+ord_prod_id).removeClass('blue_color').addClass('white_color');
			$('#suborder_prod_reorder'+ord_prod_id).addClass('reorder_now_hover');
			$("#my_account_notification").fadeIn("fast").html(this.props.language.text_status_success);
            $("#my_account_notification").fadeOut(2500);
			if(response.data){
				axios({
					method:'GET',
					url:'./api/header/cart',
					type:'json'
				})
				.then(data_response=>{
					$('#cart-total').text(data_response.data.data.total_in_cart);
					
				});	
			}			
		});
	}

	render(){
		return(
				<p className="suborder_product_list_button">
					<span className="btn btn-sm suborder_list_button blue_color white_bg_color"
						  id={"suborder_prod_reorder"+this.props.ord_prod_id}	
						  onClick={()=>this.clickOnReorderButton(this.props.ord_prod_id)}	>
						  {
						  	this.state.loading ? 'Loading...' : <span><i className="fa fa-reply"></i> {this.props.language.text_reorder_now}</span>
						  }							
					</span>
				</p>
			  )
			
	}
}