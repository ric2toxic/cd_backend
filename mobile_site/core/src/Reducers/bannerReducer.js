import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function bannerReducer(state = initialState, action) {  
 
  switch(action.type) {

    case actionType.BANNER_SUCCESS:
      return {
        ...state,
        banner: action.banner
        }  
    default: 
      return state;
  }
}