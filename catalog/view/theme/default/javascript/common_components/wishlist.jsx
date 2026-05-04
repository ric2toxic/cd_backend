class Wishlist extends React.Component {
    
   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

   componentDidMount()
  {
    axios({
    method:'get',
    url:'./api/header/wishlist',
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data})
   });
  }


	render() {  
   if(this.state.data.logged)
   {

    return (
         <a href={this.state.data.wishlist} id="wishlist-total" title="Wish List">
         <div className="header_heart">
          <span id="wishlist-total-span" className="cart_amount">{this.state.data.wishlist_total}</span>
        </div>
        </a>
    );

   }
  else
  {
     return (
         <a href="javascript:;" data-toggle="modal" data-target="#login_verify_popup" id="wishlist-total" title="Wish List">
         <div className="header_heart">
           <span className="cart_amount" id="wishlist-total-span">{this.state.data.wishlist_total}</span>
        </div>
         </a>
    );

  } 
		
	}
}
