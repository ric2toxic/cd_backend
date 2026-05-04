class ListWithUrl extends React.Component {
	
	constructor(props)	{
		super(props);		
	}


	render(){
		return (

		   	 	<div id={"collapse"+this.props.title} className="panel-collapse collapse" >
					<ul className="list-group">
						{
							this.props.sub_menu.map(function(menu, index){
								return(
										<a href={menu.url} className="">
											<li className="list-group-item ">
												{menu.title}
											</li>
										</a>
									)
							})
						}
					</ul>
				</div>
			)
	}
}