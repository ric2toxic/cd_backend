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
        this.toggleCollapse = this.toggleCollapse.bind(this);
    }
	clearCart(event){
             event.stopPropagation();
		var modal_body = "Would you like to move all items(from "+this.props.city+" location) into wish list?";
        var modal_footer = '<button type="button" data-dismiss="modal" class="btn deliver_btn move_to_wishlist" id="move_pickup_city_cart">Move to Wish List</button>'+
            '<button type="button" data-dismiss="modal" class="btn deliver_btn remove_city_items" id="clear_pickup_city_cart">Clear</button>'+
            '<button type="button" data-dismiss="modal" class="btn popup_close_btn">Cancel</button>';
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

	toggleCollapse(){
	    $('#shipment_'+this.props.city).collapse("toggle");
    }

    render() {
        return (
            <div className="odd gradeA col-sm-12 nopadding">
                <div className="cart_table_title_seller" onClick={this.toggleCollapse}>
                    <h3>{this.props.language.text_pickup_city}{this.props.city}
                    &ensp;<a className="btn btn_clear_cart" data-toggle="tooltip" style={{position:'relative',left:'8px',top:'0px',padding: '5px 7px'}} onClick={this.clearCart} title={"Remove all items from "+this.props.city+" location"}><i className="fa fa-trash-o" aria-hidden="true"></i></a>
                    </h3>
                    <i className="fa fa-angle-down arr_down pull-right pd_arrow_section" aria-hidden="true"></i>
                </div>
            </div>
        );
    }
}
