class WishlistForDetailPage extends React.Component {

   constructor()
   {
   	super();
   	this.state = {
            is_slider: 0
        };
   } 
  
	render() { 
               return (
                    <button type="button" id={'wishlist_'+this.props.product_id} data-toggle="tooltip" className="addtowishlist wishlist_home_page" title="" onClick={() => wishlist_add(this.props.product_id, this, this.props)} data-original-title="Add to Wish List">
                        <i className={this.props.fill_heart} id={'wishlist_heart_'+this.props.product_id}></i>
                    </button>
                );
	}
}


