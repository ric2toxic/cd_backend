import Api from './Api'

class ProfileApi { 

   static userData(customer_id, customer_access_token) {
    var encode = window.btoa(`customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/profile/userdata&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static terms_data(customer_id, customer_access_token) {
    var encode = window.btoa(`customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/profile/terms_data&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }  

   static bankDetails(ifsc_code, customer_id, customer_access_token) {
    var encode = window.btoa(`ifsc_code=${ifsc_code}&customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/profile/bankDetails&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static pincodeAddressData(pincode, customer_id, customer_access_token) {
    var encode = window.btoa(`pincode=${pincode}&customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/profile/getState&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static updateSellerProfile(form_data) {
  return fetch(Api.api_url+'api/sellers/profile/updateSellerProfile',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static changePassword(form_data) {
  return fetch(Api.api_url+'api/sellers/profile/changePassword',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static sameAsPrimary(form_data) {
  return fetch(Api.api_url+'api/sellers/profile/sameAsPrimary',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

 static updateSellerBankDetailsAndAddress(form_data) {
  return fetch(Api.api_url+'api/sellers/profile/updateSellerBankDetailsAndAddress',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }


}
export default ProfileApi;  