import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import headerReducer from './headerReducer';
import menuReducer from './menuReducer';
import bannerReducer from './bannerReducer';
import homeReducer from './homeReducer';
import informationReducer from './informationReducer';
import loginReducer from './loginReducer';
import categoryReducer from './categoryReducer';
import product_detailReducer from './product_detailReducer';
import cartReducer from './cartReducer';
import accountReducer from './accountReducer';
import footerReducer from './footerReducer';

export default combineReducers({
  router: routerReducer,
  headerReducer,
  menuReducer,
  bannerReducer,
  homeReducer,
  informationReducer,
  loginReducer,
  categoryReducer,
  product_detailReducer,
  cartReducer,
  accountReducer,
  footerReducer
})
