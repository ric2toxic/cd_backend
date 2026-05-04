import Api from './Api'

class MenuApi { 

   static custom_store(custom_store) {
    return fetch(Api.api_url+'api/header/custom_store_mobile&custom_store='+custom_store).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }  

   static menu() {
    return fetch(Api.api_url+'api/header/mobile_menu').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }  

   static information() {
    return fetch(Api.api_url+'api/footer/information').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

}
export default MenuApi;  