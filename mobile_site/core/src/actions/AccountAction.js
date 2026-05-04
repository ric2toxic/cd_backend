import * as actionType from './ActionType';
import AccountApi from '../api/AccountApi';
import custom from '../custom/custom';
import webengage from '../custom/webengage'
import $ from 'jquery'

export function update_number_Otp_Form(formData) {  
  return function(dispatch) {
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
     return AccountApi.send_update_number_otp(formData).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function update_Dropshipper() { 
 var customer_id     = custom.getCookie('customer_id');
 var customer_access_token = custom.getCookie('customer_access_token');
  return function(dispatch) {
    return AccountApi.update_dropshipper(customer_id, customer_access_token).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; } 
      $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200)
       {
           success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
       else
       {
          success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
        $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);
    }).catch(error => {
      throw(error); 
    });
  };
}


export function update_number_Verify_Otp_Form(formData) 
{  
  return function(dispatch) {
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    return AccountApi.verify_update_number_otp(formData).then(response => {
       if(response.statusCode === 900) { window.location.href = "./"; }
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function userDetailData() {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.user_detail(customer_id, customer_access_token).then(response => {
       if(response.statusCode === 900) { window.location.href = "./"; }
      dispatch(UserDetailSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function userProfileUpdate(formData) {  
  return function(dispatch) {
     formData.append('customer_id', custom.getCookie('customer_id'));
     formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    return AccountApi.user_profile_update(formData).then(response => {
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200)
       {
           success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
       else
       {
          success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
        $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);
    }).catch(error => {
      throw(error); 
    });
  };
}

export function userPasswordUpdate(formData) {  
  return function(dispatch) {
        formData.append('customer_id', custom.getCookie('customer_id'));
        formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    return AccountApi.user_password_update(formData).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
      if(response.statusCode===200)
       {
           success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
       else
       {
          success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
        $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);
      return response;             
    }).catch(error => {
      throw(error); 
    });
  };
}

export function bankDetailData() { 
 var customer_id     = custom.getCookie('customer_id');
 var customer_access_token = custom.getCookie('customer_access_token'); 
  return function(dispatch) {
    return AccountApi.bank_detail_data(customer_id, customer_access_token).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; } 
      dispatch(BankDetailSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function userBankDetailUpdate(formData) { 
  formData.append('customer_id', custom.getCookie('customer_id'));
  formData.append('customer_access_token', custom.getCookie('customer_access_token'));
  return function(dispatch) {
    return AccountApi.user_bank_detail_update(formData).then(response => {
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
      if(response.statusCode===200)
       {
           success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
       else
       {
          success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
        $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);
        return response;
    }).catch(error => {
      throw(error); 
    });
  };
}



export function userAddressListData() {  
  return function(dispatch) {
    var customer_id     = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.user_address_list_data(customer_id, customer_access_token).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      dispatch(userAddressListSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}


export function userAddressBookUpdate(formData) {  
  return function(dispatch) {
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    return AccountApi.user_address_book_update(formData).then(response => {
       
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200){
        success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      else
      {
        success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      
      $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);

      return response;

    }).catch(error => {
      throw(error); 
    });
  };
}


export function userAddAddress(formData) {  
  return function(dispatch) {
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    return AccountApi.user_address_book_add(formData).then(response => {
       
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200){
        success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      else
      {
        success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      
      $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);

      return response;             

    }).catch(error => {
      throw(error); 
    });
  };
}

export function userAddressDelete(addressId) {  
  return function(dispatch) {
    var customer_id     = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.user_address_delete(addressId, customer_id, customer_access_token).then(response => {
       
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200){
        $("#Address_"+addressId).remove();
        success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      else
      {
        success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);
    }).catch(error => {
      throw(error); 
    });
  };
}

export function orderDetailData(offset=4,beyond_order_id=0, filter_order_no=false) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.order_detail_data(customer_id, customer_access_token, offset, beyond_order_id, filter_order_no).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }

      if(response.data.orders && response.data.orders.length === 0)
       {
          $(".btn_browse_more").removeClass("page_auto_load");
          $(".btn_browse_more").addClass("last_page");
       }
       else
       {
          $(".btn_browse_more").addClass("page_auto_load");
          $(".btn_browse_more").removeClass("last_page");
       }

      dispatch(OrderDetailSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function orderDetailPaginationData(offset=4,beyond_order_id=0, filter_order_no=false) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.order_detail_data(customer_id, customer_access_token, offset, beyond_order_id, filter_order_no).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      if(response.data.orders && response.data.orders.length === 0)
       {
          $(".btn_browse_more").removeClass("page_auto_load");
          $(".btn_browse_more").addClass("last_page");
       }
       else
       {
          $(".btn_browse_more").addClass("page_auto_load");
          $(".btn_browse_more").removeClass("last_page");
       }
      dispatch(OrderDetailPaginationSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function getOrderInfoAndTotalAmountBreakup(order_id, suborder_id) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.getOrderInfoAndTotalAmountBreakup(customer_id, customer_access_token, order_id, suborder_id).then(response => {
     if(response.statusCode === 900) { window.location.href = "./"; } 
      dispatch(OrderInfoAndTotalAmountBreakup(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function getOrderProductDetailsBySuborderId(order_id, suborder_id) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.getOrderProductDetailsBySuborderId(customer_id, customer_access_token, order_id, suborder_id).then(response => {
     if(response.statusCode === 900) { window.location.href = "./"; } 
      dispatch(OrderProductDetailsBySuborderId(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function getShippingPreferencesForOrderDetail(order_id, suborder_id) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.getShippingPreferencesForOrderDetail(customer_id, customer_access_token, order_id, suborder_id).then(response => {
     if(response.statusCode === 900) { window.location.href = "./"; } 
      dispatch(shippingPreferencesForOrderDetail(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function rquestNewPaymentLink(formData) {  
  return function(dispatch) {
    formData.append("customer_id", custom.getCookie('customer_id'));
    formData.append("customer_access_token", custom.getCookie('customer_access_token'));
    return AccountApi.rquestNewPaymentLink(formData).then(response => {
     if(response.statusCode === 900) { window.location.href = "./"; } 
      $('body').removeClass('loading').addClass('loaded');
      $(".request_payment_btn").remove();
      $(".payment_btn").attr('href',response.data.url);

    }).catch(error => {
      throw(error); 
    });
  };
}

export function reorder(order_id, suborder_id, order_product_id) {  
  return function(dispatch) {
     var customer_id     = custom.getCookie('customer_id');
     var customer_access_token = custom.getCookie('customer_access_token');
    return AccountApi.reorder(customer_id, customer_access_token, order_id, suborder_id, order_product_id).then(response => {
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200){
        $("#cart-total").html(response.data);
        success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      else
      {
        success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
      }
      $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);
    }).catch(error => {
      throw(error); 
    });
  };
}

export function setShippingPreferences(formData) {  
  return function(dispatch) {
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    return AccountApi.setShippingPreferences(formData).then(response => {
       if(response.statusCode === 900) { window.location.href = "./"; }
       $('body').removeClass('loading').addClass('loaded');
       var success_div = '' 
       if(response.statusCode===200)
       {
           success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
       else
       {
          success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
       }
        $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
        }, 3000);

        dispatch(shippingPreferencesForOrderDetail(response.data));           
        return response;
                   
    }).catch(error => {
      throw(error); 
    });
  };
}

export function wishListProduct() {  
  var customer_id     = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  return function(dispatch) {
    return AccountApi.wish_list_product(customer_id, customer_access_token).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      dispatch(WishListProductSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function wishListRemoveProduct(product_id) {  
  var customer_id     = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  return function(dispatch) {
    return AccountApi.wish_list_remove_product(product_id, customer_id, customer_access_token).then(response => {
     
      webengage.web_engage_product_detail(product_id, 0, 'we-custom-removefromwishlist');
      if(response.statusCode === 900) { window.location.href = "./"; }
      
      return response;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function wishListClearAllProduct(products) 
{  
  var customer_id     = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  return function(dispatch) {
    return AccountApi.wish_list_clear_all_product(customer_id, customer_access_token).then(response => {
     
       products.map((product, index) => { 
        webengage.web_engage_product_detail(product.product_id, 0, 'we-custom-removefromwishlist');
        return true;
        }); 

      if(response.statusCode === 900) { window.location.href = "./"; }
      
      return response;
    }).catch(error => {
      throw(error); 
    });
  };
}


export function postYourRequirementCategory() {  
  return function(dispatch) {
    return AccountApi.post_your_requirement_category().then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      dispatch(PostYourRequirementCategorySuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function postYourRequirement(formData) { 

   formData.append('customer_id', custom.getCookie('customer_id'));
   formData.append('customer_access_token', custom.getCookie('customer_access_token'));
   
  return function(dispatch) {
    return AccountApi.post_your_requirement(formData).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
       return response;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function updateBankOtp() { 
  return function(dispatch) {
    return AccountApi.update_bank_otp(custom.getCookie('customer_id'), custom.getCookie('customer_access_token')).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
       return response;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function getCustomerLevelAccountSummary() 
{  
  return function(dispatch) {
      var filters = '';
        filters = filters+'customer_id='+custom.getCookie('customer_id');
        filters = filters+'&customer_access_token='+custom.getCookie('customer_access_token');
        var encode =  window.btoa(filters);

    return AccountApi.getCustomerLevelAccountSummary(encode).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      
    let payment_methods = [];
     payment_methods = payment_methods = $.map(response.payment_methods, function(payment_method, index) 
      {
         return{'key':index,'label':payment_method,'value':false};
      });
      dispatch(OrderAccountSummarySuccess(response, payment_methods));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function getOrderLevelAccountSummary(filter_search) 
{  
  return function(dispatch) {
      var filters = '';
        filters = filters+'customer_id='+custom.getCookie('customer_id');
        filters = filters+'&customer_access_token='+custom.getCookie('customer_access_token');
        $.map(filter_search, function(filter, index) {
        if(filter)
        {
              filters = filters+'&'+index+'='+filter;
            }   
        }); 
        var encode =  window.btoa(filters);
    return AccountApi.getOrderLevelAccountSummary(encode).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }

      if(response.data.orders && (response.data.orders.length === 0 || response.data.orders.length < response.data.limit))
       {
          $(".btn_browse_more").removeClass("page_auto_load");
          $(".btn_browse_more").addClass("last_page");
       }
       else
       {
          $(".btn_browse_more").addClass("page_auto_load");
          $(".btn_browse_more").removeClass("last_page");
       }

       let payment_methods = [];
       payment_methods = payment_methods = $.map(response.payment_methods, function(payment_method, index) 
       {
         return{'key':index,'label':payment_method,'value':false};
       });

      dispatch(OrderAccountStatementSuccess(response, payment_methods));

    }).catch(error => {
      throw(error); 
    });
  };
}

export function getOrderLevelAccountSummaryPagination(filter_search, beyond_order_id, balance) 
{  
  return function(dispatch) {
      var filters = '';
        filters = filters+'customer_id='+custom.getCookie('customer_id');
        filters = filters+'&customer_access_token='+custom.getCookie('customer_access_token');
        $.map(filter_search, function(filter, index) {
        if(filter)
        {
              filters = filters+'&'+index+'='+filter;
            }   
        }); 

        filters = filters+'&beyond_order_id='+beyond_order_id;
        filters = filters+'&balance='+balance; 

      var encode =  window.btoa(filters);
    return AccountApi.getOrderLevelAccountSummary(encode).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }

      if(response.data.orders && (response.data.orders.length === 0 || response.data.orders.length < response.data.limit))
       {
          $(".btn_browse_more").removeClass("page_auto_load");
          $(".btn_browse_more").addClass("last_page");
       }
       else
       {
          $(".btn_browse_more").addClass("page_auto_load");
          $(".btn_browse_more").removeClass("last_page");
       }

      dispatch(OrderAccountStatementPaginationSuccess(response.data));

    }).catch(error => {
      throw(error); 
    });
  };
}

export function getOrderLevelBreakupAccountDetails(filter_order_id) 
{  
  return function(dispatch) {
      var filters = '';
        filters = filters+'customer_id='+custom.getCookie('customer_id');
        filters = filters+'&customer_access_token='+custom.getCookie('customer_access_token');
        filters = filters+'&filter_order_id='+filter_order_id;
        
      var encode =  window.btoa(filters);
    return AccountApi.getOrderLevelBreakupAccountDetails(encode).then(response => {
      if(response.statusCode === 900) { window.location.href = "./"; }
      if(response.statusCode === 200)
      {
         dispatch(OrderAccountStatementBreakupSuccess(response.data));
      }
      else
      {
        alert(response.message);
      }
      

    }).catch(error => {
      throw(error); 
    });
  };
}

export function downloadReport(filter_search) 
{  
  return function(dispatch) {
      var filters = '';
        filters = filters+'customer_id='+custom.getCookie('customer_id');
        filters = filters+'&customer_access_token='+custom.getCookie('customer_access_token');
        $.map(filter_search, function(filter, index) {
        if(filter)
        {
              filters = filters+'&'+index+'='+filter;
            }   
        }); 
       var encode =  window.btoa(filters);
       AccountApi.getDownloadReport(encode);
  };
}

export function UserDetailSuccess(user_detail) {  
  return {type: actionType.USER_DETAIL_SUCCESS, user_detail};
}

export function BankDetailSuccess(bank_detail) { 
  return {type: actionType.BANK_DETAIL_SUCCESS, bank_detail};
}
export function userAddressListSuccess(address_list) { 
  return {type: actionType.USER_ADDRESS_LIST_SUCCESS, address_list};
}

export function OrderDetailSuccess(order_detail) {
  return {type: actionType.ORDER_DETAIL_SUCCESS, order_detail};
}

export function OrderDetailPaginationSuccess(order_detail) {
  return {type: actionType.ORDER_DETAIL_PAGINATION_SUCCESS, order_detail};
}
export function OrderInfoAndTotalAmountBreakup(order_detail_info) {
  return {type: actionType.ORDER_DETAIL_INFO_SUCCESS, order_detail_info};
}
export function OrderProductDetailsBySuborderId(order_product_detail) {
  return {type: actionType.ORDER_PRODUCT_DETAIL_SUCCESS, order_product_detail};
}
export function shippingPreferencesForOrderDetail(shipping_preferences) {
  return {type: actionType.ORDER_SHIPPING_PREFERENCES_SUCCESS, shipping_preferences};
}
export function WishListProductSuccess(wishlist_product_detail) {
  return {type: actionType.WISHLIST_PRODUCT_SUCCESS, wishlist_product_detail};
}
export function PostYourRequirementCategorySuccess(post_req_category) {
  return {type: actionType.POST_YOUR_REQUIRMENT_CATEGORY_SUCCESS, post_req_category};
}
export function UpdatePopupOpen(UpdatePopup) {  
  return {type: actionType.UPDATE_POPUP, UpdatePopup};
}
export function AddressPopupOpen(AddressPopup) {  
  return {type: actionType.ADDRESS_POPUP, AddressPopup};
}
export function ShippingPreferencesPopupOpen(ShippingPreferencesPopup) {  
  return {type: actionType.SHIPPING_PREFERENCES_POPUP, ShippingPreferencesPopup};
}
export function OrderAccountSummarySuccess(account_summary, payment_methods) {  
  return {type: actionType.ACCOUNT_SUMMARY_SUCCESS, account_summary, payment_methods};
}
export function OrderAccountStatementSuccess(account_statement, payment_methods) {  
  return {type: actionType.ACCOUNT_STATEMENT_SUCCESS, account_statement, payment_methods};
}
export function OrderAccountStatementPaginationSuccess(account_statement) {  
  return {type: actionType.ACCOUNT_STATEMENT_PAGINATION_SUCCESS, account_statement};
}
export function OrderAccountStatementBreakupSuccess(account_statement_breakup) {  
  return {type: actionType.ACCOUNT_STATEMENT_BREAKUP_SUCCESS, account_statement_breakup};
}





