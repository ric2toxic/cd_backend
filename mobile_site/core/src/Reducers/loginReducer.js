import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function loginReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.LOGIN_CUSTOMER_TYPE_SUCCESS:
      return {
        ...state,
        customer_type: action.customer_type
        } 
    case actionType.LOGIN_COUNTRY_SUCCESS:
      return {
        ...state,
        country_list: action.country_list
        }
    case actionType.OPT_POPUP:
      return {
        ...state,
        OptPopup: action.OptPopup
        } 
    case actionType.LOGIN_POPUP:
      return {
        ...state,
        LoginPopup: action.LoginPopup
        }
    case actionType.REGISTER_POPUP:
      return {
        ...state,
        RegisterPopup: action.RegisterPopup
        }
    case actionType.SUCCESS_POPUP:
      return {
        ...state,
        SuccessPopup: action.SuccessPopup
        }
    case actionType.DESIGN_POPUP:
      return {
        ...state,
        DesignPopup: action.DesignPopup
        }                              
    default: 
      return state;
  }
}