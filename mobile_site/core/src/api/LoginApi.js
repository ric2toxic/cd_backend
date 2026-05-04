import Api from './Api'
import $ from 'jquery'

class LoginApi { 

   static send_otp(form_data) {
    return fetch(Api.api_url+'api/login_mobile/send_otp&'+form_data).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

   static verify_otp(form_data) {
    return fetch(Api.api_url+'api/login_mobile/verify_otp&'+form_data).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static send_password_otp(form_data) {
    return fetch(Api.api_url+'api/login_mobile/send_password_otp&'+form_data).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }   

  static login() {
    return $.ajax({
        url: Api.api_url+'api/login_mobile/login',
        type: 'post',
        data: $('#login_form input[type=\'text\'], #login_form input[type=\'hidden\'], #login_form input[type=\'password\']'),
        dataType: 'json',
         success: function(json) {
             return json;
            }
        });
  } 

  static register() 
  {
    return $.ajax({
        url: Api.api_url+'api/login_mobile/register',
        type: 'post',
        data: $('#register_form input[type=\'text\'], #register_form input[type=\'password\'], #register_form input[type=\'number\'], #register_form input[type=\'hidden\'], #register_form input[type=\'radio\']:checked, #register_form input[type=\'checkbox\']:checked, #register_form select, #register_form textarea'),
        dataType: 'json',
         success: function(json) {
             return json;
            }
        });
  } 


  static customer_type() {
    return fetch(Api.api_url+'api/login/customer_type').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static country_list() {
    return fetch(Api.api_url+'api/login/country_list').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static state_list(country_id) {
    return fetch(Api.api_url+'api/login/state_list&country_id='+country_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static pincodeAddress(postcode) {
    return fetch(Api.api_url+'api/login/pincodeAddress&postcode='+postcode).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static i_want_design(formData) {
  
    return fetch(Api.api_url+'api/product/user_comment',
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

}
export default LoginApi;  