import Api from './Api'
import $ from 'jquery'

class CartApi { 

    static cart_add(formData) {
    return fetch(Api.api_url+'api/cart/add_item',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }


  static cart_add_with_option() 
  {
     return $.ajax({
      url: Api.api_url+'api/cart/addWithOptions',
      type: 'post',
      data: $('#product input[type=\'text\'], #product input[type=\'number\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea, #product #option-input'),
      dataType: 'json',
      complete: function() {
        $('body').removeClass('loading').addClass('loaded'); 
      },
      success: function(json) {
         return json;
      }
    });
  }

   static getCartData(customer_id, customer_access_token, cart_session_id, wishlist_session_id) {
    return fetch(Api.api_url+'api/cart/getCart&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&cart_session_id='+cart_session_id+'&wishlist_session_id='+wishlist_session_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static remove(formData) {
    return fetch(Api.api_url+'api/cart/remove',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static update(formData) {
    return fetch(Api.api_url+'api/cart/update',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

  static addComment(formData) {
    return fetch(Api.api_url+'api/cart/addComment',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static applyCoupon(formData) {
    return fetch(Api.api_url+'api/cart/applyCoupon',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }  

  static removeCoupon(formData) {
    return fetch(Api.api_url+'api/cart/removeCoupon',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static addToWishlist(formData) {
    return fetch(Api.api_url+'api/cart/addToWishlist',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
   } 

  static clearPickupCityCart(formData) {
    return fetch(Api.api_url+'api/cart/clearPickupCityCart',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
   } 

  static shippingMethod(formData) {
    
    return fetch(Api.api_url+'api/checkout/shippingMethod',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
   }


  static autoPopulateAddress(formData) {
  
    return fetch(Api.api_url+'api/address/autoPopulateAddress',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
   }

  static updateShippingMethod(formData) {
    return fetch(Api.api_url+'api/checkout/cartSummary',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
   }

  static getZones(country_id) {
    return fetch(Api.api_url+'api/checkout/country/'+country_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }   

}
export default CartApi;  