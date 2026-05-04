import PaymentApi from '../api/PaymentApi';

export function download_csv(file_name) {  
    return PaymentApi.download_csv(file_name).then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }	
      return response;
    }).catch(error => {
      throw(error); 
    });
}