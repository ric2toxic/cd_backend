{/*
 * class PickupCity: This will render the pickup city
 * @Params: {
 *      city: pickup city,
 *      clearCart:Method to remove all products belong to this city
 *      language: language text for different fields
 *  }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
 */}

class PickupCity extends React.Component {

	constructor(props){
        super(props);
        this.clearCart = this.clearCart.bind(this);
    }
	clearCart(){
		var modal_body = "Are you sure to remove all items from "+this.props.city+" location?";
        var modal_footer = '<button type="button" data-dismiss="modal" class="btn deliver_btn move_to_wishlist" id="move_pickup_city_cart">Move to Wish List</button>'+
            '<button type="button" data-dismiss="modal" class="btn deliver_btn remove_city_items remove_items_alert" id="clear_pickup_city_cart">No,Clear</button>'+
            '<button type="button" data-dismiss="modal" class="btn popup_close_btn remove_items_alert">Cancel</button>';
        $('#confirm_body').html(modal_body);
        $('#confirm_footer').html(modal_footer);

        $('#confirm_popup').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('#clear_pickup_city_cart').click(function(){
            this.props.clearPickupCityCart(this.props.city);
        }.bind(this));

        $('#move_pickup_city_cart').click(function(){
            this.props.clearPickupCityCart(this.props.city,1);
        }.bind(this));
	}
    render() {
        return (
            <div className="col-xs-12 Shipping_teg">
                <h2>{this.props.language.text_pickup_city}{this.props.city}</h2>
                <button type="button" className="btn btn-estimate pull-right" onClick={this.clearCart} ><i className="fa fa-trash-o"></i></button>
            </div>
        );
    }
}
