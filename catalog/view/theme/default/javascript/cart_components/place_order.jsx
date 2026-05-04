{/*
 * class PlaceOrder: This will render the Place Order div in cart summary
 * @Params: {
 *      cart_summary: cart summary,
 *      cart_error_show:{1: disable the place order button,0:enable the place order button}
 *      language: language text for different fields
 *  }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
 */}

class PlaceOrder extends React.Component{

    componentDidMount(){

    }
    render(){
        var disabledbutton = {
            backgroundColor:'grey'
        };

        return(
            <div>
                { parseInt(this.props.cart_summary.disable_place_order) == 1 ? (
                    <div>
                        <button type="button" className="btn btn-checkout" id="place_order_button" data-parent="#accordion" style={disabledbutton} disabled>PLACE ORDER</button>
                    </div>
                ):(
                    <div>
                        <button type="button" className="btn btn-checkout" onClick={this.props.placeOrder} id="place_order_button"  data-parent="#accordion" >PLACE ORDER</button>
                    </div>
                )}
            </div>
        );
    }
}
