import Api from './Api'

class HomeApi { 

  static register(form_data) {
  	var encode = window.btoa(`${form_data}`);
    return fetch(`${Api.api_url}api/sellers/header/register&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 
  

}
export default HomeApi;  