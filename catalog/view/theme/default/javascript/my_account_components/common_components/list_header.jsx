class ListHeader extends React.Component {
	
	constructor(props)
	{
		super(props);		
	}

	render(){

function url_link(self)
{
   if(self.props.url.indexOf('logout') != -1)
   {	
     dataLayer.push({'event': 'we-custom-logout'});
     setTimeout(function(){ window.location.assign(self.props.url); }, 500);
   }
   else
   {
   	 window.location.assign(self.props.url);
   }  
}


		return (
				
		  		this.props.url=='' ? <div className="panel-heading panel_block" data-toggle="collapse" data-title-value={this.props.title} href={"#collapse"+this.props.title}>
					  					<h4 className="panel-title"> 
								    		<i className={"blue_color " + this.props.icon}> </i> 
							        		<span className="panel_title">{this.props.title} </span>
			                                   <span className="toggle_caret pull-right">
								        		<i className="fa fa-angle-right"></i>
						        			</span>
								      	</h4>
								    </div>
				  				  : <div className="panel-heading panel_block" data-title-value={this.props.title}>
								    	<a href="javascript:;" onClick={()=>url_link(this)}>
					  				  		<h4 className="panel-title"> 
									    		<i className={"blue_color " + this.props.icon}> </i> 
									        		<span className="panel_title">{this.props.title} </span>
									      	</h4>
							        	</a>
								    </div>
			  	
		)
	}
}