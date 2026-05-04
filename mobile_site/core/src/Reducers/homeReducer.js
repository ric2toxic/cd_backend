import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function homeReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.HOME_CATEGORY_SUCCESS:
      return {
        ...state,
        category_list: action.category_list
        }
     case actionType.HOME_LATEST_SUCCESS:
      return {
        ...state,
        latest_product: action.latest_product
        }
     case actionType.HOME_TRENDING_SUCCESS:
      return {
        ...state,
        trending_product: action.trending_product
        }             
    default: 
      return state;
  }
}