import Api from './Api'

class BannerApi { 

   static banner(preferences) 
   {
    return fetch(Api.api_url+'api/home/banner&preferences='+preferences).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

}
export default BannerApi;  