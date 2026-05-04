import Api from './Api'

class CategoryApi { 

   static getFilterData(filter_data) {
    return fetch(Api.api_url+'api/category/filter&'+filter_data).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static product_list(filter_data) {
    return fetch(Api.api_url+'api/category/product_list&'+filter_data).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

}
export default CategoryApi;  