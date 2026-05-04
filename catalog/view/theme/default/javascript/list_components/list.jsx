 class List extends React.Component {

   constructor(props)
   {
     super(props);
     this.pagination_data           = this.pagination_data.bind(this);
     this.pagination_data_worker    = this.pagination_data_worker.bind(this);
     this.load_data                 = this.load_data.bind(this);
     this.callAjaxToFilter          = this.callAjaxToFilter.bind(this);
     this.callAjaxToMultiFilter     = this.callAjaxToMultiFilter.bind(this);
     this.callAjaxToRating          = this.callAjaxToRating.bind(this);
     this.callAjaxToPrice           = this.callAjaxToPrice.bind(this);
     this.callAjaxToRemovePrice     = this.callAjaxToRemovePrice.bind(this);
     this.callAjaxToSort            = this.callAjaxToSort.bind(this);
     this.callAjaxToRemoveFilter    = this.callAjaxToRemoveFilter.bind(this);
     this.callAjaxToRemoveOption    = this.callAjaxToRemoveOption.bind(this);
     this.callAjaxToRemoveAllFilter = this.callAjaxToRemoveAllFilter.bind(this);
     this.callAjaxToOption          = this.callAjaxToOption.bind(this);
     this.callAjaxToStock           = this.callAjaxToStock.bind(this);
     this.addFilterInHistoryState   = this.addFilterInHistoryState.bind(this);
     this.getUrlParameter           = this.getUrlParameter.bind(this);
     this.productDetailPopup        = this.productDetailPopup.bind(this);
     this.resetPopup                = this.resetPopup.bind(this);
     this.search_out_of_stock       = this.search_out_of_stock.bind(this);
     
    this.state = {
      SITE_ENVIRONMENT: this.props.SITE_ENVIRONMENT,
      products: [],
      sorts: [],
      filters: [],
      options: [],
      promotion: [],
      filter_facets: false,
      single_store_alert: false,
      bandhani_alert:false,
      alert_thaan_dispatch:false,
      page: 1,
      search: this.props.search,
      stock_filter: this.props.stock_filter,
      search_filter: this.props.filters,
      search_option: this.props.options,
      search_price:  this.props.price_filter,
      search_rating: this.props.rating_filter,
      search_sale: this.props.search_sale,
      search_sort: 'sort_order&order=ASC',
      product_count: -1,
      page_url: 'index.php?route=react/list&path='+this.props.path,
      is_popup: 0,
      popup_id: 0,
      popup_product_data: false,
      filter_show: 1,
      logged:false,
      popup_product_language: false,
      preload_img_string:false,
      is_initial:0,
      popup_type:0,
      handpicked_ids:false,
      random_string:false,
      product_total:0,
      show_limit:28,
      price_with_currency:false,
      filter_rating:false,
      last_filter_action:false,
      client_preferences:'',
      csv_req:this.props.csv_req,
      custom_store_val:this.props.custom_store_val,
      menus:false,
      hide_price:this.props.hide_price,
      location:this.props.loc,
      custom_title:this.props.custom_title,
      is_custom:this.props.is_custom,
      store_product:this.props.store_product,
      purchase_days:this.props.purchase_days,
      store_code:this.props.store_code
    };
    
    var hash = window.location.hash;

    if(window.location.hash) 
    {
        var filter_arr_str = this.getUrlParameter('filter',hash);
        if(filter_arr_str && typeof filter_arr_str != 'undefined')
        {
          var arr_filter_data = filter_arr_str.split(',') ;
          arr_filter_data = arr_filter_data.filter(Boolean)
          var filter = this.state.search_filter;
          if(arr_filter_data.length > 0) {
            $.each(arr_filter_data, function (index, value) {
            filter.push(value);
            });
           }
          this.state.search_filter = filter;
        } 

        var option_str = this.getUrlParameter('option',hash);
        
        if(option_str && typeof option_str != 'undefined')
        {
         var arr_option_data = option_str.split(',') ;
         arr_option_data = arr_option_data.filter(Boolean)

          var option = this.state.search_option;
          if(arr_option_data.length > 0) {
            $.each(arr_option_data, function (index, value) {
               option.push(value);
            });
          }
         this.state.search_option = option;
        }

        var search_price = this.getUrlParameter('price_filter',hash);
        if(search_price && typeof search_price != 'undefined')
        {
          this.state.search_price = search_price;
        }  

        var search_sort = this.getUrlParameter('sort',hash);
        if(search_sort && typeof search_price != 'undefined')
        {
          var sorting_order = this.getUrlParameter('order',hash);
          if(sorting_order && typeof sorting_order != 'undefined')
          {
            search_sort = search_sort+ "&order=" +sorting_order;  
          }
          this.state.search_sort = search_sort;
        }

        var search_rating = this.getUrlParameter('rating_filter',hash);
        if(search_rating && typeof search_rating != 'undefined')
        {
          this.state.search_rating = search_rating;
        }

        var search = this.getUrlParameter('search',hash);
        if(search && typeof search != 'undefined')
        {
          this.state.search = search;
        }

        var stock_filter = this.getUrlParameter('stock_filter',hash);
        if(stock_filter && typeof stock_filter != 'undefined')
        {
          this.state.stock_filter = stock_filter;
        }

        var last_filter_action = this.getUrlParameter('last_filter_action',hash);
        if(last_filter_action && typeof last_filter_action != 'undefined')
        {
          this.state.last_filter_action = last_filter_action;
        }

        var csv_req = this.getUrlParameter('csv_req',hash);
        if(csv_req && typeof csv_req != 'undefined')
        {
          this.state.csv_req = csv_req;
        }
      
        var search_sale = this.getUrlParameter('clearance_sale',hash);
        if(search_sale && typeof search_sale != 'undefined')
        {
         this.state.search_sale = search_sale;   
        }

    }

    
   } 

  componentDidMount()
  { 
     
     var search_filter = this.state.search_filter.join();
     var search_option = this.state.search_option.join();

    var num='0';
    var unique_product = [];
    var client_preferences_arry = [];
     if (localStorage.getItem("recentView")) {
     var previousProduct = JSON.parse(localStorage.getItem("recentView"));

             $.each(previousProduct, function(index, value) {
                 var unique_key = value.product_id + '_' + value.category_id;
                 //=====check for dublicate Value========
                 if ($.inArray(unique_key, unique_product) < '0') {
                     unique_product.push(unique_key);
                     var all_store_product_array = {
                         'customer_id': value.customer_id,
                         'product_id': value.product_id,
                         'quantity': '1',
                         'piece_in_set': value.piece_in_set,
                         'price_per_piece': value.price_per_piece,
                         'seller_id': value.seller_id,
                         'category_id': value.category_id,
                         'type': '2'
                     };
                     client_preferences_arry[num] = all_store_product_array;
                     num++;

                 }
                 if (num == "30") {
                     return false;
                 }

             });

           //localStorage.allClientPreferencesProduct = JSON.stringify(client_preferences);   

         }

          if (localStorage.getItem("recentWishList")) {
          var previousProduct = JSON.parse(localStorage.getItem("recentWishList"));
                       $.each(previousProduct, function(index, value) {
                           var unique_key = value.product_id + '_' + value.category_id;
                           //=====check for dublicate Value========
                           //if ($.inArray(unique_key, unique_product) < '0') {
                           var dt = new Date();
                    var dateTime = (dt.getFullYear() +"/"+ (dt.getMonth()+1) +"/"+ dt.getDate()+" "+dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds());

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
                                   'type': '1'
                               };
                               client_preferences_arry[num] = all_store_product_array;
                               num++;
                               }

                          // }

                       });

                   }
                   this.setState({client_preferences: client_preferences_arry});
                   var client_preferences=JSON.stringify(client_preferences_arry);
                   
     var search = this.state.search;               
    if(this.state.csv_req == 1) { var url_csv_req = '&csv_req=1'; } else { var url_csv_req = ''; }
       $.ajax({
            url:'./api/category/product_list&path='+this.props.path+'&page='+this.state.page+'&filter='+search_filter+'&option='+search_option+'&rating_filter='+this.state.search_rating+'&price_filter='+this.state.search_price+'&sort='+this.state.search_sort+'&search='+search+'&stock_filter='+this.state.stock_filter+'&clearance_sale='+this.state.search_sale+'&last_filter_action='+this.state.last_filter_action+url_csv_req+'&location='+this.state.location+'&store_product='+this.state.store_product+'&purchase_days='+this.state.purchase_days+'&store_code='+this.state.store_code,
            type: 'post',
            data: 'client_preferences='+client_preferences,
            dataType: 'json',
        }).promise()
        .then(function(response){
           
            if(response.statusCode == 902)
            {
              this.setState({product_count: -2});
              $("body").removeClass("loading").addClass("loaded"); 
              return false;
            }

            var sorted_by = response.data.sorts[0].text;
            response.data.sorts.map(function(sort, index) {
               if(sort.selected == 'active')
               {
                 sorted_by = sort.text;
               }
             }); 


             var category = '';
             var category_id = 0;
             var sub_category = '';
             var sub_category_id = 0;

             if (response.data.category_info && response.data.category_info.category_id > 0) 
             {
               category = response.data.category_info.name;
               category_id = response.data.category_info.category_id;
             }
             if (response.data.parent_category_info && response.data.parent_category_info.category_id > 0) 
             {
               category = response.data.parent_category_info.name;
               category_id = response.data.parent_category_info.category_id;
               sub_category = response.data.category_info.name;
               sub_category_id = response.data.category_info.category_id;  
             }

             dataLayer.push({'item_count': parseInt(response.data.product_total, 10)});
             dataLayer.push({'sorted_by': sorted_by});
             dataLayer.push({'filter': response.data.filters});
             dataLayer.push({'category_id': category_id.toString()});
             dataLayer.push({'category': category});
             dataLayer.push({'sub_category_id': sub_category_id.toString()});
             dataLayer.push({'sub_category': sub_category});

             if(this.state.search != '')
             {
                 dataLayer.push({'search': this.state.search});
                 dataLayer.push({'event': 'we-custom-product-search'});
             }
             else if(sub_category_id > 0)
             {
                 dataLayer.push({'event': 'we-custom-sub-category-view'});
             }
             else
             {
               dataLayer.push({'event': 'we-custom-category-view'});
             }
             
            $("html, body").animate({ scrollTop: 0 });
            this.setState({products: response.data.products});
            this.setState({sorts: response.data.sorts});
            this.setState({filters: response.data.filters});
            this.setState({options: response.data.options});
            this.setState({page: parseInt(response.data.page)});
            this.setState({logged: parseInt(response.data.logged)});
            this.setState({product_count: response.data.products.length});
            this.setState({product_total: response.data.product_total});
            this.setState({handpicked_ids: response.data.handpicked_ids});
            this.setState({random_string: response.data.random_string});
            this.setState({show_limit: response.data.show_limit});
            this.setState({single_store_alert: response.data.single_store_alert});
            this.setState({bandhani_alert: response.data.bandhani_alert});
            this.setState({alert_thaan_dispatch: response.data.alert_thaan_dispatch});
            this.setState({price_with_currency: response.data.price_with_currency});
            this.setState({filter_rating: response.data.filter_rating});
            this.setState({menus: response.data.menus});
            this.setState({promotion: response.data.promotion});
             if(response.data.filter_facets == '')
             {
               this.setState({filter_facets: '-1'});
             }
             else
             {
               this.setState({filter_facets: response.data.filter_facets});
             }

            if(this.state.product_count == this.state.product_total)
            {
              if(this.state.is_custom)
               {
                 $(".custom_load_btn").html("View More Products");
                 $(".custom_load_btn").hide();
               }
               else
               {
                 $(".btn_browse_more").removeClass("page_auto_load");
                 $(".btn_browse_more").addClass("last_page");
               }
            }

            if(!window.location.hash && response.data.product_count == 0) 
            {
               this.setState({filter_show: 0});
            }

        }.bind(this))
        .fail(function(xhr) {
         this.setState({product_count: -2});
        }.bind(this));

   
   if(!this.state.is_custom)
   {
     var self = this;
     $(window).scroll(function(){
      if($(".btn_browse_more").hasClass( "page_auto_load" ))
      { 
        let pos_browse_more = $('.page_auto_load').offset().top;
        let pos_footer = $('.social_section').offset().top;
        let pos_scroll = $(window).scrollTop();
         if (pos_scroll > (pos_browse_more-2000) && pos_scroll < (pos_footer-1000))
         {
           self.pagination_data_worker();
         }
      }   
      });
    }

  }

  load_data(filter_retain=0){
    $(".more_search_filter").modal("hide");
    $('#list_page').addClass('blur');
    $("body").removeClass("loaded").addClass("loading");
    $("html, body").animate({ scrollTop: 0 }, 800);
    var search_filter = this.state.search_filter.join();
    var search_option = this.state.search_option.join();
    var search = this.state.search;

    if(filter_retain == 1) { var url_filter_retain = '&filter_retain=1'; } else { var url_filter_retain = ''; }
    if(this.state.csv_req == 1) { var url_csv_req = '&csv_req=1'; } else { var url_csv_req = ''; }

    this.addFilterInHistoryState();
     $.ajax({
      type:'get',
      url:'./api/category/product_list&path='+this.props.path+'&page='+this.state.page+'&filter='+search_filter+'&option='+search_option+'&rating_filter='+this.state.search_rating+'&price_filter='+this.state.search_price+'&sort='+this.state.search_sort+'&search='+search+'&stock_filter='+this.state.stock_filter+'&clearance_sale='+this.state.search_sale+'&handpicked_ids='+this.state.handpicked_ids+'&random_string='+this.state.random_string+'&product_total='+this.state.product_total+'&last_filter_action='+this.state.last_filter_action+url_filter_retain+url_csv_req+'&location='+this.state.location+'&store_product='+this.state.store_product+'&purchase_days='+this.state.purchase_days+'&store_code='+this.state.store_code,
      dataType:'json'
     }).promise()
    .then(function(response){

            if(response.statusCode == 902)
            {
              this.setState({product_count: -2});
              $("body").removeClass("loading").addClass("loaded"); 
              return false;
            }

            if(filter_retain == 0)
            { 
              if(response.data.filter_facets == '')
              {
               this.setState({filter_facets: '-1'});
              }
              else
              {
                this.setState({filter_facets: response.data.filter_facets});
              }
              this.setState({price_with_currency: response.data.price_with_currency});
              this.setState({filter_rating: response.data.filter_rating});
            } 

            var sorted_by = response.data.sorts[0].text;
            response.data.sorts.map(function(sort, index) {
               if(sort.selected == 'active')
               {
                 sorted_by = sort.text;
               }
             }); 


             var category = '';
             var category_id = 0;
             var sub_category = '';
             var sub_category_id = 0;

             if (response.data.category_info && response.data.category_info.category_id > 0) 
             {
               category = response.data.category_info.name;
               category_id = response.data.category_info.category_id;
             }
             if (response.data.parent_category_info && response.data.parent_category_info.category_id > 0) 
             {
               category = response.data.parent_category_info.name;
               category_id = response.data.parent_category_info.category_id;
               sub_category = response.data.category_info.name;
               sub_category_id = response.data.category_info.category_id;  
             }

             dataLayer.push({'item_count': parseInt(response.data.product_total, 10)});
             dataLayer.push({'sorted_by': sorted_by});
             dataLayer.push({'filter': response.data.filters});
             dataLayer.push({'category_id': category_id.toString()});
             dataLayer.push({'category': category});
             dataLayer.push({'sub_category_id': sub_category_id.toString()});
             dataLayer.push({'sub_category': sub_category});

             if(this.state.search != '')
             {
                 dataLayer.push({'search': this.state.search});
                 dataLayer.push({'event': 'we-custom-product-search'});
             }
             else if(sub_category_id > 0)
             {
                 dataLayer.push({'event': 'we-custom-sub-category-view'});
             }
             else
             {
               dataLayer.push({'event': 'we-custom-category-view'});
             }


            this.setState({products: response.data.products});
            this.setState({sorts: response.data.sorts});
            this.setState({filters: response.data.filters});
            this.setState({options: response.data.options});
            this.setState({page: parseInt(response.data.page)});
            this.setState({product_count: response.data.products.length});
            this.setState({product_total: response.data.product_total});
            this.setState({handpicked_ids: response.data.handpicked_ids});
            this.setState({random_string: response.data.random_string});
            this.setState({show_limit: response.data.show_limit});
            this.setState({logged: parseInt(response.data.logged)});
            this.setState({single_store_alert: response.data.single_store_alert});
            this.setState({bandhani_alert: response.data.bandhani_alert});
            this.setState({alert_thaan_dispatch: response.data.alert_thaan_dispatch});
            
             $('#list_page').removeClass('blur');

             if(this.state.product_count == this.state.product_total)
             {
               if(this.state.is_custom)
               {
                 $(".custom_load_btn").html("View More Products");
                 $(".custom_load_btn").hide();
               }
               else
               {
                  $(".btn_browse_more").removeClass("page_auto_load");
                  $(".btn_browse_more").addClass("last_page");
                  $(".custom_load_btn").removeClass("loading");
               }  

             }
             else
             {
               if(this.state.is_custom)
               {
                $(".custom_load_btn").html("View More Products");
                $(".custom_load_btn").show();
               }
               else
               {
                $(".btn_browse_more").addClass("page_auto_load");
                $(".btn_browse_more").removeClass("last_page");
                $(".custom_load_btn").removeClass("loading");
               } 
             }

             $("body").removeClass("loading").addClass("loaded");
             $(".b-lazy").attr("src", cdn_url+"placeholder.png");
             $(".b-lazy").removeClass("b-loaded");
             $(".b-lazy").removeClass("b-error");
             $('[data-toggle="popover"]').popover(); 

        }.bind(this))
        .fail(function(xhr) {
         $('#list_page').removeClass('blur'); 
         $("body").removeClass("loading").addClass("loaded"); 
         this.setState({product_count: -2});
        }.bind(this));
   
  }


  pagination_data()
  {
   if(!$( ".custom_load_btn" ).hasClass( "loading" )) 
   {
     $(".custom_load_btn").addClass("loading");
     $(".custom_load_btn").html("Loading...");
     var self = this;
     var w;
     let page = this.state.page;
        page = page+1;
    this.state.page = page;
    var search_filter = this.state.search_filter.join();
    var search_option = this.state.search_option.join();
    var search = this.state.search;
    if(this.state.csv_req == 1) { var url_csv_req = '&csv_req=1'; } else { var url_csv_req = ''; }
    var workerUrl = './api/category/product_list&path='+this.props.path+'&page='+this.state.page+'&filter='+search_filter+'&option='+search_option+'&rating_filter='+this.state.search_rating+'&price_filter='+this.state.search_price+'&sort='+this.state.search_sort+'&search='+search+'&stock_filter='+this.state.stock_filter+'&clearance_sale='+this.state.search_sale+'&handpicked_ids='+this.state.handpicked_ids+'&random_string='+this.state.random_string+'&product_total='+this.state.product_total+'&last_filter_action='+this.state.last_filter_action+'&filter_retain=1'+url_csv_req+'&location='+this.state.location+'&store_product='+this.state.store_product+'&purchase_days='+this.state.purchase_days+'&store_code='+this.state.store_code;
    w=new Worker("react_worker.js");
    w.postMessage({ "url": workerUrl});
     w.onmessage = function (event) {
                  $(".custom_load_btn").removeClass("loading");
                  $(".custom_load_btn").html("View More Products");
                  var pos_footer = $('.social_section').offset().top;
                   var pos_scroll = $(window).scrollTop();
                   var response= $.parseJSON(event.data.result); 
                   self.setState({products: self.state.products.concat(response.data.products)});
                   self.setState({sorts: response.data.sorts});
                   self.setState({options: response.data.options});
                   self.setState({page: parseInt(response.data.page)});
                   self.setState({logged: parseInt(response.data.logged)});
                   self.setState({product_count: parseInt(response.data.products.length)+self.state.product_count});
                   self.setState({product_total: response.data.product_total});
                   self.setState({handpicked_ids: response.data.handpicked_ids});
                   self.setState({random_string: response.data.random_string});
             
                   if(self.state.product_count == self.state.product_total)
                    { 
                      $(".custom_load_btn").hide(); 
                    }
                  else
                    {
                      $(".custom_load_btn").show(); 
                    }
                    w.undefined;
                   if (pos_scroll > (pos_footer-200))
                    {
                       $("html, body").animate({ scrollTop: $(window).scrollTop()+4000 });
                    }
                };

    }            
  }


  pagination_data_worker()
  {
    var self = this;
    var w;
    $(".btn_browse_more").removeClass("page_auto_load");
    let page = this.state.page;
        page = page+1;
    this.state.page = page;
    var search_filter = this.state.search_filter.join();
    var search_option = this.state.search_option.join();
    var search = this.state.search;
    if(this.state.csv_req == 1) { var url_csv_req = '&csv_req=1'; } else { var url_csv_req = ''; }
    var workerUrl = './api/category/product_list&path='+this.props.path+'&page='+this.state.page+'&filter='+search_filter+'&option='+search_option+'&rating_filter='+this.state.search_rating+'&price_filter='+this.state.search_price+'&sort='+this.state.search_sort+'&search='+search+'&stock_filter='+this.state.stock_filter+'&clearance_sale='+this.state.search_sale+'&handpicked_ids='+this.state.handpicked_ids+'&random_string='+this.state.random_string+'&product_total='+this.state.product_total+'&last_filter_action='+this.state.last_filter_action+'&filter_retain=1'+url_csv_req+'&location='+this.state.location+'&store_product='+this.state.store_product+'&purchase_days='+this.state.purchase_days+'&store_code='+this.state.store_code;
    w=new Worker("react_worker.js");
    w.postMessage({ "url": workerUrl});
     w.onmessage = function (event) {
     	           var pos_footer = $('.social_section').offset().top;
                   var pos_scroll = $(window).scrollTop();

                   var response= $.parseJSON(event.data.result); 
                   self.setState({products: self.state.products.concat(response.data.products)});
                   self.setState({sorts: response.data.sorts});
                   //self.setState({filters: response.data.filters});
                   self.setState({options: response.data.options});
                   self.setState({page: parseInt(response.data.page)});
                   self.setState({logged: parseInt(response.data.logged)});
                   self.setState({product_count: parseInt(response.data.products.length)+self.state.product_count});
                   self.setState({product_total: response.data.product_total});
                   self.setState({handpicked_ids: response.data.handpicked_ids});
                   self.setState({random_string: response.data.random_string});
             
                   if(self.state.product_count == self.state.product_total)
                    { 
                       $(".btn_browse_more").removeClass("page_auto_load");
                       $(".btn_browse_more").addClass("last_page");
                    }
                  else
                    {
                       $(".btn_browse_more").removeClass("last_page");
                       $(".btn_browse_more").addClass("page_auto_load");
                    }
                    w.undefined;
                   if (pos_scroll > (pos_footer-200))
                    {
                       $("html, body").animate({ scrollTop: $(window).scrollTop()+4000 });
                    }
                };
  }


  callAjaxToFilter(newValue) { 
      var filter = this.state.search_filter;
      if(newValue.target.checked)
      {
        filter.push(newValue.target.value);
      }
      else
      {
       filter = $.grep(filter, function( a ) {
             return a !== newValue.target.value;
             });
      }
      //this.setState({search_filter: filter});
      this.state.search_filter = filter;
      this.state.last_filter_action = "filter";
      this.state.page = 1;
      this.load_data();
    }

  callAjaxToMultiFilter(popup_filter, newValue)
  {
    var filter = this.state.search_filter;
    var group_id = newValue.props.groups.filter_group_id;

    popup_filter.map(function(new_value) 
    {
      filter.push(new_value);
    });

    $('#list-group-filter'+group_id+' input:not(:checked)').each(function() {
         var value = $(this).val();
       
         filter = $.grep(filter, function( a ) {
             return a !== value;
             }); 
    });

    var uniqueNames = [];
    $.each(filter, function(i, el){
    if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
    });

    this.state.search_filter = uniqueNames;
    this.state.last_filter_action = "filter";
    this.state.page = 1;
    this.load_data();
  }


   callAjaxToRemoveFilter(newValue) { 
      var filter = this.state.search_filter;
       filter = $.grep(filter, function( a ) {
             return a !== newValue.props.filter.filter_id;
             });
      this.state.search_filter = filter;
      this.state.last_filter_action = "filter";
      this.state.page = 1;
      this.load_data();
    }   

   callAjaxToRemoveAllFilter() { 
      var filter= [];
      var option= [];
      this.state.search_filter = filter;
      this.state.search_option = option;
      this.state.search_price  = 'all';
      this.state.page = 1;
      this.state.last_filter_action = "filter";
      this.load_data();
    }  

   callAjaxToOption(newValue) { 
      var option = this.state.search_option;
      if(newValue.target.checked)
      {
        option.push(newValue.target.value);
      }
      else
      {
       option = $.grep(option, function( a ) {
             return a !== newValue.target.value;
             });
      }
      this.state.search_option = option;
      this.state.page = 1;
      this.state.last_filter_action = "option";
      this.load_data();
    }

    callAjaxToRemoveOption(newValue) { 
      var option = this.state.search_option;
       option = $.grep(option, function( a ) {
             return a !== newValue.props.option.option_value_id;
             });
      this.state.search_option = option;
      this.state.page = 1;
      this.state.last_filter_action = "option";
      this.load_data();
    }    

  callAjaxToRating(newValue) { 
      this.state.search_rating = newValue.target.value;
      this.state.page = 1;
      this.state.last_filter_action = "rating";
      this.load_data();
    }

  callAjaxToPrice(newValue) { 
      this.state.search_price = newValue.target.value;
      this.state.page = 1;
      this.state.last_filter_action = "price";
      this.load_data();
    }

  callAjaxToRemovePrice() { 
      this.state.search_price = 'all';
      this.state.page = 1;
      this.state.last_filter_action = "price";
      this.load_data();
    }    

  callAjaxToSort(newValue) { 
      this.state.search_sort = newValue.props.sort.query_string;
      this.state.page = 1;
      this.load_data(1);
    }

  callAjaxToStock(newValue) { 
      this.state.stock_filter = newValue.target.value;
      this.state.page = 1;
      this.state.last_filter_action = "stock";
      this.load_data();
    }    
  search_out_of_stock(e)
  {
    this.state.stock_filter = 1;
    this.state.page = 1;
    this.state.last_filter_action = "stock";
    this.addFilterInHistoryState();
    location.reload();
  }

   addFilterInHistoryState(){
       var filter        = this.state.search_filter;
       var price_filter  = this.state.search_price;
       var option        = this.state.search_option;
       var sorting       = this.state.search_sort;
       var rating_filter = this.state.search_rating;
       var search        = this.state.search;
       var stock_filter  = this.state.stock_filter;
      var last_filter_action  = this.state.last_filter_action;

        if (window.location.hash) {
             var url = window.location.href;
             var url_page = url.split("&");
             var final_url = url_page[0].split("#!");

              if(final_url.length == 1 )
               {
                 var url_page2 = url_page[1].split("#!");
                 final_url = final_url+'&'+url_page2[0];
               }
               else
               {
                final_url = final_url[0];
               }

            var filter_string = final_url + "#!filter=" + filter.join() + "&price_filter=" + price_filter + "&option="+ option.join()+ "&sort="+$.trim(sorting) + "&rating_filter="+ rating_filter+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action;
        } else {
            var filter_string = window.location + "#!filter=" + filter.join() + "&price_filter=" + price_filter + "&option="+ option.join()+ "&sort="+$.trim(sorting) + "&rating_filter="+ rating_filter+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action;
        }
        history.pushState('', null, filter_string);
    }

  getUrlParameter(sParam,url) {
        var sPageURL = decodeURIComponent(url.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }

    productDetailPopup(e, p)
    {
        e.preventDefault();
        $("#product_popup").modal("show");
        if(p.props.product.product_id != '' && p.props.product.product_id > 0){
          this.state.is_popup = 1;
          this.state.popup_id = p.props.product.product_id;
          this.setState({popup_product_data: p.props.product});
          this.setState({preload_img_string: p.props.product.preload_img_string});
          this.state.is_initial = 1;
      }
    }    



    resetPopup(e)
    {
        $('.close').click();
        this.setState({is_popup: 0});
        this.setState({popup_id: 0});
    }  
    
  render() {

     let self = this;
     let product_item = '';
     if(this.state.product_count > 0){   

        product_item = this.state.products.map(function(product, index) {
         if(index%4==0){ var scroll_class = 'left_image_scroll'; } else { var scroll_class = ''; } 
        return <ProductItem key={index} count={index} hide_price={self.state.hide_price} logged={self.state.logged} SITE_ENVIRONMENT={self.state.SITE_ENVIRONMENT} product={product} clickHandlerPopup={self.productDetailPopup} list_page={1} scroll_class={scroll_class} />
     }) 
    }
      else if(this.state.product_count == 0)
     {
       product_item = <NoResults />
     }
     else
    {
    product_item = <ListLoadingProducts />
    }



function want_designe_list(e)
{
    var popup_comment = $("#popup_comment_list").val();
    var error = 'Fill out all the given field';
    var preOderPopup = $("#preOderPopupList").serialize();
    var customer_mobile  = $('#design_customer_mobile_list').val();
  
    if(getCookie("customer_mobile") == '')
    {
      var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
      var mobile_pattern = new RegExp(/^\d{10}$/);

      if (!email_pattern.test(customer_mobile) && !mobile_pattern.test(customer_mobile))
      {
          $('.alert_msg').remove();
          $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-danger">Please provide correct email address or mobile</div></div>');
          return false; 
      }  
    }

  if(popup_comment == '')
   {
     $('.alert_msg').remove();
     $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0px"><div class="alert alert-danger">'+error+'</div></div>');
      return false;
   }
  

   $(".want_designe_btn").html("please wait..."); 
   var ajax = $.ajax({
          type : "POST",
          url  : 'api/product/user_comment',
          data : preOderPopup,
          beforeSend: function() {
              if(ajax != null) { ajax.abort(); }
              $('.popup-footer .btn-default').button('loading');
            },
          complete: function() {
              $('.popup-footer .btn-default').button('reset');
              var ajax = null;
            },
          success: function(data){
              $(".want_designe_btn").html("Send");
              $('.alert_msg').remove();
              $("#popup_comment_list").val('');
              $("#design_customer_mobile_list").val('');
              $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-success"><i class="fa fa-check-circle"></i>'+data['message']+'</div></div>');
            }
            });
   
}


$(".pd_detail_popup_btn").click(function() {
  $('.zoomContainer').remove();
})

if(this.props.hide_price == 1)
{
  var list_section = 12;
}
else
{
  var list_section = 10; 
}


if(this.state.product_count == -2)
{
  
  return (<ApiError retry={this.load_data} />)

}
else if(this.state.search != '' && this.state.product_count == 0)
{
  
  return (<SearchNoResults search={this.state.search} stock_filter={this.state.stock_filter} search_out_of_stock={this.search_out_of_stock} menus={this.state.menus} />)

}
else if(!this.state.filter_show)
{
    return (<section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12">
      <section className="col-sm-12 privacy_text">
          <div className="wrong_search_box">
            <h3 className="wrong_search_text"><label><i className="fa fa-info-circle" aria-hidden="true"></i></label> Sorry! No products matched "{this.props.category_info.name}"</h3>
            <ul className="wrong_search_box_ul">
            <li>Check your spelling.</li>
            <li>Use different categories and try again.</li>
            </ul>
          </div>
          </section>

        </div>
        </div>
       </div> 
    </section>)
}
else
{

   var catCatalogCount='0';
    if(typeof this.state.filter_facets.filters != 'undefined' && this.state.filter_facets.filters.length > 0) {
                Object.keys(this.state.filter_facets.filters).map((item, key) =>{
                        if (this.state.filter_facets.filters[item]['group_label'].toLowerCase() == "brand name") {
                            var produCountVal='0';
                            $.each(this.state.filter_facets.filters[item]['filter'], function( index, value ) {
                              if(value.product_count>0){
                              produCountVal++;
                              }
                            });
                            if(produCountVal > '0'){
                              catCatalogCount++;
                            }
                             
                        }

                    })
            }

           if(typeof this.state.search_price != 'undefined'  && (this.state.search_price == '' || this.state.search_price=='all')){
            catCatalogCount++;
           } 
return (
        <section>
        <div id="product_popup" className="modal fade product_popup_box" role="dialog" data-backdrop="static" data-keyboard="false">
         <div className="modal-dialog pd_detail_popup_wh">
            <div className="modal-content">
                <div className="modal-header pd_detail_popup_head">
                    <button onClick = {this.resetPopup} type="button" className="close pd_detail_popup_btn" data-dismiss="modal">&times;</button> 
                </div> 
                <div className="modal-body"> 
                  {this.state.popup_id ? 
                  <ProductDetail custom_store_val={this.state.custom_store_val} is_popup={this.state.is_popup} SITE_ENVIRONMENT={this.state.SITE_ENVIRONMENT} product_id = {this.state.popup_id} popup_product_data={this.state.popup_product_data} popup_product_language={this.state.popup_product_language} preload_img_string={this.state.preload_img_string} customer_data = {this.props.customer_data} is_initial={this.state.is_initial} international_store={this.props.international_store} />   
                  : 
                   <img width="100%" src={cdn_url+"loading_product_details.jpg"} />
                  }
                </div>
            </div>
          </div>
        </div>
      
          
       <div className="container-fluid width_fix" id="list_page"> 

       <div>
        <div className="col-sm-12">
          <div className="row cdff">

           {!this.props.hide_price ?
            this.state.filter_facets ?
            <Filter international_store={this.props.international_store} filter_facets={this.state.filter_facets} price_with_currency={this.state.price_with_currency} rating_filter={this.state.filter_rating} path={this.props.path} category_info={this.props.category_info} clickHandler={this.callAjaxToFilter} clickHandlerRating={this.callAjaxToRating} clickHandlerStock={this.callAjaxToStock} clickHandlerPrice={this.callAjaxToPrice} clickHandlerOption={this.callAjaxToOption}  search_filter={this.state.search_filter}  search_option={this.state.search_option} search_price={this.state.search_price} search_rating={this.state.search_rating} stock_filter={this.state.stock_filter} international_store={this.props.international_store} clickHandlerMultiFilter={this.callAjaxToMultiFilter}  />
            : 
            <column className="col-sm-2 filtar_section nopadding"><FilterLoading /></column>
            : ''
           } 

            <column className={"col-sm-"+list_section+" category_section list_section margin_adjustment"}>
            {this.state.product_count != -1 ? 
             <section>
             <div className="clearfix"></div>
            {
            this.state.promotion.length >= 1 && catCatalogCount >=1?
            this.state.filter_facets && this.state.products.length > 8?
            <CategoryCatalog filter_array={this.state.filter_facets}  price_with_currency={this.state.price_with_currency} rating_filter={this.state.filter_rating} search_price={this.state.search_price} search_filter={this.state.search_filter}
            promotion={this.state.promotion} />
            :''
            :''
            }
            <div className="clearfix"></div>
           
             {this.state.custom_title != '' ?
               <h1>
               {$('<div/>').html(this.state.custom_title).text()} 
               </h1>
              : ''
              }
           
           
             {this.props.category_info.name != '' && this.state.custom_title == '' ?
              <h1> {$('<div/>').html(this.props.category_info.name).text()} </h1>
              : ''
              }

              {this.state.search != '' && this.state.custom_title == '' ?
              <h1> {'Search By: '+this.state.search.replace(/`/g, "&")} </h1>
              : ''
              }
             

             {this.state.single_store_alert ?
              <div className="alert alert-info"><i className="fa fa-check-circle"></i> 
                Info: {this.state.single_store_alert}
               </div>
              : ''
              }

             {this.state.bandhani_alert ?
              <div className="alert alert-warning"><i className="fa fa-check-circle"></i> 
                Warning: {this.state.bandhani_alert}
               </div>
              : ''
              }

              {this.state.alert_thaan_dispatch ?
              <div className="alert alert-warning"><i className="fa fa-check-circle"></i> 
                Warning: {this.state.alert_thaan_dispatch}
               </div>
              : ''
              } 
              
              {this.props.category_info.short_description ?
              <div className="short_description">
                <div className="category_short_description" dangerouslySetInnerHTML={{ __html: this.props.category_info.short_description }} />
               </div>
              : ''
              }
             
             <div className="col-sm-12 nopadding heading_bottem">    
             <div className="range_sort col-sm-8">
             {!this.props.hide_price ?
              <ul id="input-sort" className="sort_panel_bg">
                <li className="sortBy">Sort By:</li>
                {
                  this.state.sorts.map(function(sort, index) {
                    return <Sorts key={index} sort={sort} clickHandler={self.callAjaxToSort}  />
                  })
                }
              </ul>
              : ''}
            </div>

            {this.state.product_total > 0 ?
             <div className="product_count col-sm-4">
             (Showing 1 - {this.state.product_count} products of {this.state.product_total} products)
             </div>
             : ''}
             
            </div>
             </section>
              : '' }
            <div className="clearfix"></div>
             <section className="pd_top_padding_box">

             {this.state.product_count != -1 ? 
              this.state.filters != '' || (this.state.search_price != '' && this.state.search_price != 'all') ?
              <div className="selected_filtar col-sm-12">
               <ul>
                <li className="clear_filtar"><a href="javascript:;" data-id="all" onClick={this.callAjaxToRemoveAllFilter}>CLEAR ALL</a></li>
                
                {this.state.search_price && this.state.search_price != 'all' ?
                <li className="filter">{this.state.search_price}<label className="filtar_close_icon"  onClick={this.callAjaxToRemovePrice}></label></li>
                 : ''
                }
                 { this.state.filters ?
                  this.state.filters.map(function(filter, index) {
                    return <SearchFilter key={index} filter={filter} clickHandler={self.callAjaxToRemoveFilter} />
                  })
                  : ''
                 }

                {this.state.options ?
                  this.state.options.map(function(option, index) {
                    return <SearchOption key={index} option={option} clickHandler={self.callAjaxToRemoveOption} />
                  })
                 : '' }
               </ul> 
              </div>
              : '' : ''}
              <div className="clearfix"></div>

              <section className="list_section">
              {product_item}
              </section>
              </section>
             <div className="clearfix"></div>

              {!this.state.is_custom && this.state.product_count != -1 ?
              <div className="browse_more btn_browse_more page_auto_load">
              <span><i className="fa fa-circle-o-notch fa-spin"></i> Products Loading...</span> 
              </div>
              : ''}

              {this.state.is_custom && this.state.product_count != -1 ?
              <div className="browse_more btn_browse_more" style={{textAlign: 'center'}}>
               <button className="btn deliver_btn custom_load_btn" onClick={this.pagination_data} type="button">View More Products</button>
              </div>
              : ''}

            </column> 
             <div className="clearfix" id="filterScrollLimitPoint"></div>

             {this.props.category_info.description ? 
              this.props.category_info.description.length > 20 ?
             <div className="category_description" dangerouslySetInnerHTML={{ __html: this.props.category_info.description }} />
            : <div className="category_description no-border"></div>
            : <div className="category_description no-border"></div>
            }

             <div className="modal fade want_designe_popup question_popup" id="want_designe_popup" role="dailog">
                <div className="modal-dialog">
                <div className="modal-content">
                  <form id="preOderPopupList">
                   <input type="hidden" name="product_id" id="want_designe_product_id" />
                   <input type="hidden" name="product_status" value="out of stock" />
                      <div id="want_header_popup" className="modal-header want_header">
                        <button type="button" className="close" data-dismiss="modal">&times;</button>
                        <h4 className="modal-title">I want this design.</h4>
                      </div>

                       <div className="modal-body popup-q-body"> 
                          <div className="popup-title preorder_title_box">
                                <p>The product is out of stock but you could still ask factory if they can provide it. </p>
                                <p>Please specify how many pieces and what sizes/colors you like to have:</p>
                          </div>
                          <input type="text" name="customer_mobile" id="design_customer_mobile_list" placeholder="Mobile OR Email" style={{width:'100%', margin:'10px 0px'}} />
                          <textarea name="popup_comment" id="popup_comment_list" rows="6" className="popup_comment"></textarea>
                          <div className="clearfix"></div>
                          <div className="popup-footer">
                             <button type="button" className="btn deliver_btn want_designe_btn pull-right" onClick={want_designe_list}>Send </button>
                             <div className="clearfix"></div>
                          </div> 
                      </div>
                  </form>    
               </div>
               </div>
               </div>
          </div>
         </div>
        </div>  
      </div>
      </section>);
    }

  }
}
