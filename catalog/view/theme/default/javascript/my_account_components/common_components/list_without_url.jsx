class ListWithoutUrl extends React.Component {
	
	constructor(props)
	{
		super(props);		
	}

	render(){
		let self = this;
		return (
				<div id={"collapse"+this.props.title} className="panel-collapse collapse">
					<ul className="list-group">
						{
							this.props.sub_menu.map(function(submenu, index){
								if(self.props.order_type_title==''){ self.props.order_type_title = self.props.default_ord_type_title;}
								return(
										<li className={"list-group-item "+ (self.props.order_type_title == submenu.title ? "blue_bg_color" : "")} 
											data-filter-order-type={submenu.filter_order_type} 
				  							onClick={()=>self.props.clickOnFilterOrderType(submenu.filter_order_type,submenu.title)} >
											<a href="javascript:void(0);" className={(self.props.order_type_title == submenu.title ? "white_color" : "")}>{submenu.title}</a>
										</li>
									)
							})
						}
					</ul>
				</div>
		)
	}
}