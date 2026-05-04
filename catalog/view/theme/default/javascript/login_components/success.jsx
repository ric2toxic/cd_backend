
class Success extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

	render() {


                   var customer_id = '0';
                   var cId = 'customer_id';
                   var name = cId + "=";
                   var ca = document.cookie.split(';');
                   for (var i = 0; i < ca.length; i++) {
                       var c = ca[i];
                       while (c.charAt(0) == ' ') {
                           c = c.substring(1);
                       }
                       if (c.indexOf(name) == 0) {
                           var customer_id = c.substring(name.length, c.length);
                       }
                   }
                   if(parseInt(customer_id)!='0'){

                   if (localStorage.getItem("recentView")) {
                    var i = '0';
                   var unique_product = [];
                   var all_store_product = [];
                   var localRecentValidate=$.trim(localStorage.getItem("recentView"));
                   if(localRecentValidate.length){
                    var previousProduct = JSON.parse(localStorage.getItem("recentView"));
                         $.each(previousProduct, function(index, value) {
                             var unique_key = value.product_id + '_' + value.category_id;
                             //=====check for dublicate Value========

                             if ($.inArray(unique_key, unique_product) < '0') {
                                 unique_product.push(unique_key);
                                 if(parseInt(value.customer_id)==0){
                                    var curent_cust=customer_id;
                                 }else{
                                    var curent_cust=value.customer_id;
                                 }
                                 var all_store_product_array = {
                                     'customer_id': curent_cust,
                                     'product_id': value.product_id,
                                     'quantity': '1',
                                     'piece_in_set': value.piece_in_set,
                                     'price_per_piece': value.price_per_piece,
                                     'seller_id': value.seller_id,
                                     'category_id': value.category_id
                                 };
                                 all_store_product[i] = all_store_product_array;
                                 i++;

                             }
                             if (i == "30") {
                                 return false;
                             }

                         });

                          localStorage.recentView = JSON.stringify(all_store_product);
                     }else{
                          localStorage.removeItem("recentView");
                     }

                     
                    }

                    if (localStorage.getItem("recentWishList")) { 
                    var i = '0';
                      var unique_product = [];
                      var all_store_product = [];
                      var localRecentShortValidate=$.trim(localStorage.getItem("recentWishList"));
                      if(localRecentShortValidate.length){
                      var previousProduct = JSON.parse(localStorage.getItem("recentWishList"));
                       $.each(previousProduct, function(index, value) {
                           var unique_key = value.product_id + '_' + value.category_id;
                           //=====check for dublicate Value========
                           if ($.inArray(unique_key, unique_product) < '0') {
                            var timeDiff=(( new Date(dateTime) - new Date(value.dateTime) ) / 1000 / 60 / 60);
                            if(parseInt(value.customer_id)==0){
                                var curent_cust=customer_id;
                            }else{
                                var curent_cust=value.customer_id;
                            }
                            if(timeDiff < '24'){
                               unique_product.push(unique_key);
                               var all_store_product_array = {
                                   'customer_id': curent_cust,
                                   'product_id': value.product_id,
                                   'quantity': '1',
                                   'piece_in_set': value.piece_in_set,
                                   'price_per_piece': value.price_per_piece,
                                   'seller_id': value.seller_id,
                                   'category_id': value.category_id,
                                   'dateTime': value.dateTime
                               };
                               all_store_product[i] = all_store_product_array;
                               i++;
                               }

                           }

                       });


                               if(all_store_product.length>0){
                                  localStorage.recentWishList = JSON.stringify(all_store_product);
                                }
                             }else{
                                  localStorage.removeItem("recentWishList");
                             }
                      }
                   

                   }

   function cart_shopping_popup_close(e){
          if($('#product_popup').hasClass('in'))
          {
            $('#cart_shopping_popup').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
             });
          }
    }

		return ( 
       <div className="modal fade add_new_address" id="cart_shopping_popup" role="dialog" data-backdrop="static" data-keyboard="false">
          <div className="modal-dialog login_register_popup sucess_cart">
            <div className="modal-content">
              <div className="modal-header address_popup_head">
                <button type="button" className="close" data-dismiss="modal" id="cart_shopping_popup_close" onClick={cart_shopping_popup_close}>&times;</button>
                <button className="hide" data-toggle="modal" data-target="#cart_shopping_popup" id="cart_shopping_popup_open"></button>
                <h4 className="modal-title">Congratulation!</h4>
              </div>
              <div className="modal-body">
                    <div className="success_msg cart_shopping_success">Your mobile number verified successfully.</div>
                    <div className="otp_model">
                      <form method="get">
                          <button id="continue_shopping_btn" data-dismiss="modal" onClick={cart_shopping_popup_close} className="btn deliver_btn pull-right" type="button">Continue Shopping</button>
                        <a href={site_url+'/cart'}>
                         <button id="go_to_cart_btn" className="btn deliver_btn pull-left" type="button">Go To Cart</button>
                       </a>
                     </form>
                    </div>
                    <div className="clearfix"></div>
              </div>
            </div>
          </div>
     </div>
		)
	}
}
