import TagManager from 'react-gtm-module'
import Api from '../api/Api'
import custom from './custom'
import $ from 'jquery';

let gtmId= 'GTM-TRP9H5';

if(window.location.hostname === 'www.wholesalebox.co')
{
  gtmId= 'GTM-MBB945';
}

var webengage = {
  login: function (data, international_store) 
  {
     var gst = 0;
     var dropshipper = false;
     if(data.gst_number && data.gst_number !== '') { gst = 1; }
     if(data.is_dropshipper && parseInt(data.is_dropshipper, 0) > 0) { dropshipper = true; }  

    let tagManagerArgs = {
     gtmId: gtmId,
     dataLayer: {
        customer_id: data.customer_id,
        first_name: data.first_name,
        last_name: data.last_name,
        email: data.email,
        phone:data.telephone,
        customer_type:data.customer_type,
        store_id: international_store,
        user_city: data.user_city,
        gst:gst,
        dropshipper:dropshipper,
        has_website:parseInt(data.has_website, 0),
        self_order:data.self_order.toString(),
        pincode:parseInt(data.pincode, 10),
        membership: data.membership.membership.toString(),
        membership_id: data.membership.membership_id.toString(),
        expiry_date: data.membership.expiry_date.toString()
      },
      events: {
        event: 'we-custom-login'
     }
    }
    TagManager.initialize(tagManagerArgs);
   },

  register: function (mode, data, international_store) 
   {
     var gst = 0;
     var dropshipper = false;
     if(data.gst_number && data.gst_number !== '') { gst = 1; }
     if(data.is_dropshipper && parseInt(data.is_dropshipper, 0) > 0) { dropshipper = true; }

     let tagManagerArgs = {
     gtmId: gtmId,
     dataLayer: {
        mode: mode,
        customer_id: data.customer_id,
        first_name: data.first_name,
        last_name: data.last_name,
        email: data.email,
        phone:data.telephone,
        customer_type:data.customer_type,
        store_id: international_store,
        user_city: data.user_city,
        gst:gst,
        dropshipper:dropshipper,
        has_website:parseInt(data.has_website, 0),
        self_order:data.self_order.toString(),
        pincode:parseInt(data.pincode, 10),
        membership: data.membership.membership.toString(),
        membership_id: data.membership.membership_id.toString(),
        expiry_date: data.membership.expiry_date.toString()
      },
      events: {
        event: 'we-custom-registration'
     }
    }
    TagManager.initialize(tagManagerArgs);
   },

   logout: function () 
   {
     let tagManagerArgs = {
     gtmId: gtmId,
      events: {
        event: 'we-custom-logout'
     }
    }
    TagManager.initialize(tagManagerArgs);
   },

   web_engage_product_detail: function (product_id, quantity, event, event2='') 
   {

      $.ajax({
           type:'post',
           url: Api.api_url+'api/product/getProductDetailsByProductId',
           data: 'product_id_string=' + product_id,
           dataType:'json',

        success: function(response)
        {
          $.each( response, function( i, result ) {
          if(quantity === 0) { quantity = result.quantity;  }
    
         
        let tagManagerArgs = {
        gtmId: gtmId,
        dataLayer: {
           category_id: result.category_id.toString(),
           category: result.name,
           product_id: result.product_id.toString(),
           title: result.name.substr(0, 100),
           quantity:parseInt(quantity, 10),
           model:result.model,
           brand:result.brand_name,
           price:result.price_value,
           special:parseInt(result.special_price, 10),
           seller_id:parseInt(result.seller_id, 10),
           seller_nickname:result.seller_nickname,
           options: result.options[0],
           currency:custom.getCookie('currency'),
           images:result.image,
          meta_data:{'meta_title': result.meta_title,
             'meta_keyword': result.meta_keyword,
             'meta_description': result.meta_description
            }
         },
         events: {
            event: event
           }
        }
        TagManager.initialize(tagManagerArgs);

          if(event=== 'we-custom-addtocart' || event=== 'we-custom-removefromcart')
          {
            webengage.web_engage_cart_update();
          }
          
          });
      
       }
      });
    },

    web_engage_cart_update: function () 
    {
         var customer_id     = custom.getCookie('customer_id');
         var customer_access_token = custom.getCookie('customer_access_token');
         var cart_session_id = custom.getCookie('cart_session_id'); 
         var wishlist_session_id = custom.getCookie('wishlist_session_id'); 

         $.ajax({
             type:'post',
             url : Api.api_url+'api/cart/getWebengageCartData',
             data: 'customer_id=' + customer_id+'&customer_access_token='+customer_access_token+'&cart_session_id='+cart_session_id+'&wishlist_session_id='+wishlist_session_id,
             dataType:'json',
           success: function(response)
          {
             var result = response;
             webengage.cart_datalayer_update(result.products, result.totals);
          } 
        });
    },


  cart_datalayer_update: function (products=0, totals=0)
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
                   return true;
                });
              return true;
            });
             return true;
         });
        }

        let tagManagerArgs = {
        gtmId: gtmId,
        dataLayer: {
           product_details: product_data,
           cart_value: parseInt(total_value, 10),
           cart_item: parseInt(product_data.length, 10),
           product_ids: product_id.join(),
           product_names:product_name.join(),
           products_price:product_price.join(),
           product_url:product_url.join(),
         },
         events: {
            event: 'we-custom-updatecart'
           }
        }
        TagManager.initialize(tagManagerArgs);
    },

   category_view: function (data) 
   {
      var category = '';
      var category_id = 0;

      var sorted_by = data.sorts[0].text;
      data.sorts.map(function(sort, index) {
         if(sort.selected === 'active')
          {
             sorted_by = sort.text;
          }
           return true;
      });

      if (data.category_info && data.category_info.category_id > 0) 
        {
          category = data.category_info.name;
          category_id = data.category_info.category_id;
        }
      
       let tagManagerArgs = 
       {
         gtmId: gtmId,
         dataLayer: {
            item_count: parseInt(data.product_total, 10),
            sorted_by: sorted_by,
            filter: data.filters,
            category_id: category_id.toString(),
            category:category,
          },
         events: {
            event: 'we-custom-category-view'
          }
        }
      TagManager.initialize(tagManagerArgs);

   },

  sub_category_view: function (data) 
   {
      var category = '';
      var category_id = 0;
      var sub_category = '';
      var sub_category_id = 0;

      var sorted_by = data.sorts[0].text;
      data.sorts.map(function(sort, index) {
         if(sort.selected === 'active')
          {
             sorted_by = sort.text;
          }
         return true;  
      });

      if (data.category_info && data.category_info.category_id > 0) 
        {
          category = data.category_info.name;
          category_id = data.category_info.category_id;
        }
      if (data.parent_category_info && data.parent_category_info.category_id > 0) 
        {
          category = data.parent_category_info.name;
          category_id = data.parent_category_info.category_id;
          sub_category = data.category_info.name;
          sub_category_id = data.category_info.category_id;  
        }

       let tagManagerArgs = 
        {
          gtmId: gtmId,
          dataLayer: {
            item_count: parseInt(data.product_total, 10),
            sorted_by: sorted_by,
            filter: data.filters,
            category_id: category_id.toString(),
            category:category,
            sub_category_id:sub_category_id.toString(),
            sub_category:sub_category,
          },
         events: {
            event: 'we-custom-sub-category-view'
          }
        }
       TagManager.initialize(tagManagerArgs);
   },  

   product_search: function (data) 
   {
      var category = '';
      var category_id = 0;
      var sub_category = '';
      var sub_category_id = 0;

      var sorted_by = data.sorts[0].text;
      data.sorts.map(function(sort, index) {
         if(sort.selected === 'active')
          {
             sorted_by = sort.text;
          }
         return true;  
      });

      if (data.category_info && data.category_info.category_id > 0) 
        {
          category = data.category_info.name;
          category_id = data.category_info.category_id;
        }
      if (data.parent_category_info && data.parent_category_info.category_id > 0) 
        {
          category = data.parent_category_info.name;
          category_id = data.parent_category_info.category_id;
          sub_category = data.category_info.name;
          sub_category_id = data.category_info.category_id;  
        }

       let tagManagerArgs = 
       {
         gtmId: gtmId,
         dataLayer: {
           search: data.search,
           item_count: parseInt(data.product_total, 10),
           sorted_by: sorted_by,
           filter: data.filters,
           category_id:category_id.toString(),
           category:category,
           sub_category_id:sub_category_id.toString(),
           sub_category:sub_category
         },
        events: {
           event: 'we-custom-product-search'
         }
       }
      TagManager.initialize(tagManagerArgs);
   },

  product_view: function (data) 
   {
      var category = '';
      var category_id = 0;
      var sub_category = '';
      var sub_category_id = 0;
      var special_price = 0;
      var brand_name = '';

      if(data.special_price)
       {
             special_price = data.special_price;
       }
      if (data.category_info && data.category_info.category_id > 0) 
       {
          category    = data.category_info.name;
          category_id = data.category_info.category_id;
       }
      if (data.parent_category_info && data.parent_category_info.category_id > 0) 
      {
         category        = data.parent_category_info.name;
         category_id     = data.parent_category_info.category_id;
         sub_category    = data.category_info.name;
         sub_category_id = data.category_info.category_id;  
      }

      if (data.filters) 
      {
           brand_name = data.filters.map(function(filter, i){
              if(filter.group_name === 'Brand Name')
              {
                return filter.filter_name;
              }
              else
              {
                return false;
              }
           });
      }
     
      let tagManagerArgs = {
        gtmId: gtmId,
        dataLayer: {
          category_id: category_id.toString(),
          category: category,
          sub_category_id: sub_category_id.toString(),
          sub_category: sub_category,
          product_id:data.product_id.toString(),
          title:data.product_name.substr(0, 100),
          quantity:parseInt(data.quantity, 10),
          model:data.model,
          brand:brand_name,
          price:data.price_value,
          special:parseInt(special_price, 10),
          seller_id:parseInt(data.seller_id, 10),
          seller_nickname:data.seller_nickname,
          options:data.options[0],
          currency:custom.getCookie('currency'),
          images:data.image,
          meta_data: {'meta_title': data.meta_title,
             'meta_keyword': data.meta_keyword,
             'meta_description': data.meta_description
            }  
         },
         events: {
            event: 'we-custom-product-view'
          }
      }
      TagManager.initialize(tagManagerArgs);  

   },  

  checkout_start: function (cart_item, number_of_unique_sku, cart_value) 
   {
      let tagManagerArgs = {
        gtmId: gtmId,
        dataLayer: {
          cart_item: parseInt(cart_item, 10),
          number_of_unique_sku: parseInt(number_of_unique_sku, 10),
          cart_value:  parseInt(cart_value, 10)
        },
        events: {
          event: 'we-custom-checkout-started'
          }
        }
        TagManager.initialize(tagManagerArgs);
   }

}

export default webengage;