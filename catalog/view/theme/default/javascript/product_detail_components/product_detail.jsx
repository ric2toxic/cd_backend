class ProductDetail extends React.Component {

    constructor(props){
        super(props);
        this.productSellerPopup  = this.productSellerPopup.bind(this);
        this.closepreOderPopup = this.closepreOderPopup.bind(this);
        this.getProductDeatil  = this.getProductDeatil.bind(this);
        this.state = {
           data: this.props.popup_product_data,
           language: this.props.popup_product_language,
           is_initial :this.props.is_initial,
           preload_img_string: this.props.preload_img_string,
           is_popup:this.props.is_popup,
           product_id:this.props.product_id,
           customer_data:this.props.customer_data,
           SITE_ENVIRONMENT:this.props.SITE_ENVIRONMENT,
           custom_store_val:this.props.custom_store_val,
           api_call:false,
           server_error:0
        };


    } 

    componentDidMount()
    { 
       this.getProductDeatil();
    }

    getProductDeatil()
    {
        var self = this; 
        this.setState({product_id: this.props.product_id});
        self.setState({server_error: 0});
        axios({
        method:'get',
        url:'api/product/getProductDetails/'+this.state.product_id,
        responseType:'json'
        })
       .then(response => {

                if(response.data.statusCode == 902)
                {
                  self.setState({server_error: 1}); 
                  return false;
                }
                this.setState({data: response.data});
                this.setState({language: response.data.language});
                this.setState({is_initial: 1});
                this.setState({api_call: 1});
                this.setState({preload_img_string: response.data.preload_img_string});  
                
        }).then(response => {

          if(this.state.data.special_price)
          {
            var special_price = this.state.data.special_price;
          }
          else
          {
            var special_price = 0;
          }

           var category = '';
           var category_id = 0;
           var sub_category = '';
           var sub_category_id = 0;
           var brand_name = '';
           if (this.state.data.category_info && this.state.data.category_info.category_id > 0) 
             {
               category    = this.state.data.category_info.name;
               category_id = this.state.data.category_info.category_id;
             }
           if (this.state.data.parent_category_info && this.state.data.parent_category_info.category_id > 0) 
             {
               category        = this.state.data.parent_category_info.name;
               category_id     = this.state.data.parent_category_info.category_id;
               sub_category    = this.state.data.category_info.name;
               sub_category_id = this.state.data.category_info.category_id;  
             }
 
         if (this.state.data.filters) 
             {
               var brand_name = this.state.data.filters.map(function(filter, i){
                       if(filter.group_name == 'Brand Name')
                       {
                         return filter.filter_name;
                       }
                  });
             }    
     
          dataLayer.push({'category_id': category_id.toString()});
          dataLayer.push({'category': category});
          dataLayer.push({'sub_category_id': sub_category_id.toString()});
          dataLayer.push({'sub_category': sub_category});
          dataLayer.push({'product_id': this.state.data.product_id.toString()});
          dataLayer.push({'title': this.state.data.product_name.substr(0, 100)});
          dataLayer.push({'quantity': parseInt(this.state.data.quantity, 10)});
          dataLayer.push({'model': this.state.data.model});
          dataLayer.push({'brand': brand_name});
          dataLayer.push({'price': this.state.data.price_value});
          dataLayer.push({'special': parseInt(special_price, 10)});
          dataLayer.push({'options': this.state.data.options[0]});
          dataLayer.push({'currency': getCookie('currency')});
          dataLayer.push({'images': this.state.data.image });
          dataLayer.push({'seller_id': parseInt(this.state.data.seller_id, 10) });
          dataLayer.push({'seller_nickname': this.state.data.seller_nickname });
          dataLayer.push({'meta_data': 
            {'meta_title': this.state.data.meta_title,
             'meta_keyword': this.state.data.meta_keyword,
             'meta_description': this.state.data.meta_description
            }
          });
          dataLayer.push({'event': 'we-custom-product-view'});

         var product_id = this.state.data.product_id;
         var product_quantity = this.state.data.product_quantity;
         var product_price_value = this.state.data.price_value;
         var product_piece_in_set = this.state.data.piece_in_set;
         var seller_id = this.state.data.seller_id;
         var category_ids = this.state.data.category_id;
         if(category_ids) { var category_id_split = category_ids.split(','); }
         else { var category_id_split = ''; }
         
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

        
         //localStorage.removeItem("recentView");


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
                 'category_id': value
             };
             all_store_product[i] = all_store_product_array;
             i++;
         });
         if (localStorage.getItem("recentView")) {
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
         }

         localStorage.recentView = JSON.stringify(all_store_product);

         var k='0';
         var unique_product_ky=[];
         var all_recent_product = [];
         var recentViewProduct="recentViewProduct";
         if(window.location.href.indexOf("/staging") > -1) {
          var recentViewProduct="staging_recentViewProduct";
          }
         var all_recent_product_array = {
                 'product_id': this.state.data.product_id,
                 'price'     : this.state.data.price,
                 'image'     : this.state.data.pan_detail,
                 'title'     : this.state.data.heading_title,
                 'href'     : this.state.data.href,
                 'current'  : '1'
             };
             unique_product_ky.push(this.state.data.product_id);
             all_recent_product[k] = all_recent_product_array;
             k++;

            if (localStorage.getItem(recentViewProduct)) {
                  var previousProduct = JSON.parse(localStorage.getItem(recentViewProduct));
                     $.each(previousProduct, function(index, value) {
                         var unique_key = value.product_id;
                         //=====check for dublicate Value========
                         if ($.inArray(unique_key, unique_product_ky) < '0') {
                             unique_product_ky.push(unique_key);
                              var all_recent_product_array = {
                                    'product_id': value.product_id,
                                    'price'     : value.price,
                                    'image'     : value.image,
                                    'title'     : value.title,
                                    'href'      : value.href,
                                    'current'  : '0'
                     };
                             all_recent_product[k] = all_recent_product_array;
                             k++;

                         }
                         if (k == "300") {
                             return false;
                         }

                     });
                 }
                 localStorage.setItem(recentViewProduct, JSON.stringify(all_recent_product));


         var sliderEvent='';
         var i='1';
         var item_active_class='';
         var maxItem='6';
         var allWidth=99/maxItem;
         //var allWidth=90/maxItem;  //total width is 90%;
         var start='1';
         var rcentImg='';
         var first='1';
    if (localStorage.getItem(recentViewProduct)) {
          sliderEvent +='<div class="home_category_section" id="recentSliderPos">';
          sliderEvent +='<h2 class="page_title home_title recentViewSlider"><span>My Recently Viewed Items</span></h2>';
          sliderEvent +='<div class="clearfix"></div>';
          sliderEvent +='<div class="carousel slide multi-item-carousel" id="RecentViewSlider">';
          sliderEvent +='<div class="carousel-inner related_product_slider">';
          var previousProduct = JSON.parse(localStorage.getItem(recentViewProduct));
             $.each(previousProduct, function(index, value) {
                 if(value.current=='1'){
                    first++;
                 }else{
              if(rcentImg==''){
                rcentImg=value.image;
              }
              if(i=='1'){item_active_class=' active';}
              else{item_active_class='';}
              
              if(i!='1'){
                start=i-1;
              }
                if(i=='1' || start%maxItem=='0'){
                  sliderEvent +='<div class="item '+item_active_class+'">';
                }
                  sliderEvent +='<div class="c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid home-product-list2 productSlide" style="width: '+allWidth+'%;">';
                  sliderEvent +='<div class="c-product-card__img-placeholder min_height_50">';
                  sliderEvent +='<a href="'+value.href+'" class="c-product-card__img-placeholder-inner min_height_50"><span class="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">';
                  sliderEvent +='<img class="largeImage c-img-lazy__img" src="'+value.image+'" data-src="'+value.image+'" data-main="'+value.image+'"></span></a></div>';
                  sliderEvent +='<div class="c-product-card__description recntSlideTitle">';
                  sliderEvent +='<a class="recntSlideTitle_a" href="'+value.href+'">'+value.title+'</a>';
                  sliderEvent +='</div>';
                  sliderEvent +='<div class="c-product-card__description recntSlidePrz">';
                  sliderEvent +='<a class="recntSlidePrz_a" href="'+value.href+'">'+value.price+'</a>';
                  sliderEvent +='</div>';
                  sliderEvent +='</div>';
                if(i%maxItem=='0' || previousProduct.length==i){
                  sliderEvent +='</div>';
                }
                     i++;
                 if (i == "31") {
                     return false;
                 }
             }
             });
            sliderEvent +='</div>';
            sliderEvent +='</div>';
            if(i > 6){
            sliderEvent +='<div class="home_slider_btn" style="">';
            sliderEvent +='<a class="slideLeftIcon" href="#RecentViewSlider" data-slide="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
            sliderEvent +='<a class="slideRightIcon" href="#RecentViewSlider" data-slide="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
            sliderEvent +='</div>';
            }
            sliderEvent +='</div>';
            if(previousProduct.length=='1'){
              sliderEvent='';
            }

         }
        if(sliderEvent!=''){
        $('#recentSlider').html(sliderEvent);
        $('#recentSlideBtn').html('<a href="#toRecentView" class="full-slide-recent"><div class="history-wrap"><div class="img util-left" style="background-image: url('+rcentImg+');"></div><div class="view util-left">Recently Viewed</div><i class="fa fa-angle-down" aria-hidden="true"></i></div></a>');
        }else{
        $('#recentSlideBtn').html('');
        }


        })
        .catch(function (error) {
           self.setState({server_error: 1}); 
         });
    }

    productSellerPopup(e, p)
    {   
       /* e.preventDefault();
        if(p.props.product.product_id != '' && p.props.product.product_id > 0)
        {
          this.state.product_id = p.props.product.product_id;
          axios({
          method:'get',
          url:'api/product/getProductDetails/'+this.state.product_id,
          responseType:'json'
           })
         .then(response => {
                $("img#img_zoom").attr('src', 'image/placeholder.png');
                $("img#img_zoom").attr('data-zoom-image', 'image/placeholder.png');
                $("#image-0").attr('src', 'image/placeholder.png');
                $('.zoomContainer').remove();
                this.setState({data: response.data});
                this.setState({language: response.data.language});
                this.setState({is_initial: 1});
                this.setState({preload_img_string: response.data.preload_img_string});

                var prevNowPlaying = null;
                prevNowPlaying = setInterval(function () {
                var zoom_effact = $('#image-0').attr('src');
                if(zoom_effact != 'image/placeholder.png') { $("#image-0").trigger("click");  clearInterval(prevNowPlaying); }
                 }, 500);

                 });
        }*/ 
       
    }

     
    closepreOderPopup(e){

        e.stopPropagation(); 
        $(".want_designe_popup").modal("hide"); 
        $('.popup-q-body').show();
        $('.preorder_title_box').show();
        $('.popup_comment').show(); 
        $('.preeorder_submit_bnt').show();
        //$('.close_btn').hide();

        $('.popup_comment').val('');
        $('.alert_msg').hide();
       
        if($('#product_popup').hasClass('in'))
        {
          $('#want_designe_popup').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
            $("#preOderPopup")[0].reset();
          });
        }
        
    }


    render() { 


       function i_want_design_detail(product_id)
        { 
            var option_name  = $('#option_name').val();
            var option_value = $('#option_value').val();
            var product_status = $('.product_status_'+product_id).val();
            var popup_comment = $('.popup_comment_'+product_id).val();
            var customer_mobile  = $('#design_customer_mobile').val();

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
                $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-danger">Fill out all the given field</div></div>');
                return false;
            }

            $.ajax({
                type : "POST",
                url  : 'api/product/user_comment',
                data : {customer_mobile:customer_mobile,product_id:product_id,product_status:product_status,popup_comment:popup_comment,option_name:option_name,option_value:option_value},
                beforeSend: function() {
                  $(".preeorder_submit_bnt").html("please wait..."); 
                },
                complete: function() {
                  $('.popup-footer .btn-default').button('reset');
                },
                success: function(data){
                   $('.alert_msg').remove();
                  $(".preeorder_submit_bnt").html("Send"); 
                  $('.popup_comment_'+product_id).val(''); 
                  $("#design_customer_mobile").val(''); 
                  $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-success"><i class="fa fa-check-circle"></i>'+data['message']+'</div></div>');
                }
            });
        }


        if(!this.state.is_initial)
            {
              if(this.state.server_error)
              {
                return (<ApiError retry={this.getProductDeatil} />)
              }
              else
              {
                return (<div><img width="100%" src={cdn_url+"loading_product_details.jpg"} /></div> ) 
              }
            }
        else
            {
   
              return (          
                        <div className="container-fluid width_fix product_section"> 
                                            <div className=""> 
                                                <div className="col-sm-12">
                                                    <div className="row">
                                                        <ImageCarousel 
                                                            language = {this.state.data.language} 
                                                            images = {this.state.data.images}
                                                            product_id = {this.state.product_id}
                                                            model = {this.state.data.model}
                                                            wide_image = {this.state.data.wide_image}
                                                            preload_img_string = {this.state.preload_img_string}
                                                            is_popup = {this.state.is_popup}
                                                            SITE_ENVIRONMENT={this.state.SITE_ENVIRONMENT}
                                                            pickup_city={this.state.data.pickup_city}
                                                        />
                                                        <column className="col-sm-12 col-md-8 contact_sanction">
                                                            <ProductInfo 
                                                                language = {this.state.language} 
                                                                product_id = {this.state.product_id}
                                                                product_quantity = '1'
                                                                product_price_value = {this.state.data.price_value}
                                                                product_piece_in_set = {this.state.data.piece_in_set} 
                                                                seller_id = {this.state.data.seller_id} 
                                                                category_ids = {this.state.data.category_id} 
                                                                customer_data = {this.state.customer_data}  
                                                                heading_title = {this.state.data.heading_title} 
                                                                model = {this.state.data.model}
                                                                is_sor_enabled = {this.state.data.is_sor_enabled}
                                                                sor_enabled_text = {this.state.data.sor_enabled_text}
                                                                sor_enabled_detail_text = {this.state.data.sor_enabled_detail_text}
                                                                hsn_code = {this.state.data.hsn_code}
                                                                rating = {this.state.data.rating}     
                                                                fill_heart = {this.state.data.fill_heart}     
                                                                minimum = {this.state.data.minimum} 
                                                                set_description = {this.state.data.set_description} 
                                                                options = {this.state.data.options} 
                                                                description = {this.state.data.description} 
                                                                tags = {this.state.data.tags}
                                                                discounts = {this.state.data.discounts}
                                                                filters = {this.state.data.filters}
                                                                seller_returnable = {this.state.data.seller_returnable}
                                                                exp_dispatch_days = {this.state.data.exp_dispatch_days}
                                                                previously_ordered= {this.state.data.previously_ordered}
                                                                
                                                            />
                                                            <ProductCart 
                                                                language = {this.state.language} 
                                                                product_id = {this.state.product_id}
                                                                customer_data = {this.state.customer_data}
                                                                price = {this.state.data.price} 
                                                                quantity = {this.state.data.quantity} 
                                                                options = {this.state.data.options} 
                                                                minimum = {this.state.data.minimum} 
                                                                is_single = {this.state.data.is_single} 
                                                                custom_store_selling_price = {this.state.data.custom_store_selling_price} 
                                                                custom_store_product_href = {this.state.data.custom_store_product_href} 
                                                                tax_class_id = {this.state.data.text_tax_rate} 
                                                                stock_status = {this.state.data.stock_status}  
                                                                special = {this.state.data.special}
                                                                mrp = {this.state.data.mrp}
                                                                format_mrp = {this.state.data.format_mrp}
                                                                margin_percentage = {this.state.data.margin_percentage} 
                                                                saving_money = {this.state.data.saving_money}
                                                                text_tax_rate = {this.state.data.text_tax_rate}
                                                                international_store = {this.props.international_store}
                                                                api_call = {this.state.api_call}
                                                                is_popup = {this.state.is_popup}
                                                                text_withoutslash_piece= {this.state.data.text_withoutslash_piece}
                                                                server_error={this.state.server_error}
                                                                getProductDeatil={this.getProductDeatil}
                                                                seller_returnable = {this.state.data.seller_returnable}
                                                            /> 
                                                        </column> 
                                                        <div className="clearfix"></div>
                                       <br />  

                                       <Slider custom_store_val={this.state.custom_store_val} product_id={this.state.product_id} seller_id={this.state.data.seller_id} is_popup = {this.state.is_popup}  productSellerPopup={this.productSellerPopup} key="more_seller" api_type="more_seller" slider_id="more_seller" active_item="5" />
                                       <Slider custom_store_val={this.state.custom_store_val} product_id={this.state.product_id} seller_id={this.state.data.seller_id} key="related" is_popup = {this.state.is_popup} productSellerPopup={this.productSellerPopup} api_type="related" slider_id="related" active_item="5" />               

                                        <RecentViewSlider key="latest"  category_id="latest" active_item="5" page_type="productDetail"/>
                                                        <div className="clearfix"></div>


                                           <div className="modal fade want_designe_popup question_popup" id="want_designe_popup" role="dailog" data-backdrop="static" data-keyboard="false">
                                            <div className="modal-dialog">
                                             <div className="modal-content">
                                               <div id="want_header_popup" className="modal-header want_header">
                                                <button onClick = {this.closepreOderPopup} type="button" className="close" data-product-id={this.state.product_id}>&times;</button>
                                                  <h4 className="modal-title">I want this design.</h4>
                                                </div>

                                               <div className="modal-body popup-q-body"> 
                                                 <div className="popup-title preorder_title_box">
                                                    <p>The product is out of stock but you could still ask factory if they can provide it. </p>
                                                    <p>Please specify how many pieces and what sizes/colors you like to have:</p>
                                               </div>

                                                <form id="preOderPopup">
                                                  <input type="hidden" name="product_id" value={this.state.product_id} className={'product_id_'+this.state.product_id} />
                                                  <input type="hidden" name="product_status" value="out of stock" className={'product_status_'+this.state.product_id} />
                                                  <input type="hidden" name="option_name" id="option_name" value="" />
                                                  <input type="hidden" name="option_value" id="option_value" value="" />
                                                  <input type="text" name="customer_mobile" id="design_customer_mobile" placeholder="Mobile OR Email" style={{width:'100%', margin:'10px 0px'}} />
                                                  <textarea name="popup_comment" rows="6" className={'popup_comment popup_comment_'+this.state.product_id}></textarea>
                                                </form>
                                                <div className="clearfix"></div>


                                                 <div className="popup-footer">
                                                    <button type="button" className="btn deliver_btn preeorder_submit_bnt pull-right" onClick={() => i_want_design_detail(this.state.product_id) }>Send </button>
                                                    <div className="clearfix"></div>
                                                    </div> 
                                                 </div>

                                             </div>
                                               </div>
                                                  </div>
   


                                                    </div>   
                                                </div>
                                            </div>
                                        </div>    
            
     
        )


 }
 
  }
}
