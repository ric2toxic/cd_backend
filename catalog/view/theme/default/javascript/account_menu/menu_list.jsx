class MenuList extends React.Component {
	
	constructor(props)	{
		super(props);		
	}


	render(){
		let self = this;
		return (

		   	 	<div id={"collapse"+this.props.title} className={"panel-collapse collapse " + this.props.active} >
					<ul className="list-group">
						{this.props.sub_menu ?
							this.props.sub_menu.map(function(menu, index){
								return(
									   <li className={self.props.sub_active == menu.title ? "list-group-item blue_bg_color" : "list-group-item " }>
										<a href={menu.url} className={self.props.sub_active == menu.title ? "white_color" : "" }>
												{menu.title}
										</a>
										</li>
									)
							})
						: ''}
					</ul>
				</div>
			)
	}
}