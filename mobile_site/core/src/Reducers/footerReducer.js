import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function footerReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.FOOTER_POPULAR_TAGS:
      return {
        ...state,
        popular_tags: action.popular_tags
        }                       
    default: 
      return state;
  }
}