import * as actionType from './ActionType';
import HeaderApi from '../api/HeaderApi';
import custom from '../custom/custom'

export function userloginData() { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token'); 
 return HeaderApi.userlogin(customer_id,customer_access_token)
  .then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }
     return response })
  .then(user_login => user_login.data)
}

export function saveAgreementConsent(declaration) { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token'); 
 return HeaderApi.saveAgreementConsent(declaration, customer_id,customer_access_token)
  .then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }
     return response.data  }).catch(error => {
      throw(error); 
    });
}

export function loginData(form_data) {  
    return HeaderApi.login(form_data).then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }
      return response.data;
    }).catch(error => {
      throw(error); 
    });
}

export function sendOTP(form_data) {  
    return HeaderApi.send_otp(form_data).then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }
      return response.data;
    }).catch(error => {
      throw(error); 
    });
}

export function verifyOtp(form_data) {  
    return HeaderApi.verify_otp(form_data).then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }
      return response.data;
    }).catch(error => {
      throw(error); 
    });
}

export const HeaderUserloginSuccess = (userlogin) => ({
    type: actionType.HEADER_USERLOGIN_SUCCESS,
    payload: { userlogin },
});