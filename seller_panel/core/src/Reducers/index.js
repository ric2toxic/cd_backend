import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import headerReducer from './headerReducer';
import footerReducer from './footerReducer';
import profileReducer from './profileReducer';

export default combineReducers({
  router: routerReducer,
  headerReducer,
  footerReducer,
  profileReducer
})
