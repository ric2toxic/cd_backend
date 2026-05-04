import * as actionType from './ActionType';
import HeaderApi from '../api/HeaderApi';
import custom from '../custom/custom'

export function logoData() {  
  return function(dispatch) {
    return HeaderApi.logo().then(response => {
      dispatch(HeaderLogoSuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}

export function wishlistData() { 
  
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  var wishlist_session_id = custom.getCookie('wishlist_session_id');  
  return function(dispatch) {
    return HeaderApi.wishlist(customer_id, customer_access_token, wishlist_session_id).then(response => {
      dispatch(HeaderWishlistSuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}

export function cartData() {  
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  var cart_session_id = custom.getCookie('cart_session_id');
  return function(dispatch) {
    return HeaderApi.cart(customer_id, customer_access_token, cart_session_id).then(response => {
      dispatch(HeaderCartSuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}

export function userloginData() { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token'); 
  var country = custom.getUrlParameter('country');
  return function(dispatch) {
    return HeaderApi.userlogin(customer_id, customer_access_token, country).then(response => {
       custom.createCookie('user_country', response.data.user_country, 7);
       if(response.data.is_redirect)
       {
         window.location.href = response.data.redirect_url;
       }

       dispatch(HeaderUserloginSuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}

export function currencyData() {  
  return function(dispatch) {
    return HeaderApi.currency().then(response => {
       dispatch(HeaderCurrencySuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}

export function setCurrencySession(currency) {  
  return function(dispatch) {
    return HeaderApi.setCurrencySession(currency).then(response => {
       return response.data;
    }).catch(error => {
      throw(error);
    });
  };
}

export function searchAutocompleteData(token) { 
  return function(dispatch) {
    return HeaderApi.searchAutocomplete(token).then(response => {
        /*var data=[];
        for(var i=0;i<response.length;i++){
            data[i]= response[i]['value'];
        }*/
       dispatch(HeaderSearchSuccess(response));
    }).catch(error => {
      throw(error);
    });
  };
}


export function HeaderLogoSuccess(logo) {  
  return {type: actionType.HEADER_LOGO_SUCCESS, logo};
}

export function HeaderWishlistSuccess(wishlist) {  
  return {type: actionType.HEADER_WISHLIST_SUCCESS, wishlist};
}

export function HeaderCartSuccess(cart) {  
  return {type: actionType.HEADER_CART_SUCCESS, cart};
}

export function HeaderUserloginSuccess(userlogin) {  
  return {type: actionType.HEADER_USERLOGIN_SUCCESS, userlogin};
}

export function HeaderSearchSuccess(search_data) {  
  return {type: actionType.HEADER_SEARCH_SUCCESS, search_data};
}

export function HeaderCurrencySuccess(currency_data) {  
  return {type: actionType.HEADER_CURRENCY_SUCCESS, currency_data};
}
