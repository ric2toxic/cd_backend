import Api from './Api'

class ProductDetailApi { 

   static product_detail(product_id,customer_id,customer_access_token,cart_session_id,wishlist_session_id) {
    return fetch(Api.api_url+'api/product/getProductDetails/'+product_id+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&cart_session_id='+cart_session_id+'&wishlist_session_id='+wishlist_session_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }     

   static related_products(product_id, seller_id) {
    return fetch(Api.api_url+'api/product/getRelatedProducts/'+product_id+'/'+seller_id).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  }

  static ask_question(formData) {
    
    return fetch(Api.api_url+'api/product/askQuestion',
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


}
export default ProductDetailApi;  