import Api from './Api'

class HeaderApi { 

  static logo() {
    return fetch(Api.api_url+'api/header/logo').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static wishlist(customer_id, customer_access_token, wishlist_session_id) {
    return fetch(Api.api_url+'api/header/wishlist&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&wishlist_session_id='+wishlist_session_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static cart(customer_id, customer_access_token, cart_session_id) {
    return fetch(Api.api_url+'api/header/cart&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&cart_session_id='+cart_session_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static userlogin(customer_id, customer_access_token, country = '') {
    return fetch(Api.api_url+'api/header/userloginMobile&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&country='+country).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

  static currency() {
    return fetch(Api.api_url+'api/header/currencyList').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static setCurrencySession(currency) {
    return fetch(Api.api_url+'api/header/setCurrencySession&currency='+currency).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static searchAutocomplete(token) {
    return fetch(Api.api_url+'api/header/autoComplete&term='+token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

}
export default HeaderApi;  