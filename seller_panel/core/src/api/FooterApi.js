import Api from './Api'

class FooterApi { 

   static information_link(customer_id, customer_access_token) {
   	var encode = window.btoa(`customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/footer/information&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

}
export default FooterApi;  