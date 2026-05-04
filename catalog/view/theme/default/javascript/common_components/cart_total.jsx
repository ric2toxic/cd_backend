
class CartTotal extends React.Component {

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
    url:'./api/header/cart',
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data})
   });
  }

	render() {
		return (
        <a href="javascript:;" href={this.state.data.cart_url} id="cart_btn" title="Cart"> 
			  <div className="header_cart"><span className="cart_amount" id="cart-total">{this.state.data.total_in_cart}</span></div>
        </a>
		);
	}
}
