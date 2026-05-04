import * as actionType from './ActionType';
import CartApi from '../api/CartApi';
import custom from '../custom/custom'
import webengage from '../custom/webengage'
import $ from 'jquery'

export function cart_add(product_id, quantity, for_detail_page=0) { 
  
    $('body').removeClass('loaded').addClass('loading'); 
     var formData = new FormData();
     formData.append('product_id', product_id);
     formData.append('quantity', quantity);
     formData.append('customer_id', custom.getCookie('customer_id'));
     formData.append('customer_access_token', custom.getCookie('customer_access_token'));
     formData.append('cart_session_id', custom.getCookie('cart_session_id'));
     formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

   return function(dispatch) {
    return CartApi.cart_add(formData).then(response => {
      webengage.web_engage_product_detail(product_id, quantity, 'we-custom-addtocart');
      $('body').removeClass('loading').addClass('loaded');
      if(for_detail_page === 0){
                 var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Added to cart successfully</p></div></div>';
                  $(document.body).append(success_div);   
                  $('#notification').fadeOut(2000);
                   setTimeout(function() {
                    $('#notification').remove();
                    }, 2000);
                }
      }).catch(error => {
      //dispatch(CartError(error));
      });
    };  

}

export function cart_add_with_option() {

    $('body').removeClass('loaded').addClass('loading');
      var for_detail_page = $("#for_detail_page").val();
      var customer_id     = custom.getCookie("customer_id");
      var customer_access_token = custom.getCookie("customer_access_token");
      var cart_session_id = custom.getCookie("cart_session_id");
      var wishlist_session_id = custom.getCookie("wishlist_session_id");

     
      $("#customer_id").val(customer_id);
      $("#customer_access_token").val(customer_access_token);
      $("#cart_session_id").val(cart_session_id);
      $("#wishlist_session_id").val(wishlist_session_id);

      var product_id = parseInt($('#product input[name=\'product_id\']').val(), 10);
      var quantity = 0;

      $('#product input[type=\'text\']').each(function( index ) {
         quantity = parseInt($( this ).val(), 10) + parseInt(quantity, 10);
      });

   return function(dispatch) {
    return CartApi.cart_add_with_option().then(response => {
     
        if(response.error)
        {  
            $("#for_detail_page").val('0');
           var success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <hr class="message-inner-separator"><p><strong>Error </strong>please select atleast one option</p></div></div>';
           $(document.body).append(success_div);
           $('#notification').fadeOut(2000); 
           setTimeout(function() {
           $('#notification').remove();
           }, 2000);
        }
        else
        { 
          webengage.web_engage_product_detail(product_id, quantity, 'we-custom-addtocart');
          if(for_detail_page === '0')
          {
            success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Added to cart successfully</p></div></div>';
            $(document.body).append(success_div);   
            $('#notification').fadeOut(2000);
            setTimeout(function() {
                $('#notification').remove();
            }, 2000);
          }

          $("#for_detail_page").val('0'); 

        }

      }).catch(error => {
      //dispatch(CartError(error));
      });
    };  

}

export function getCartData() { 
    var customer_id     = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token');
    var cart_session_id = custom.getCookie('cart_session_id'); 
    var wishlist_session_id = custom.getCookie('wishlist_session_id'); 

  return function(dispatch) {
    return CartApi.getCartData(customer_id, customer_access_token, cart_session_id, wishlist_session_id).then(response => {
      dispatch(CartSuccess(response));
    }).catch(error => {
      dispatch(CartError(error));
    });
  };
}

export function remove(key, product_id=0) { 

    $('body').removeClass('loaded').addClass('loading');

    var formData = new FormData();
    formData.append('key', key);
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    formData.append('cart_session_id', custom.getCookie('cart_session_id'));
    formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));
    
  return function(dispatch) {
    return CartApi.remove(formData).then(response => {

     webengage.web_engage_product_detail(product_id, 0, 'we-custom-removefromcart');

     $('body').removeClass('loading').addClass('loaded');
     var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Item remove to cart successfully</p></div></div>';
                  $(document.body).append(success_div);   
                  $('#notification').fadeOut(2000);
                   setTimeout(function() {
                    $('#notification').remove();
                    }, 2000);

     if(response.status === 'error') { alert(response.error_msg); }
     else {  dispatch(CartUpdateSuccess(response)); return response; }             

    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function update(key, quantity, product_id=0) {  

    $('body').removeClass('loaded').addClass('loading');

    var formData = new FormData();
        formData.append('key', key);
        formData.append('quantity', quantity);
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
        formData.append('cart_session_id', custom.getCookie('cart_session_id'));
        formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.update(formData).then(response => {
     
     webengage.web_engage_product_detail(product_id, quantity, 'we-custom-addtocart');

     $('body').removeClass('loading').addClass('loaded');
     var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Cart update successfully</p></div></div>';
                  $(document.body).append(success_div);   
                  $('#notification').fadeOut(2000);
                   setTimeout(function() {
                    $('#notification').remove();
                    }, 2000);

     if(response.status === 'error') { alert(response.error_msg); }
     else {  dispatch(CartUpdateSuccess(response)); return response; }

    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function addComment(key, comment) { 

    var formData = new FormData();
        formData.append('key', key);
        formData.append('comment', comment);
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
        formData.append('cart_session_id', custom.getCookie('cart_session_id'));
        formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.addComment(formData).then(response => {
     if(response.status === 'error') { alert(response.error_msg); }
     else {  dispatch(CartUpdateSuccess(response)); return response; }
    }).catch(error => {
      dispatch(CartError(error));
    });
  };
}

export function applyCoupon(coupon) { 
    var formData = new FormData();
        formData.append('coupon', coupon);
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
        formData.append('cart_session_id', custom.getCookie('cart_session_id'));
        formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.applyCoupon(formData).then(response => {
    
     $('body').removeClass('loading').addClass('loaded');
     
     if(response.status === 'error') 
      { 
        alert(response.error_msg); 
      }
     else if(response.error)
      {
       $('.promo_code_penal').after('<div id="alert-danger" class="alert alert-danger" style="margin-top: 5px;"><i class="fa fa-exclamation-circle"></i> ' + response.error + '<button type="button" class="close">&times;</button></div>'); 
       $('#alert-danger').fadeOut(2000);
       setTimeout(function() {
          $('#alert-danger').remove();
       }, 2000);
      }
     else 
     {
       dispatch(CartUpdateSuccess(response)); return response; 
     }

    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function removeCoupon() { 

  $('body').removeClass('loaded').addClass('loading');

    var formData = new FormData();
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
        formData.append('cart_session_id', custom.getCookie('cart_session_id'));
        formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.removeCoupon(formData).then(response => {
     $('body').removeClass('loading').addClass('loaded');
     if(response.status === 'error') { alert(response.error_msg); }
     else {  dispatch(CartUpdateSuccess(response)); return response; }
    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function addToWishlist(product_id) {
    $('body').removeClass('loaded').addClass('loading');
         var formData = new FormData();
        formData.append('product_id', product_id);
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
        formData.append('cart_session_id', custom.getCookie('cart_session_id'));
        formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id')); 

  return function(dispatch) {
     return CartApi.addToWishlist(formData).then(response => {

    webengage.web_engage_product_detail(product_id, 0, 'we-custom-addtowishlist');

     $('body').removeClass('loading').addClass('loaded');
      $("#wishlist_heart_"+product_id).children("i").removeClass('custom_heart').addClass('custom_heart');
      var success_div = '';
      if (response['info']){
            success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+ response['info']+'</p></div></div>';
            $(document.body).append(success_div);
        }

      if (response['success']){
            success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+ response['success']+'</p></div></div>';
            $(document.body).append(success_div);
        }  

        $('#notification').fadeOut(2000);
          setTimeout(function() {
           $('#notification').remove();
          }, 2000);

     if(response.status === 'error') { alert(response.error_msg); }
     else {  return response; }

    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function clearPickupCityCart(keys, add_to_wishlist, products, city) { 

    $('body').removeClass('loaded').addClass('loading');

    var formData = new FormData();
    formData.append('keys', keys); 
    formData.append('add_to_wishlist', add_to_wishlist);
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    formData.append('cart_session_id', custom.getCookie('cart_session_id'));
    formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.clearPickupCityCart(formData).then(response => {
         
         Object.keys(products[city]).map((combo_product_key,key) =>
          {
             products[city][combo_product_key].map((item, key) =>
                {
                    if(add_to_wishlist === 1)
                    {
                      webengage.web_engage_product_detail(parseInt(item.product_id, 10), 0, 'we-custom-removefromcart', 'we-custom-addtowishlist');
                    }
                    else
                    {
                      webengage.web_engage_product_detail(parseInt(item.product_id, 10), parseInt(item.quantity, 10), 'we-custom-removefromcart');
                    }
                    return true;
                });
             return true;
           });
     
      $('body').removeClass('loading').addClass('loaded');
     var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Item remove to cart successfully</p></div></div>';
                  $(document.body).append(success_div);   
                  $('#notification').fadeOut(2000);
                   setTimeout(function() {
                    $('#notification').remove();
                    }, 2000);

     if(response.status === 'error') { alert(response.error_msg); }
     else {  dispatch(CartUpdateSuccess(response)); return response; }

    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function shippingMethod(country_id, zone_id, postcode, cartlimitcross) {  
    $('body').removeClass('loaded').addClass('loading');
    var formData = new FormData();
    formData.append('country_id', country_id); 
    formData.append('zone_id', zone_id);
    formData.append('postcode', postcode);
    formData.append('cartlimitcross', cartlimitcross);
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    formData.append('cart_session_id', custom.getCookie('cart_session_id'));
    formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.shippingMethod(formData).then(response => {
       $('body').removeClass('loading').addClass('loaded');
       return response;
    }).catch(error => {
      $('body').removeClass('loaded').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function estimateShipping(pincode) {
    $('body').removeClass('loaded').addClass('loading');

    var formData = new FormData();
    formData.append('pincode', pincode);
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    formData.append('cart_session_id', custom.getCookie('cart_session_id'));
    formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.autoPopulateAddress(formData).then(response => {
       $('body').removeClass('loading').addClass('loaded');
       return response;
    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function updateShippingMethod(shipping_method, shipping_cost) {  
  
    $('body').removeClass('loaded').addClass('loading');
     var formData = new FormData();
    formData.append('shipping_method', shipping_method);
    formData.append('shipping_cost', shipping_cost);
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    formData.append('cart_session_id', custom.getCookie('cart_session_id'));
    formData.append('wishlist_session_id', custom.getCookie('wishlist_session_id'));

  return function(dispatch) {
    return CartApi.updateShippingMethod(formData).then(response => {
        $('body').removeClass('loading').addClass('loaded');
        if(response.status === 'error') { alert(response.error_msg); }
     else {  dispatch(CartUpdateShippingSuccess(response)); }
    }).catch(error => {
      $('body').removeClass('loading').addClass('loaded');
      dispatch(CartError(error));
    });
  };
}

export function getZones(country_id) {  
  return function(dispatch) {
    return CartApi.getZones(country_id).then(response => {
       return response;
    }).catch(error => {
      dispatch(CartError(error));
    });
  };
}


export function CartSuccess(cart_data) {  
  return {type: actionType.CART_SUCCESS, cart_data};
}

export function CartUpdateSuccess(cart_data) {  
  return {type: actionType.CART_UPDATE_SUCCESS, cart_data};
}

export function CartUpdateShippingSuccess(cart_data) {  
  return {type: actionType.CART_UPDATE_SHIPPPING_SUCCESS, cart_data};
}


export function CartError(error) {  
  return {type: actionType.CART_ERROR, error};
}

export function ConfirmPopupOpen(ConfirmPopup) {  
  return {type: actionType.CART_CONFIRM_POPUP, ConfirmPopup};
}

export function EstimatePopupOpen(EstimatePopup) {  
  return {type: actionType.CART_ESTIMATE_POPUP, EstimatePopup};
}