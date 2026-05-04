import Api from './Api'

class HeaderApi { 

   static userlogin(customer_id, customer_access_token) {
    var encode = window.btoa(`customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/header/userlogin&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }
 
  static saveAgreementConsent(declaration, customer_id, customer_access_token) {
    var encode = window.btoa(`declaration=${declaration}&customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/header/saveAgreementConsent&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static login(form_data) {
    var encode = window.btoa(`${form_data}`);
    return fetch(`${Api.api_url}api/sellers/header/login&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 
 
  static send_otp(form_data) {
    var encode = encodeURIComponent(window.btoa(`${form_data}`));
    return fetch(`${Api.api_url}api/sellers/header/send_otp&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 
  
  static verify_otp(form_data) {
    var encode = window.btoa(`${form_data}`);
    return fetch(`${Api.api_url}api/sellers/header/verify_otp&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

}
export default HeaderApi;  