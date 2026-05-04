import Api from './Api'

class OrderApi { 

 static splitOrderProducts(form_data) {
  return fetch(Api.api_url+'api/sellers/orders/splitOrderProducts',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }


static sellerEditedAfterGeneratingInvoice(form_data) {
  return fetch(Api.api_url+'api/sellers/orders/sellerEditedAfterGeneratingInvoice',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

 static revertGoodsBySeller(form_data) {
  return fetch(Api.api_url+'api/sellers/orders/revertGoodsBySeller',
                {
                 method: "POST",
                 body: form_data
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }  

}
export default OrderApi;  