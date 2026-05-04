import "babel-polyfill"
import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { ConnectedRouter } from 'react-router-redux'
import store, { history } from './store'
import App from './containers/app'
import CookieDisable from './containers/loading/cookie_disable'
import custom from './custom/custom'
import 'sanitize.css/sanitize.css'
import './index.css'

import { logoData, wishlistData, cartData, userloginData, currencyData } from './actions/HeaderAction';
import { customstoreData, menuData, informationData } from './actions/MenuAction';
import {customer_typeData, country_listData} from './actions/LoginAction';

import registerServiceWorker from './registerServiceWorker';
var token = Math.random().toString(36).substr(2, 16);
if(!custom.getCookie('cart_session_id'))
{
  custom.createCookie('cart_session_id', token, 7);
}

if(!custom.getCookie('wishlist_session_id'))
{
  custom.createCookie('wishlist_session_id', token, 7);
}

const target = document.querySelector('#root');

store.dispatch(menuData());
store.dispatch(userloginData());
store.dispatch(logoData());
store.dispatch(wishlistData());
store.dispatch(cartData());
if(custom.getCookie('custom_store') === '') { store.dispatch(customstoreData(custom.getCookie('custom_store'))); }
store.dispatch(informationData());
store.dispatch(customer_typeData());
store.dispatch(country_listData());
store.dispatch(currencyData());


render(
  <Provider store={store}>
    <ConnectedRouter history={history}>
      {navigator.cookieEnabled === false ?
      	<CookieDisable />
      	:
        <App />
      }
    </ConnectedRouter>
  </Provider>,
  target
)

//registerServiceWorker();
