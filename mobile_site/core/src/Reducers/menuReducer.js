import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function menuReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.MENUS_CUSTOMSTORE_SUCCESS:
      return {
        ...state,
        custom_store: action.custom_store
        }  
    case actionType.MENUS_SUCCESS:
      return {
        ...state,
        menus: action.menus
        } 

    case actionType.MENUS_INFORMATION_SUCCESS:
      return {
        ...state,
        information: action.information
        }                     

    default: 
      return state;
  }
}