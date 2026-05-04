import OrderApi from '../api/OrderApi';
import custom from '../custom/custom'

export function splitOrderProducts(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  
    return OrderApi.splitOrderProducts(form_data)
     .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  } 
                        return response; 
                      }) 
}

export function sellerEditedAfterGeneratingInvoice(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  
    return OrderApi.sellerEditedAfterGeneratingInvoice(form_data)
     .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  } 
                        return response; 
                      }) 
}


export function revertGoodsBySeller(form_data) {
    var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    form_data.append('customer_id', customer_id);
    form_data.append('customer_access_token', customer_access_token);  
    return OrderApi.revertGoodsBySeller(form_data)
     .then(response => { if(!response.data && response.statusCode === 900) { window.location.href = './#/login';  } 
                        return response; 
                      }) 
}
