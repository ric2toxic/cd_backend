import HomeApi from '../api/HomeApi';

export function registerData(form_data) {  
    return HomeApi.register(form_data).then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  } 	
      return response.data;
    }).catch(error => {
      throw(error); 
    });
}