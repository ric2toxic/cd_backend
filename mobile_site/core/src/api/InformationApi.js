import Api from './Api'

class InformationApi { 

   static about_us() {
    return fetch(Api.api_url+'api/information/about_us').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static policies() {
    return fetch(Api.api_url+'api/information/policies').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 


  static storelocator() {
    return fetch(Api.api_url+'api/information/storelocator').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static contactus() {
    return fetch(Api.api_url+'api/information/contactus').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }        

   static contactus_form(form_data) {
    return fetch(Api.api_url+'api/information/contactus_form&'+form_data).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static information(information_id) {
    return fetch(Api.api_url+'api/information/information&information_id='+information_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static seller_register(form_data) 
  {
     var encode = window.btoa(`${form_data}`);
     return fetch(`${Api.api_url}api/sellers/header/register&data=${encode}`).then(response => {
      return response.json();
     }).catch(error => {
      return error;
     });
  }
 
  static seller_agreement() {
    return fetch(Api.api_url+'api/information/terms_data').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }



}

export default InformationApi;  