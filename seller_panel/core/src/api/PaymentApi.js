import Api from './Api'

class PaymentApi { 

   static download_csv(data) {
   	var encode = window.btoa(`data=${data}`);
    return fetch(`${Api.api_url}api/sellers/payment/paymentreports&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

}
export default PaymentApi;  