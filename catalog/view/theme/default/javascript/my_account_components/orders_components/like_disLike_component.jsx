class LikeDisLikeComponent extends React.Component{

	constructor(props){
		super(props);
		
		this.clickOnLikeDisLikeFeature= this.clickOnLikeDisLikeFeature.bind(this);
	}

	clickOnLikeDisLikeFeature(like_dislike_url,ord_prod_id){
		axios({
			method:'GET',
			url:like_dislike_url+'&customer_access_token='+this.props.customer_access_token,
			type:'json'
		})
		.then(response=>{
			if(response.data.statusCode == 900){
				window.location.href = this.props.logout;
				return false;
			}
			if(response.data.data.review=='like'){
				$('#with_url_like_btn'+ord_prod_id).addClass('hidden');
				$('#without_url_like_btn'+ord_prod_id).removeClass('hidden');

				$('#with_url_dislike_btn'+ord_prod_id).removeClass('hidden');
				$('#without_url_dislike_btn'+ord_prod_id).addClass('hidden');

			}else if(response.data.data.review=='dislike') {
				$('#with_url_dislike_btn'+ord_prod_id).addClass('hidden');
				$('#without_url_dislike_btn'+ord_prod_id).removeClass('hidden');

				$('#with_url_like_btn'+ord_prod_id).removeClass('hidden');
				$('#without_url_like_btn'+ord_prod_id).addClass('hidden');

			}else {
				// nothing to do
			}
			
		});
	}
	render(){
		let db_product_review = (this.props.product_review ? this.props.product_review : '');
		return(
				<div className="suborder_product_like_dislike text-center">
						{
							(!db_product_review) ? <span className="product_right_thumbs_up"><button type="button" 
															className="btn btn-sm like_dislike_button"
															id={"with_url_like_btn"+this.props.ord_prod_id}
															onClick={()=>this.clickOnLikeDisLikeFeature(this.props.like_url,this.props.ord_prod_id)}
															>
														<i className="fa fa-thumbs-o-up green_color" id={"like_ord_prod_id_"+this.props.ord_prod_id} ></i> 
													</button>
													<button type="button" 
															className="btn btn-sm like_dislike_button hidden" 
															id={"without_url_like_btn"+this.props.ord_prod_id}
															disabled >
														<i className="fa fa-thumbs-up"></i> 
													</button>
													</span>
												: (db_product_review == 'like') ? <span className="product_right_thumbs_up">
																					<button type="button" 
																							className="btn btn-sm like_dislike_button hidden"
																							id={"with_url_like_btn"+this.props.ord_prod_id}
																							onClick={()=>this.clickOnLikeDisLikeFeature(this.props.like_url,this.props.ord_prod_id)}
																							>
																						<i className="fa fa-thumbs-o-up green_color" id={"like_ord_prod_id_"+this.props.ord_prod_id} ></i> 
																					</button>
																					<button type="button" 
																							className="btn btn-sm like_dislike_button" 
																							id={"without_url_like_btn"+this.props.ord_prod_id}
																							disabled >
																						<i className="fa fa-thumbs-up"></i> 
																					</button>
																				  </span>
																				: <span className="product_right_thumbs_up">
																					<button type="button" 
																							className="btn btn-sm like_dislike_button"
																							id={"with_url_like_btn"+this.props.ord_prod_id}
																							onClick={()=>this.clickOnLikeDisLikeFeature(this.props.like_url,this.props.ord_prod_id)}
																							>
																						<i className="fa fa-thumbs-o-up green_color" id={"like_ord_prod_id_"+this.props.ord_prod_id} ></i> 
																					</button>
																					<button type="button" 
																							className="btn btn-sm like_dislike_button hidden" 
																							id={"without_url_like_btn"+this.props.ord_prod_id}
																							disabled >
																						<i className="fa fa-thumbs-up"></i> 
																					</button>
																				  </span>
						}
						
						{
							(!db_product_review) ? <span className="product_right_thumbs_down">
													<button type="button" 
															className="btn btn-sm like_dislike_button"
															id={"with_url_dislike_btn"+this.props.ord_prod_id}
															onClick={()=>this.clickOnLikeDisLikeFeature(this.props.dislike_url,this.props.ord_prod_id)}>
														<i className="fa fa-thumbs-o-down red_color" id={"dislike_ord_prod_id_"+this.props.ord_prod_id} ></i> 
													</button>
													<button type="button" 
															className="btn btn-sm like_dislike_button hidden"
															id={"without_url_dislike_btn"+this.props.ord_prod_id}
															disabled>
														<i className="fa fa-thumbs-down"></i> 
													</button>
												  </span>
												 : (db_product_review == 'dislike') ? <span className="product_right_thumbs_down">
												 										<button type="button" 
																								className="btn btn-sm like_dislike_button hidden"
																								id={"with_url_dislike_btn"+this.props.ord_prod_id}
																								onClick={()=>this.clickOnLikeDisLikeFeature(this.props.dislike_url,this.props.ord_prod_id)}>
																							<i className="fa fa-thumbs-o-down red_color" id={"dislike_ord_prod_id_"+this.props.ord_prod_id} ></i> 
																						</button>	
												 										<button type="button" 
																								className="btn btn-sm like_dislike_button"
																								id={"without_url_dislike_btn"+this.props.ord_prod_id}
																								disabled>
																							<i className="fa fa-thumbs-down"></i> 
																						</button>
																					  </span>
																					: <span className="product_right_thumbs_down">
																						<button type="button" 
																								className="btn btn-sm like_dislike_button"
																								id={"with_url_dislike_btn"+this.props.ord_prod_id}
																								onClick={()=>this.clickOnLikeDisLikeFeature(this.props.dislike_url,this.props.ord_prod_id)}>
																							<i className="fa fa-thumbs-o-down red_color" id={"dislike_ord_prod_id_"+this.props.ord_prod_id} ></i> 
																						</button>
																						<button type="button" 
																								className="btn btn-sm like_dislike_button hidden"
																								id={"without_url_dislike_btn"+this.props.ord_prod_id}
																								disabled>
																							<i className="fa fa-thumbs-down"></i> 
																						</button>
																					  </span>
						}
						
				</div>
			  )
			
	}
}