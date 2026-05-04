import * as actionType from './ActionType';
import LoginApi from '../api/LoginApi';
import custom from '../custom/custom';
import $ from 'jquery';

export function opt_formData(form_data) {  
  return function(dispatch) {
    return LoginApi.send_otp(form_data).then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function verify_otp_formData(form_data) {  
  return function(dispatch) {
    return LoginApi.verify_otp(form_data).then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function password_otp_formData(form_data) {  
  return function(dispatch) {
    return LoginApi.send_password_otp(form_data).then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function customer_typeData() {  
  return function(dispatch) {
    return LoginApi.customer_type().then(response => {
      dispatch(LoginCustomeTypeSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function country_listData() {  
  return function(dispatch) {
    return LoginApi.country_list().then(response => {
      dispatch(LoginCountrySuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function state_listData(country_id) {  
  return function(dispatch) {
    return LoginApi.state_list(country_id).then(response => {
       return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function pincodeAddressData(postcode) {  
  return function(dispatch) {
    return LoginApi.pincodeAddress(postcode).then(response => {
       return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function loginData() {  
  return function(dispatch) {
    return LoginApi.login().then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}


export function registerData() 
{  
  return function(dispatch) {
    return LoginApi.register().then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function i_want_design(customer_mobile, product_id, product_status, popup_comment) { 
    var formData = new FormData();
    formData.append('product_id', product_id);
    formData.append('product_status', product_status);
    formData.append('popup_comment', popup_comment);
    formData.append('customer_id', custom.getCookie('customer_id'));
    formData.append('customer_access_token', custom.getCookie('customer_access_token'));
    formData.append('customer_mobile', customer_mobile);

  return function(dispatch) {
    return LoginApi.i_want_design(formData).then(response => {
     $('body').removeClass('loading').addClass('loaded');
     var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Comment submit successfully</p></div></div>';
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


export function LoginCustomeTypeSuccess(customer_type) {  
  return {type: actionType.LOGIN_CUSTOMER_TYPE_SUCCESS, customer_type};
}

export function LoginCountrySuccess(country_list) {  
  return {type: actionType.LOGIN_COUNTRY_SUCCESS, country_list};
}

export function OptPopupOpen(OptPopup) {  
  return {type: actionType.OPT_POPUP, OptPopup};
}

export function LoginPopupOpen(LoginPopup) {  
  return {type: actionType.LOGIN_POPUP, LoginPopup};
}

export function RegisterPopupOpen(RegisterPopup) {  
  return {type: actionType.REGISTER_POPUP, RegisterPopup};
}

export function SuccessPopupOpen(SuccessPopup) {  
  return {type: actionType.SUCCESS_POPUP, SuccessPopup};
}

export function DesignPopupOpen(DesignPopup) {  
  return {type: actionType.DESIGN_POPUP, DesignPopup};
}