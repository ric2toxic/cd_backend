import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function headerReducer(state = initialState, action) {  
 
  switch(action.type) {
    
    case actionType.HEADER_LOGO_SUCCESS:
      return {
        ...state,
      	logo: action.logo
        }

    case actionType.HEADER_WISHLIST_SUCCESS:
      return {
        ...state,
      	wishlist: action.wishlist
        }    

    case actionType.HEADER_CART_SUCCESS:
      return {
        ...state,
      	cart: action.cart
        }

    case actionType.HEADER_USERLOGIN_SUCCESS:
      return {
        ...state,
      	userlogin: action.userlogin
        }  
    
    case actionType.HEADER_SEARCH_SUCCESS:
      return {
        ...state,
      	search_data: action.search_data
        }
    case actionType.HEADER_CURRENCY_SUCCESS:
      return {
        ...state,
        currency_data: action.currency_data
        }      
    default: 
      return state;
  }
}