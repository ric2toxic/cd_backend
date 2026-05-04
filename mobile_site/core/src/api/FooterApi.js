import Api from './Api'

class FooterApi { 

   static sendMailForCallBackRequest(customer_id, customer_access_token, mobile) {
    return fetch(Api.api_url+'api/footer/sendMailForCallBackRequest&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&mobile='+mobile).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

   static popular_tags(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/footer/popular_tags&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }


}
export default FooterApi;  