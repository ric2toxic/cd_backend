class MenuHeader extends React.Component {
	
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


       if(this.props.url == '')
       {
         return(<div className={"panel-heading panel_block collapsed "+this.props.active} data-toggle="collapse" data-title-value={this.props.title} href={"#collapse"+this.props.title}>
       
					  					<h4 className="panel-title"> 
					  					    {this.props.active ?
								    		<i className={ this.props.icon}> </i> 
								    		: 
								    	    <i className={"blue_color " + this.props.icon}> </i> 
								    	}
							        		<span className="panel_title">{this.props.title} </span>
			                                   <span className="toggle_caret pull-right">
								        		<i className="fa fa-angle-right"></i>
						        			</span>
								      	</h4>
								    </div>)
		}
		else
		{						    
		  return ( <div className={"panel-heading panel_block "+this.props.active} data-title-value={this.props.title}>
								    	{this.props.url.indexOf('logout') != -1 ?
								    	<a href="javascript:;" onClick={()=>url_link(this)} style={this.props.active ? {color:'#fff'} : {}}>
					  				  		<h4 className="panel-title"> 
									    		<i className={this.props.active ? this.props.icon : "blue_color " + this.props.icon}> </i> 
									        		<span className="panel_title">{this.props.title} </span>
									      	</h4>
							        	</a>
							        	:
							        	<a href={this.props.url} style={this.props.active ? {color:'#fff'} : {}}>
					  				  		<h4 className="panel-title"> 
									    		<i className={this.props.active ? this.props.icon : "blue_color " + this.props.icon}> </i> 
									        		<span className="panel_title">{this.props.title} </span>
									      	</h4>
							        	</a>
							            }

								    </div>)
		}						    
	}
}