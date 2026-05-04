var parts = window.location.pathname.split( '/' );
var cdn_url = "https://d36qiqd7gl7e25.cloudfront.net/";

var site_url = window.location.origin;  
if(parts[1] == 'staging'){ 
    site_url = site_url+'/staging'; 
}
              //======remove vals add bebore 24 hour in local storage wishlist start=========//
              var all_store_product_array=[];
              var all_store_product = [];
              //localStorage.removeItem("recentWishList");
                   if (localStorage.getItem("recentWishList")) {
                    var i = '0';
                    var previousProduct = JSON.parse(localStorage.getItem("recentWishList"));
                    var unique_product = [];
                    var dt = new Date();
                    var dateTime = (dt.getFullYear() +"/"+ (dt.getMonth()+1) +"/"+ dt.getDate()+" "+dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds());
                       $.each(previousProduct, function(index, value) {
                           var unique_key = value.product_id + '_' + value.category_id;
                           //=====check for dublicate Value========
                           if ($.inArray(unique_key, unique_product) < '0') {
                            var timeDiff=(( new Date(dateTime) - new Date(value.dateTime) ) / 1000 / 60 / 60);
                            if(timeDiff < '24'){
                               unique_product.push(unique_key);
                               var all_store_product_array = {
                                   'customer_id': value.customer_id,
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
                   }

                  if(all_store_product.length>0){
                   localStorage.recentWishList = JSON.stringify(all_store_product);
                   }
                   //======remove vals add bebore 24 hour in local storage wishlist end=========//
 function createCookie(name, value, days) {
  var expires;
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path=/";
}

 function getCookie(c_name) {
  if (document.cookie.length > 0) {
   var c_start = document.cookie.indexOf(c_name + "=");
    if (c_start != -1) {
      c_start = c_start + c_name.length + 1;
     var c_end = document.cookie.indexOf(";", c_start);
      if (c_end == -1){
        c_end = document.cookie.length;
      }
      return unescape(document.cookie.substring(c_start, c_end));
    }
  }
  return "";
}


 function cart_add(product_id, quantity, for_detail_page=0) { 

    if(getCookie("customer_mobile") == '')
        {
            $("input[name=redirect_cart]").val(product_id+'-'+quantity);
            $('#login_verify_popup_open').click();
        }
    else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
       {
         $("input[name=redirect_cart]").val(product_id+'-'+quantity);
         $('#login_popup_open').click();
       }
    else
      {
        if((typeof(quantity) == 'undefined') || quantity == 0)
        {
           quantity = 1;
        }

        web_engage_product_detail(product_id, quantity, 'we-custom-addtocart');

        $('body').removeClass('loaded').addClass('loading');
         axios({
           method:'post',
           url:'./api/cart/add_item',
           data: 'product_id=' + product_id + '&quantity=' + quantity,
           responseType:'json'
          })
         .then(response => { 
              document.getElementById("cart-total").innerHTML = response.data.total_in_cart;
              $('body').removeClass('loading').addClass('loaded'); 
              if(for_detail_page == 1){     
                $('#cart_shopping_popup_open').click();
              }       

              else if(for_detail_page == 0){
                 var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Added to cart successfully</p></div></div>';
                  $(document.body).append(success_div);   
                  $('#notification').fadeOut(2000);
                   setTimeout(function() {
                    $('#notification').remove();
                    }, 2000);
                }

               });
        }


      }
  

function cart_add_with_option() {

   if(getCookie("customer_mobile") == '')
       {
         $("#for_detail_page").val(1);
         $("input[name=redirect_cart]").val(1);
         $('#login_verify_popup').modal('show');
       }
   else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
      {
        $("#for_detail_page").val(1);
        $("input[name=redirect_cart]").val(1);
        $('#login_popup').modal('show');
      }
   else
   {  

       var product_id = parseInt($('#product input[name=\'product_id\']').val(), 10);
       var quantity = 0;

       $('#product input[type=\'text\']').each(function( index ) {
         quantity = parseInt($( this ).val(), 10) + parseInt(quantity, 10);
       });

     web_engage_product_detail(product_id, quantity, 'we-custom-addtocart');

     var for_detail_page = $("#for_detail_page").val();
     $("#for_detail_page").val('0');
     $('body').removeClass('loaded').addClass('loading');
   $.ajax({
     url: './api/cart/addWithOptions',
     type: 'post',
     data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea, #product #option-input'),
     dataType: 'json',
     beforeSend: function() {
       $('#button-cart').button('loading');
     },
     complete: function() {
       $('body').removeClass('loading').addClass('loaded');
       $('#button-cart').button('reset');
     },
     success: function(json) {
       if(json.error)
       {
          var success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Error</strong><hr class="message-inner-separator"><p>please select atleast one option</p></div></div>';
          $(document.body).append(success_div);
          $('#notification').fadeOut(2000);
          setTimeout(function() {
         $('#notification').remove();
          }, 2000);
       }
       else
       {
         if(for_detail_page == 0)
         {
          var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Added to cart successfully</p></div></div>';
                 $(document.body).append(success_div);  
                 $('#notification').fadeOut(2000);
                  setTimeout(function() {
                   $('#notification').remove();
                   }, 2000);
         }
         else
         {
           document.getElementById("cart-total").innerHTML = json.total_in_cart;
           $('#cart_shopping_popup').modal("show");
         }          
       }
     }
   });
 }
}




 function wishlist_add (product_id,b,a) {

     web_engage_product_detail(product_id, 0, 'we-custom-addtowishlist');

    if ($(".ctoken").get(0)) {
      var ctoken = $(".ctoken").val();
    } else {
      var ctoken = 0;
    }
    $('body').removeClass('loaded').addClass('loading');
    $.ajax({
      //url: './index.php?route=account/wishlist/add&ctoken='+ctoken,
      url: './api/product/addWishlist&ctoken='+ctoken, 
      type: 'post',
      data: 'product_id=' + product_id,
      dataType: 'json',
      success: function(json) {
        $('body').removeClass('loading').addClass('loaded');
 
        $('#wishlist-total-span').html(json['count']);
        $('#wishlist-total').attr('title', json['total']);
        $("#wishlist_heart_"+a.product_id).removeClass();
        $("#wishlist_heart_"+a.product_id).addClass('fa fa-heart custom_heart');

       var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+ json['success']+'</p></div></div>';

      if (json['success']) {
          $(document.body).append(success_div);
        }

        if (json['info']){
          var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+ json['info']+'</p></div></div>';
            $(document.body).append(success_div);
        }
        //======Store value in local storage start=========//
                   var product_id = a.product_id;
                   var product_price_value = a.price_value;
                   var product_piece_in_set = a.piece_in_set;
                   var seller_id = a.seller_id;
                   var category_ids = a.category_id;

                   var category_id_split = category_ids.split(',');
                   // condition for category
                   var unique_product = [];
                   var all_store_product = [];

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

                   var i = '0';

                   var previousProduct = JSON.parse(localStorage.getItem("recentWishList"));
                   //localStorage.removeItem("recentWishList");

                   var dt = new Date();
                    var dateTime = (dt.getFullYear() +"/"+ (dt.getMonth()+1) +"/"+ dt.getDate()+" "+dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds());
                   $.each(category_id_split, function(index, value) {
                       var unique_key = product_id + '_' + value;
                       unique_product.push(unique_key);
                       var price_per_piece = (product_price_value * product_piece_in_set).toFixed(2);

                       var all_store_product_array = {
                           'customer_id': customer_id,
                           'product_id': product_id,
                           'quantity': '1',
                           'piece_in_set': product_piece_in_set,
                           'price_per_piece': price_per_piece,
                           'seller_id': seller_id,
                           'category_id': value,
                           'dateTime': dateTime
                       };
                       all_store_product[i] = all_store_product_array;
                       i++;
                   });

                   if (localStorage.getItem("recentWishList")) {
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

                   }
                    if(all_store_product.length>0){
                   localStorage.recentWishList = JSON.stringify(all_store_product);
                   }

                   //======Store value in local storage end=========//
          
         $('#notification').fadeOut(2000); 
         setTimeout(function() {
          $('#notification').remove();
        }, 2000);

      }
    });
  }


 function web_engage_product_detail (product_id, quantity, event, cart_summary='') 
 {
      axios({
           method:'post',
           url:'./api/product/getProductDetailsByProductId',
           data: 'product_id_string=' + product_id,
           responseType:'json'
          })
      .then(response => {
         if(response.data)
         {
          $.each( response.data, function( i, result ) {
          if(quantity == 0) { quantity = result.quantity;  }
          dataLayer.push({'category_id': result.category_id.toString()});
          dataLayer.push({'category': result.name});
          dataLayer.push({'product_id': result.product_id.toString()});
          dataLayer.push({'title': result.name.substr(0, 100)});
          dataLayer.push({'quantity': parseInt(quantity, 10)});
          dataLayer.push({'model': result.model});
          dataLayer.push({'brand': result.brand_name});
          dataLayer.push({'price': result.price_value});
          dataLayer.push({'special': parseInt(result.special_price, 10)});
          dataLayer.push({'options': result.options[0]});
          dataLayer.push({'currency': getCookie('currency')});
          dataLayer.push({'images': result.image });
          dataLayer.push({'seller_id': parseInt(result.seller_id, 10) });
          dataLayer.push({'seller_nickname': result.seller_nickname });
          dataLayer.push({'meta_data': 
            {'meta_title': result.meta_title,
             'meta_keyword': result.meta_keyword,
             'meta_description': result.meta_description
            }
          });
          dataLayer.push({'event': event});

          if(event== 'we-custom-addtocart' || event== 'we-custom-removefromcart')
          {
            web_engage_cart_update(cart_summary);
          }
          
        });
        }
      });
 }

 function web_engage_cart_update(cart_summary='') 
 {
   if(cart_summary != '')
   {
     cart_datalayer_update(cart_summary.products, cart_summary.cart_summary.totals);
   }
   else
   {
      axios({
           method:'post',
           url:'./api/cart/getWebengageCartData',
           responseType:'json'
          })
      .then(response => { 
       var result = response.data;
       cart_datalayer_update(result.products, result.totals);
      });
    }  
 }


 function cart_datalayer_update(products=0, totals=0)
 {
       var product_data  = [];
       var total_value   = 0;
       var product_id    = [];
       var product_name  = [];
       var product_price = [];
       var product_url = [];

       if(totals)
       {
        total_value = totals.sub_total.value
       }
       
       if(products)
       {
        Object.keys(products).map((city) => 
         {
            Object.keys(products[city]).map((combo_product_key,key) =>
            {
                products[city][combo_product_key].map((item, key) =>
                {
                   product_data.push(item);
                   product_id.push(item.product_id);
                   product_name.push(item.name.substr(0, 100));
                   product_price.push(item.unformat_total);
                   product_url.push(item.href);
                });

            });

         });
        }

        dataLayer.push({'product_details': product_data});
        dataLayer.push({'cart_value': parseInt(total_value, 10)});
        dataLayer.push({'cart_item': parseInt(product_data.length, 10)});
        dataLayer.push({'product_ids': product_id.join()});
        dataLayer.push({'product_names': product_name.join()});
        dataLayer.push({'products_price': product_price.join()});
        dataLayer.push({'product_url': product_url.join()});
        dataLayer.push({'event': 'we-custom-updatecart'});
 }


 function web_engage_auto_wishlist() 
 {
    dataLayer.push({'quantity': parseInt(0, 10)});
    dataLayer.push({'event': 'we-custom-addtowishlist'});
 }

  var elem = document.getElementById("myBar");   
  var width = 1;
  var id = setInterval(frame, 30);
  $("#myProgress").show();
  function frame() {
    if (width >= 100) {
      clearInterval(id);
      $("#myProgress").hide();
    } else {
      width++; 
      elem.style.width = width + '%'; 
    }
  }