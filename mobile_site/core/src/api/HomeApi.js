import Api from './Api'

class HomeApi { 

   static category_list() {
    return fetch(Api.api_url+'api/home/category_list').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

   static latest_product(customer_id, customer_access_token, custom_store, preferences) {
    return fetch(Api.api_url+'api/home/latest_product&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&custom_store='+custom_store+'&preferences='+preferences).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

   static trending_product(customer_id, customer_access_token, custom_store, preferences) {
    return fetch(Api.api_url+'api/home/trending_product&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&custom_store='+custom_store+'&preferences='+preferences).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }    

}
export default HomeApi;  