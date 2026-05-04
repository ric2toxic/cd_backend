import Api from './Api';

class AccountApi { 

  static send_update_number_otp(formData) {
    return fetch(Api.api_url+'api/account/update_number_otp',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

  static verify_update_number_otp(formData) {
    
    return fetch(Api.api_url+'api/account/verify_otp',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static user_detail(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/account/user_detail&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

  static user_profile_update(formData) {
    return fetch(Api.api_url+'api/account/user_profile_update',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static user_password_update(formData) 
  {
    return fetch(Api.api_url+'api/account/user_password_update',
                {
                 method: "POST",
                 body: formData
                }
    ).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });   
  }

  

static bank_detail_data(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/bank/user_bank_detail&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

static update_dropshipper(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/account/updateDropshipper&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

  static user_bank_detail_update(formData) {
        return fetch(Api.api_url+'api/bank/user_bank_detail_update',
                {
                 method: "POST",
                 body: formData
                }
        ).then(response => {
          return response.json();
         }).catch(error => {
          return error;
        });  
    }


   static information(information_id) {
    return fetch(Api.api_url+'api/information/information&information_id='+information_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  
  static user_address_list_data(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/address/get_address_list&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

    static user_address_book_add(formData) {
        
        return fetch(Api.api_url+'api/address/user_address_book_add',
                {
                 method: "POST",
                 body: formData
                }
        ).then(response => {
          return response.json();
         }).catch(error => {
          return error;
        });    
    }

    static user_address_book_update(formData) {

        return fetch(Api.api_url+'api/address/user_address_book_update',
                {
                 method: "POST",
                 body: formData
                }
        ).then(response => {
          return response.json();
         }).catch(error => {
          return error;
        });
    }


static user_address_delete(address_id, customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/address/user_address_delete&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&address_id='+address_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

static order_detail_data(customer_id, customer_access_token, offset, beyond_order_id, filter_order_no) {
    let url = Api.api_url+'api/customer_account/orders/getOrderList&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&offset='+offset;
    if(beyond_order_id > 0)
    {
      url = url+'&beyond_order_id='+beyond_order_id;
    }
    if(filter_order_no)
    {
      url = url+'&filter_order_or_invoice_no='+filter_order_no;
    }
   
    return fetch(url).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

static getOrderInfoAndTotalAmountBreakup(customer_id, customer_access_token, order_id, suborder_id) {
    return fetch(Api.api_url+'api/customer_account/orders/getOrderInfoAndTotalAmountBreakup&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&order_id='+order_id+'&suborder_id='+suborder_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

static getOrderProductDetailsBySuborderId(customer_id, customer_access_token, order_id, suborder_id) {
    return fetch(Api.api_url+'api/customer_account/orders/getOrderProductDetailsBySuborderId&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&order_id='+order_id+'&suborder_id='+suborder_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    } 

static getShippingPreferencesForOrderDetail(customer_id, customer_access_token, order_id, suborder_id) {
    return fetch(Api.api_url+'api/customer_account/orders/shippingPreferencesForOrderDetail&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&order_id='+order_id+'&suborder_id='+suborder_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }        

static reorder(customer_id, customer_access_token, order_id, suborder_id, order_product_id) {
    return fetch(Api.api_url+'api/customer_account/orders/reorderOrderProduct&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&order_id='+order_id+'&suborder_id='+suborder_id+'&order_product_id='+order_product_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

static setShippingPreferences(formData) {
        return fetch(Api.api_url+'api/customer_account/orders/shippingPreferencesForOrderDetail',
                {
                 method: "POST",
                 body: formData
                }
        ).then(response => {
          return response.json();
         }).catch(error => {
          return error;
        });
    }

static rquestNewPaymentLink(formData) {
     return fetch(Api.api_url+'api/customer_account/orders/rquestNewPaymentLink',
                {
                 method: "POST",
                 body: formData
                }
        ).then(response => {
          return response.json();
         }).catch(error => {
          return error;
        });
    } 

static wish_list_product(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/wishlist/getList&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
 }

static wish_list_remove_product(product_id, customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/wishlist/remove&remove='+product_id+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
 }

 static wish_list_clear_all_product(customer_id, customer_access_token) {
    return fetch(Api.api_url+'api/wishlist/clear_all&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
 }  

    static post_your_requirement_category() {
    return fetch(Api.api_url+'api/account/postYourRequirementCategory').then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
    }

    static post_your_requirement(formData) {
    return fetch(Api.api_url+'api/account/postYourRequirement',
                {
                 method: "POST",
                 body: formData
                }
        ).then(response => {
          return response.json();
         }).catch(error => {
          return error;
        });
    }

  static update_bank_otp(customer_id, customer_access_token) {
     return fetch(Api.api_url+'api/bank/update_bank_otp&customer_id='+customer_id+'&customer_access_token='+customer_access_token).then(response => {
      return response.json();
     }).catch(error => {
      return error;
     });
   }

  static getCustomerLevelAccountSummary(searchData) 
   {
       return fetch(Api.api_url+'api/customer_account/accounts/getCustomerLevelAccountSummary&data='+searchData).then(response => {
         return response.json();
        }).catch(error => {
          return error;
        });
    }

  static getOrderLevelAccountSummary(searchData) 
   {
       return fetch(Api.api_url+'api/customer_account/accounts/getOrderLevelAccountSummary&data='+searchData).then(response => {
         return response.json();
        }).catch(error => {
          return error;
        });
   }
   
  static getOrderLevelBreakupAccountDetails(searchData) 
   {
       return fetch(Api.api_url+'api/customer_account/accounts/getOrderLevelBreakupAccountDetails&data='+searchData).then(response => {
         return response.json();
        }).catch(error => {
          return error;
        });
   }

  static getDownloadReport(searchData) 
   {
       window.location.href = Api.api_url+'api/customer_account/accounts/downloadCsvForCustomerAccountsStatement&data='+searchData;
       return true;
   }

}



export default AccountApi;  