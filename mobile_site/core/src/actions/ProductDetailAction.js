import * as actionType from './ActionType';
import ProductDetailApi from '../api/ProductDetailApi';
import custom from '../custom/custom';
import webengage from '../custom/webengage';
import $ from 'jquery';

export function product_detailData(product_id) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
     var cart_session_id       = custom.getCookie('cart_session_id');
     var wishlist_session_id   = custom.getCookie('wishlist_session_id');
    return ProductDetailApi.product_detail(product_id,customer_id,customer_access_token,cart_session_id,wishlist_session_id).then(response => {
      if(response.product_detail_id) 
      { 
        webengage.product_view(response); 
        $('body').removeClass('loading').addClass('loaded');
        dispatch(ProductSuccess(response)); return response; 
      }
      else                           
        { 
          $('body').removeClass('loading').addClass('loaded');
          dispatch(ProductError('error')); 
       }
    }).catch(error => {
      dispatch(ProductError(error));
    });
  };
}

export function related_productData(product_id, seller_id) {  
  return function(dispatch) {
    return ProductDetailApi.related_products(product_id, seller_id).then(response => {
      dispatch(RelatedProductSuccess(response));
    }).catch(error => {
      dispatch(ProductError(error));
    });
  };
}

export function ask_question(product_id, customer_name, telephone, email, popup_question) 
{ 
  var formData = new FormData();
      formData.append('product_id', product_id);
      formData.append('customer_name', customer_name);
      formData.append('telephone', telephone);
      formData.append('email', email);
      formData.append('popup_question', popup_question);
      formData.append('customer_id', custom.getCookie('customer_id'));
      formData.append('customer_access_token', custom.getCookie('customer_access_token'));

  return function(dispatch) {
    return ProductDetailApi.ask_question(formData).then(response => {
     $('body').removeClass('loading').addClass('loaded');
     var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Question submit successfully</p></div></div>';
                  $(document.body).append(success_div);   
                  $('#notification').fadeOut(2000);
                   setTimeout(function() {
                    $('#notification').remove();
                    }, 2000);
     
     if(response.status === 'error') { alert(response.error_msg); }
     else { return response; }  

    }).catch(error => {
      throw(error); 
    });
  };
}

export function ProductSuccess(product_detail) {  
  return {type: actionType.PRODUCT_SUCCESS, product_detail};
}

export function RelatedProductSuccess(related_products) {  

  return {type: actionType.PRODUCT_RELATED_SUCCESS, related_products};
}
export function ProductError(error) {  
  return {type: actionType.PRODUCT_ERROR, error};
}

export function QuestionPopupOpen(QuestionPopup) {  
  return {type: actionType.QUESTION_POPUP, QuestionPopup};
}