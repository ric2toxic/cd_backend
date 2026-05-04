import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { ConfirmPopupOpen } from '../../actions/CartAction';
import $ from 'jquery'

class PickupCity extends Component {
    
    constructor(props){
        super(props);
        this.toggleCollapse = this.toggleCollapse.bind(this);
        this.clearCart      = this.clearCart.bind(this);
    }

   clearCart(event){
        event.stopPropagation();
        var self = this;
        var city = this.props.city;
        this.props.dispatch(ConfirmPopupOpen(1));

         var modal_body = "Would you like to move all items(from "+this.props.city+" location) into wish list?";
         var modal_footer = '<button type="button" data-city="'+this.props.city+'" class="btn cart_btn move_to_wishlist" id="move_pickup_city_cart">Move to Wish List</button>'+
            ' <button type="button" data-city="'+city+'" class="btn cart_btn remove_city_items" id="clear_pickup_city_cart">Clear</button>'; 

        setTimeout(function(){ 
          $('#confirm_body').html(modal_body);
          $('#confirm_footer').html(modal_footer);
        }, 200);

        $(document).delegate('#clear_pickup_city_cart', 'click', function(e)
        { 
          city = $(this).attr("data-city");
          self.props.dispatch(ConfirmPopupOpen(0));  
          self.props.clearPickupCityCart(city);
        });
        
        $(document).delegate('#move_pickup_city_cart', 'click', function(e)
        { 
          city = $(this).attr("data-city");
          self.props.dispatch(ConfirmPopupOpen(0));  
          self.props.clearPickupCityCart(city,1);
        });
    }

    toggleCollapse(){
        $('#shipment_'+this.props.city).toggleClass("in");
    }

    render() {
        return (
            <div className="col-xs-12 Shipping_teg"  onClick={this.toggleCollapse}>
                           <h2>{this.props.language.text_pickup_city}{this.props.city}</h2>
                            <button type="button" className="btn btn-estimate pull-right" onClick={this.clearCart}><i className="fa fa-trash-o"></i></button> 
            </div>
        );
    }
}

function mapStateToProps(state){
  return {
    actions: bindActionCreators(ConfirmPopupOpen)
  };
}

export default connect(mapStateToProps)(PickupCity);