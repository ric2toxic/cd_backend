class SuborderProductListComponent extends React.Component{

	constructor(props){
		super(props);
	}
	

	render(){
		let self = this;
		return(
				<div className="suborder_product_list">
				{

					$.map(this.props.suborder_product_data, function(suborder_product, index) {
						return (
									<div className="col-sm-12 suborder_product_list_block">
										<div className="suborder_products">
											<div className="col-sm-2">
												<div className="product_image text-center">
													<a href={suborder_product.link} target="_blank">
														<img src={suborder_product.image} 
															 alt={suborder_product.name} 
														     width={suborder_product.width} 
														     height={suborder_product.height}/>
													</a>	     
												</div>
											</div>
											<div className="col-sm-4">
												<h5 className="bold_content product_title">
													<a href={suborder_product.link} target="_blank">{suborder_product.name}</a>
												</h5>
												<p className="product_sku">
													<a href={suborder_product.link} target="_blank">
														{self.props.language.label_sku_code} {suborder_product.model}
													</a>	
												</p>
												<div className="product_comment">
													{
														suborder_product.comment ? <fieldset>
																						<legend>{self.props.language.label_comment}</legend>
																						<span>{suborder_product.comment}</span>
																					</fieldset>
																				 : ''	
													}
													
												</div>
											</div>
											<div className="col-sm-3 no_padding">
												<div className="suborder_prod_amt_breakup">
													<ul>
														{
															$.map(suborder_product.amount_beakup,function(item, i){ 
																return ( <li> 
																			<span> {item.key} </span>
																			<span className="pull-right bold_content"> <RupeesSymbolWithAmountComponent amount={item.value} /> </span>
																		 </li> 
																	   )
															})
														}	
													</ul>
												</div>
											</div>
											<div className="col-sm-3">
												<ReorderComponent language={self.props.language}
																  reorder_url={suborder_product.reorder_link}
																  ord_prod_id={index}
																  customer_access_token={self.props.customer_access_token}
																  logout={self.props.logout}	 />
												
												<p className="suborder_product_list_button">
													<a href={suborder_product.download_images} 
														className="btn btn-sm suborder_list_button_download_img green_color white_bg_color">
															<i className="fa fa-download"></i> {self.props.language.text_downlaod_img}
													</a>
												</p>
												
												{
													self.props.show_like_dislike_btn 
														?	<LikeDisLikeComponent like_url={suborder_product.like_link} 
																	  dislike_url={suborder_product.dislike_link}
																	  product_review={suborder_product.product_review}
																	  ord_prod_id={index}
																	  customer_access_token={self.props.customer_access_token}
																	  logout={self.props.logout}/>
														: ''
												}
												
											</div>
										</div>
									</div>	

								);
					})
				}
					
				</div>
			  )
			
	}
}