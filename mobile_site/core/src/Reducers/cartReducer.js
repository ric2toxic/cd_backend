import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function cartReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.CART_SUCCESS:
      return {
        ...state,
        checkout_page: action.cart_data.checkout,
        cart_products: action.cart_data.cart_data.products,
        cart_total_sets: action.cart_data.cart_data.total_sets,
        cart_total_pieces: action.cart_data.cart_data.total_pieces,
        cart_summary: action.cart_data.cart_summary,
        clear_cart: action.cart_data.cart_data.clear_cart,
        cartlimitcross: action.cart_data.cart_data.cartlimitcross,
        cart_weight: action.cart_data.cart_data.weight,
        cart_is_empty: action.cart_data.is_empty,
        cart_language: action.cart_data.language,
        cart_customer: action.cart_data.customer,
        cart_coupon: action.cart_data.coupon,
        surface_shipping:action.cart_data.surface_shipping,
        cart_api_suceess: 1
        }
    case actionType.CART_UPDATE_SUCCESS:
      return {
        ...state,
        cart_products: action.cart_data.products,
        cart_total_sets: action.cart_data.total_sets,
        cart_total_pieces: action.cart_data.total_pieces,
        clear_cart: action.cart_data.clear_cart,
        cartlimitcross: action.cart_data.cartlimitcross,
        cart_weight: action.cart_data.weight,
        cart_is_empty: action.cart_data.empty,
        cart_customer: action.cart_data.customer,
        cart_coupon: action.cart_data.coupon,
        surface_shipping:action.cart_data.surface_shipping,
        cart_summary: {totals: action.cart_data.totals, 
                       disable_place_order: action.cart_data.disable_place_order,
                       show_cform_option: action.cart_data.show_cform_option,
                       tax_refund: action.cart_data.tax_refund,
                       text_cart_minimum: action.cart_data.text_cart_minimum,
                       total_out_of_stock_products: action.cart_data.total_out_of_stock_products,
                       total_quantity_reduced_products: action.cart_data.total_quantity_reduced_products },
        cart_api_suceess: 1
        }
    case actionType.CART_UPDATE_SHIPPPING_SUCCESS:
      return {
        ...state,
        cart_summary: {totals: action.cart_data.totals, 
                       tax_refund: action.cart_data.tax_refund
                       },
        cart_api_suceess: 1
        }        
    case actionType.CART_ERROR:
      return {
        ...state,
        cart_api_suceess: 0
        } 
    case actionType.CART_CONFIRM_POPUP:
      return {
        ...state,
        ConfirmPopup: action.ConfirmPopup
        }
    case actionType.CART_ESTIMATE_POPUP:
      return {
        ...state,
        EstimatePopup: action.EstimatePopup
        }                                           
    default: 
      return state;
  }
}