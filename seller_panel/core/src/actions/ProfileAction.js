import * as actionType from './ActionType';
import ProfileApi from '../api/ProfileApi';
import custom from '../custom/custom'

export function userData() { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token'); 
 return ProfileApi.userData(customer_id,customer_access_token)
  .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                      else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  }
                      return response;
                     })
}

export function terms_data() { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token'); 
  return ProfileApi.terms_data(customer_id,customer_access_token)
  .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                      else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  } 
                      return response;
                    });
}


export function updateSellerProfile(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  
    return ProfileApi.updateSellerProfile(form_data)
    .then(response => { 
                       if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                       else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  } 
                        return response; 
                      }) 
}

export function sameAsPrimary(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  
    return ProfileApi.sameAsPrimary(form_data)
    .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                        else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  } 
                        return response; 
                      }) 
}

export function updateSellerBankDetailsAndAddress(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  

    return ProfileApi.updateSellerBankDetailsAndAddress(form_data)
     .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                         else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  } 
                        return response; 
                      }) 
}

export function changePassword(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  
    return ProfileApi.changePassword(form_data)
            .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                                else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  } 
                        return response; 
                      }) 
}

export function pincodeAddressData(pincode) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    return ProfileApi.pincodeAddressData(pincode,customer_id,customer_access_token)
            .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                                else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  } 
                        return response; 
                      }) 
}

export function bankDetails(ifsc_code) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    return ProfileApi.bankDetails(ifsc_code,customer_id,customer_access_token)
         .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  }
                             else if(!response.data && response.statusCode === 902) { window.location.href = response.message;  }  
                        return response; 
                      }) 
}


export const ProfileUserDataSuccess = (profile_data) => ({
    type: actionType.PROFILE_USERDATA_SUCCESS,
    payload: { profile_data },
});