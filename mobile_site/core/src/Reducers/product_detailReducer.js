import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function product_detailReducer(state = initialState, action) {  

  switch(action.type) {
    case actionType.PRODUCT_SUCCESS:
      return {
        ...state,
        product_detail_id: action.product_detail.product_detail_id,
        product_detail: action.product_detail,
        product_detail_success: 1,
        } 
    case actionType.PRODUCT_RELATED_SUCCESS:
      return {
        ...state,
        related_products: action.related_products,
        }  
 case actionType.PRODUCT_ERROR:
      return {
        ...state,
        product_detail_id:false,
        product_detail:false,
        product_detail_success: -2,
        } 
  case actionType.QUESTION_POPUP:
      return {
        ...state,
        QuestionPopup:action.QuestionPopup
        }                           
    default: 
      return state;
  }
}