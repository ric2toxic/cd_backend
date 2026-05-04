import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function informationReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.INFORMATION_ABOUT_SUCCESS:
      return {
        ...state,
        about_us: action.about_us
        } 
    case actionType.INFORMATION_POLICY_SUCCESS:
      return {
        ...state,
        policies: action.policies
        } 
    case actionType.INFORMATION_SUCCESS:
      return {
        ...state,
        information_data: action.information_data
        }
    case actionType.STORELOCATOR_SUCCESS:
      return {
        ...state,
        storelocator: action.storelocator
        }       
    case actionType.INFORMATION_CONTACT_SUCCESS:
      return {
        ...state,
        contact_us: action.contact_us
        } 
    case actionType.DIALOG_OPEN:
      return {
        ...state,
        dialog_open: action.dialog_open
        }
    case actionType.SELLER_AGREEMENT_DATA:
      return {
        ...state,
        seller_agreement_data: action.seller_agreement_data
        }      
                            
    default: 
      return state;
  }
}