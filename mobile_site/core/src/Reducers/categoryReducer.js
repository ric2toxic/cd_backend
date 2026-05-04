import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function categoryReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.CATEGORY_PRODUCT_SUCCESS:
      return {
        ...state,
        path_keyword: action.product_list.path_keyword,
        parent_path_keyword: action.product_list.parent_path_keyword,
        selected_tab:action.product_list.selected_tab,
        category_data: action.product_list,
        product_list: action.product_list.products,
        product_count: action.product_list.products.length
        } 
     case actionType.CATEGORY_FILTER_SUCCESS:
      return {
        ...state,
        filter_facets:action.filter_list.filter_facets,
        rating_filter:action.filter_list.rating_filter,
        price_with_currency:action.filter_list.price_with_currency
        }    
    case actionType.CATEGORY_PRODUCT_PAGINATION_SUCCESS:
      return {
        ...state,
        category_data: action.product_list,
        product_list: state.product_list.concat(action.product_list.products),
        product_count: state.product_count+action.product_list.products.length,
        } 
    case actionType.CATEGORY_FILTER_PATH_SUCCESS:
      return {
        ...state,
        category_filter_path: action.filter_string
        } 
    case actionType.CATEGORY_PATH_KEYWORD_SUCCESS:
      return {
        ...state,
        path_keyword: action.path_keyword
        } 
    case actionType.CATEGORY_PRODUCT_ERROR:
      return {
        ...state,
        product_count: -2
        }                                
    default: 
      return state;
  }
}