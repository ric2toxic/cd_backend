{/*
* class Checkout: This will render the checkout data on onepagecheckout page
* @Params: {
*    cart: {
*           cart_data: contains the products details,
*           cart_summary,
*           shipping: shipping address and shipping methods if any
*       },
*    shipping_addresses: addresses of customer,
*    payment_data:available payment methods,bank transfer info,
*    language: language text for different fields
*  }
*
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
*/}

class Checkout extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            customer:this.props.customer,
            cart_data:this.props.cart.cart_data,
            cart_summary:this.props.cart.cart_summary,
            shipping:this.props.cart.shipping,
            shipping_addresses:this.props.shipping_addresses,
            payment_data:this.props.payment_data,
            tab:this.props.tab,
            have_gst_tab:this.props.cart.have_gst_tab,
            checkout_page:1,
            lazypay_eligibility:false,
            lazypay_otp:false,
            surface_shipping:15000,
            rbl_credit_user_credit_limit:0
        }
        this.updateDeliveryAddress = this.updateDeliveryAddress.bind(this);
        this.updateBillingAddress  = this.updateBillingAddress.bind(this);
        this.updateShippingMethod = this.updateShippingMethod.bind(this);
        this.updatePaymentMethod = this.updatePaymentMethod.bind(this);
        this.updatePaymentMethods = this.updatePaymentMethods.bind(this);
        this.checkLazypayEligibility = this.checkLazypayEligibility.bind(this);
        this.addAddress = this.addAddress.bind(this);
        this.placeOrder = this.placeOrder.bind(this);
        this.changeTab = this.changeTab.bind(this);
        this.clearGST = this.clearGST.bind(this);

        this.getCifStatus = this.getCifStatus.bind(this);

        this.tabCollapseMap = {
            "cart":"collapseCart",
            "delivery":"collapseDelivery",
            "billing":"collapseBilling",
            "shipping":"collapseShipping",
            "gst":"collapseGST",
            "payment":"collapsePayment"
        }
    }

    removeItem(item){
        var name = item.name;
        var commentboxid = "comment_"+item.key.replace(/=/g, "");
        $('#'+commentboxid+'').remove();
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/remove',
            type: 'post',
            data: 'key=' + item.key +'&access_token='+access_token+'&customer_id='+customer_id+"&page=checkout",
            dataType: 'json',
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        })
            .done(function(json){
                var total = 0;
                if(json['empty'] == '1'){
                    localStorage.removeItem(this.state.customer.customer_id+'_cart_data');
                    window.location = '\cart';
                }
                else{
                    total = json['total_sets'];
                    var new_cart_summary = this.state.cart_summary;
                    new_cart_summary.text_rbl_consent = json['text_rbl_consent'];
                    new_cart_summary.totals = json['totals'];
                    new_cart_summary.tax_refund = json['tax_refund'];
                    new_cart_summary.disable_place_order = json['disable_place_order'];
                    new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                    new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                    new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                    var surface_shipping = json['surface_shipping'];
                    var cart_data = {"products":json['products'] ,"total_sets":json['total_sets'],"total_pieces":json['total_pieces'],
                        "clear_cart":json['clear_cart'],"cartlimitcross":json['cartlimitcross'],"weight":json['weight']};
                    this.setState({"cart_data":cart_data,cart_summary:new_cart_summary,surface_shipping:surface_shipping});
                }
            }.bind(this))
            .then(function(){
                $("#notification").fadeIn("slow").html(this.props.language.text_success_alert);
                $("#notification").fadeOut(3000);
            }.bind(this))
            .then(function(){
                if(this.state.shipping.hasOwnProperty('shipping_address')) {
                    this.updateDeliveryAddress(this.state.shipping.shipping_address);
                }
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });

    }
    updateItem(item){
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/update',
            type: 'post',
            data: 'key=' + item.key +'&quantity='+item.quantity +'&access_token='+access_token+'&customer_id='+customer_id+"&page=checkout",
            dataType: 'json',
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
            .then(function(json){
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.text_rbl_consent = json['text_rbl_consent'];
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.disable_place_order = json['disable_place_order'];
                new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                 var surface_shipping = json['surface_shipping'];
                var cart_data = {"products":json['products'] ,"total_sets":json['total_sets'],"total_pieces":json['total_pieces'],
                    "clear_cart":json['clear_cart'],"cartlimitcross":json['cartlimitcross'],"weight":json['weight']};
                this.setState({"cart_data":cart_data,cart_summary:new_cart_summary,surface_shipping:surface_shipping});
            }.bind(this))
            .then(function(){
                $("#notification").fadeIn("slow").html(this.props.language.text_success_alert);
                $("#notification").fadeOut(3000);
            }.bind(this))
            .then(function(){
                if(this.state.shipping.hasOwnProperty('shipping_address')) {
                    this.updateDeliveryAddress(this.state.shipping.shipping_address);
                }
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });
    }

    moveToWishlist(item){
        var product_id = item.combo_product_id;

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/addToWishlist',
            type: 'post',
            data: 'product_id=' + product_id +'&access_token='+access_token+'&customer_id='+customer_id+"&page=checkout",
            dataType: 'json',
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        })
        .done(function(json){
            var total = 0;
            if(json['empty'] == '1'){
                this.setState({is_empty:1});
                localStorage.removeItem(this.state.customer.customer_id+'_cart_data');
                total = json['total_wishlist_items'];
            }
            else{
                total = json['total_wishlist_items'];
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.text_rbl_consent = json['text_rbl_consent'];
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.disable_place_order = json['disable_place_order'];
                new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                var surface_shipping = json['surface_shipping'];
                var cart_data = {"products":json['products'] ,"total_sets":json['total_sets'],"total_pieces":json['total_pieces'],
                    "clear_cart":json['clear_cart'],"cartlimitcross":json['cartlimitcross'],"weight":json['weight']};
                this.setState({"cart_data":cart_data,cart_summary:new_cart_summary,surface_shipping:surface_shipping});
            }
        }.bind(this))
        .then(function(){
            $("#notification").fadeIn("slow").html(this.props.language.text_add_wishlist);
            $("#notification").fadeOut(3000);
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }


    clearPickupCityCart(city,add_to_wishlist = 0){
        var keys = this.state.cart_data.clear_cart[city];
        keys = keys.join();
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/clearPickupCityCart',
            type: 'post',
            data: 'keys=' + keys +'&access_token='+access_token+'&customer_id='+customer_id+'&add_to_wishlist='+add_to_wishlist+"&page=checkout",
            dataType: 'json',
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
            .then(function(json){
                var total = 0;
                const new_products = json['products'];
                if(json['empty'] == '1'){
                    localStorage.removeItem(this.state.customer.customer_id+'_cart_data');
                    window.location = '\cart';
                }
                else {
                    total = json['total_sets'];
                    var new_cart_summary = this.state.cart_summary;
                    new_cart_summary.text_rbl_consent = json['text_rbl_consent'];
                    new_cart_summary.totals = json['totals'];
                    new_cart_summary.tax_refund = json['tax_refund'];
                    new_cart_summary.disable_place_order = json['disable_place_order'];
                    new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                    new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                    new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                    var surface_shipping = json['surface_shipping'];
                    var cart_data = {"products":json['products'] ,"total_sets":json['total_sets'],"total_pieces":json['total_pieces'],
                        "clear_cart":json['clear_cart'],"cartlimitcross":json['cartlimitcross'],"weight":json['weight']};
                    this.setState({"cart_data":cart_data,cart_summary:new_cart_summary,surface_shipping:surface_shipping});
                }
            }.bind(this))
            .then(function(){
                $("#notification").fadeIn("slow").html(this.props.language.text_success_alert);
                $("#notification").fadeOut(3000);
            }.bind(this))
            .then(function(){
                if(this.state.shipping.hasOwnProperty('shipping_address')) {
                    this.updateDeliveryAddress(this.state.shipping.shipping_address);
                }
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });
    }

    updatePaymentMethods(address_id,shipping_method){
        $.ajax({
            url: "api/checkout/paymentMethod",
            type: "post",
            data: "address_id="+address_id+"&shipping_method="+shipping_method,
            dataType: "json",
        }).promise()
        .then(function(json){
            var payment_data = json['payment_data'];
            this.setState({payment_data:payment_data});
            this.updatePaymentMethod($('.payment_view .active .button-register').attr('data-payment-method'));
        }.bind(this));
    }

    getCifStatus(){
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "api/checkout/getCifStatus",
            type: "post",
            data: "customer_id="+customer_id,
            dataType: "json"
        }).promise()
        .then(function(json){
            
            this.setState({rbl_credit_user_credit_limit:json.rbl_balance});

        }.bind(this));
    }

    updateShippingMethod(hideCollapse = true){
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        var shipping_method = $('input[name="shipping_charge"]:checked').val();
        if(typeof shipping_method == 'undefined') return;

         dataLayer.push({'shipping_method': shipping_method});
         dataLayer.push({'event': 'we-custom-checkout-shipping-method'}); 

        $.ajax({
            url: "api/checkout/cartSummary",
            type: "post",
            dataType: "json",
            data: "shipping_method=" + shipping_method +'&access_token='+access_token+'&customer_id='+customer_id,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            var new_cart_summary = this.state.cart_summary;
            new_cart_summary.text_rbl_consent = json['text_rbl_consent'];
            new_cart_summary.totals = json['totals'];
            new_cart_summary.tax_refund = json['tax_refund'];
            new_cart_summary.price_in_rupees = json['price_in_rupees'];
            this.setState({cart_summary:new_cart_summary});
        }.bind(this))
        .then(function(json){
            var new_shipping = this.state.shipping;
            new_shipping.shipping_method = shipping_method;
            this.setState({shipping:new_shipping});
            $('input[value="'+this.state.shipping.shipping_method+'"]').attr('checked',true);
            $('#select_payment_button').css('display','block');
        }.bind(this))
        .then(function(){
            this.updatePaymentMethods(this.state.shipping.shipping_address.address_id,this.state.shipping.shipping_method);
        }.bind(this))
        .then(function () {
            var current = '#'+$('.panel-collapse.collapse.in').attr('id');
            if(current == '#collapseShipping' && hideCollapse == true){
                if(parseInt(this.state.have_gst_tab) == 1){
                    this.changeTab('gst');
                }
                else{
                    this.changeTab('payment');
                }
            }
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    updatePaymentMethod(payment_method){
        if(payment_method == 'lazypay' && this.state.lazypay_otp) 
        {
          $('.button-register').html('CONFIRM ORDER');
          $('.button-register').removeClass('disabled');
          $('.button-register').removeAttr("disabled");  
          jQuery("#payment_lazypay_div").hide(); 
          jQuery("#confirm_lazypay_div").show();
        }
        else
        {
          $('.button-register').html('CONFIRM ORDER');
          $('.button-register').removeClass('disabled');
          $('.button-register').removeAttr("disabled");  
          jQuery("#payment_lazypay_div").show(); 
          jQuery("#confirm_lazypay_div").hide();  
        }  

        var shipping_method = $('input[name="shipping_charge"]:checked').val();
        if(typeof shipping_method == 'undefined') return;
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');

        dataLayer.push({'payment_method': payment_method});
        dataLayer.push({'event': 'we-custom-checkout-payment-method'}); 

        $.ajax({
            url: "api/checkout/cartSummary",
            type: "post",
            dataType: "json",
            data: "payment_method=" + payment_method + "&shipping_method=" + shipping_method +'&access_token='+access_token+'&customer_id='+customer_id,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
            .then(function(json){
                if(payment_method == 'lazypay')
                {
                  this.checkLazypayEligibility(json['totals']['total']['value']);  
                }
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.text_rbl_consent = json['text_rbl_consent'];
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.price_in_rupees = json['price_in_rupees'];
                this.setState({cart_summary:new_cart_summary});

            }.bind(this))
            .then(function(json){
                var new_shipping = this.state.shipping;
                new_shipping.shipping_method = shipping_method;
                this.setState({shipping:new_shipping});
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });
    }

    checkLazypayEligibility(total)
    {
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "index.php?route=checkout/one_page_checkout/lazypay_eligibility_check",
            type: "post",
            dataType: "json",
            data: 'access_token='+access_token+'&customer_id='+customer_id+'&total='+total,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
            .then(function(json){
                this.setState({lazypay_eligibility:json});
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });
    }

    updateDeliveryAddress(address,hideCollapse = true){
        if(address.address_id == 0)
            return;
        if(this.state.shipping_addresses.length == 0)
            return;

        //if($('#'+address.address_id).hasClass('selected_delivery_address')) return;
        $('.address_penal').removeClass('selected_delivery_address');


        $('#select_payment_button').css('display','none');

        var address_id = address.address_id;
        var postcode = address.postcode;
        var zone_id = address.zone_id;
        var country_id = address.country_id;
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');

        if(country_id == "" || parseInt(country_id) == 0){
            country_id = "99";
        }

        var data = "country_id=" + country_id + "&zone_id=" + zone_id + "&postcode=" + postcode+ '&access_token='+access_token+'&customer_id='+customer_id+'&cartlimitcross='+this.state.cart_data.cartlimitcross;
        if(this.props.international_store)
            data = "country_id=" + country_id + "&zone_id=0&postcode="+ postcode+ '&access_token='+access_token+'&customer_id='+customer_id+'&cartlimitcross='+this.state.cart_data.cartlimitcross;

         dataLayer.push({'address_id': parseInt(address_id, 10)});
         dataLayer.push({'event': 'we-custom-checkout-delivery-address'}); 

        $.ajax({
            url: "api/checkout/shippingMethod",
            type: "post",
            dataType: "json",
            data: data,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            var shipping_methods = {};
            if(json['error']){
                this.props.language.error_no_shipping = json['error']['warning'];
            }
            else{

                if ( typeof json['shipping_method']['free'] !== 'undefined' ) {
                    shipping_methods = Object.assign(json['shipping_method']['free']['quote'], json['shipping_method']['weight']['quote']);
                } else {
                    shipping_methods = json['shipping_method']['weight']['quote'];
                }
            }
            var new_shipping = this.state.shipping;
            new_shipping.shipping_address = address;
            new_shipping.shipping_methods = shipping_methods;
            var shipping_method_keys = Object.keys(shipping_methods);
            if(shipping_method_keys.length == 1){
                new_shipping.shipping_method = shipping_methods[shipping_method_keys[0]]['code'];
            }
            this.setState({shipping:new_shipping});

        }.bind(this))
        .then(function(){
            $('#'+address.address_id).addClass('selected_delivery_address');
            $('#select_shipping_button').css('display','block');
            if(this.state.shipping.hasOwnProperty('shipping_method')){
                $('input[value="'+this.state.shipping.shipping_method+'"]').prop('checked',true);
                $('#select_payment_button').css('display','block');
            }
            this.updateShippingMethod(false);
        }.bind(this))
        .then(function () {
            var current = '#'+$('.panel-collapse.collapse.in').attr('id');
            if(current != '#collapseCart' && hideCollapse == true)
                this.changeTab('billing');
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    updateBillingAddress(address,hideCollapse = true)
    {
        var self = this; 
        if(address.address_id == 0)
            return;
        if(this.state.shipping_addresses.length == 0)
            return;

        //if($('#'+address.address_id).hasClass('selected_delivery_address')) return;
        $('.address_penal_billing').removeClass('selected_delivery_address');
        $('#select_payment_button').css('display','none');

        var address_id   = address.address_id;
        var postcode     = address.postcode;
        var zone_id      = address.zone_id;
        var country_id   = address.country_id;
        var access_token = getCookie('wat');
        var customer_id  = getCookie('customer_id');

        if(country_id == "" || parseInt(country_id) == 0){
            country_id = "99";
        }

        dataLayer.push({'address_id': parseInt(address_id, 10)});
        dataLayer.push({'event': 'we-custom-checkout-billing-address'}); 

        var data = "country_id=" + country_id + "&zone_id=" + zone_id + "&postcode=" + postcode+ '&access_token='+access_token+'&customer_id='+customer_id+'&cartlimitcross='+this.state.cart_data.cartlimitcross;
        if(this.props.international_store)
            data = "country_id=" + country_id + "&zone_id=0&postcode="+ postcode+ '&access_token='+access_token+'&customer_id='+customer_id+'&cartlimitcross='+this.state.cart_data.cartlimitcross;
        
        var new_shipping = this.state.shipping;
            new_shipping.billing_address  = address;
            this.setState({shipping:new_shipping}); 
            $('#billing_'+address.address_id).addClass('selected_delivery_address');
            $('#select_billing_button').css('display','block');
            if(this.state.shipping.hasOwnProperty('shipping_method')){
                $('input[value="'+this.state.shipping.shipping_method+'"]').prop('checked',true);
                $('#select_payment_button').css('display','block');
            }
            var current = '#'+$('.panel-collapse.collapse.in').attr('id');
            if(current != '#collapseCart' && hideCollapse == true)

            setTimeout(function(){ self.changeTab('shipping');  }, 200);

    }


    addAddress(){

        var error=false;
        $('.error_address').hide();
        var address_id = $('#address_id').val();
        var name = $('form input[id="name"]').val().trim();

        if(name.length > 32 || name.length < 1){
            $('#error_name').show();
            error = true;
        }
        var company = $('form input[id="company"]').val().trim();
        if(company.length > 64){
            $('#error_company').show();
            error = true;
        }

        var address_1 = $('form input[id="address_1"]').val().trim();
        if(address_1.length > 128 || address_1.length < 3){
            $('#error_address_1').show();
            error = true;
        }
        var address_2 = $('form input[id="address_2"]').val().trim();
        if(address_2.length > 128){
            $('#error_address_2').show();
            error = true;
        }

        var country_id = $('#country_id').val();
        if( typeof country_id == 'undefined' || country_id == ""){
            if(!this.props.international_store){
                country_id = "99";
            }
            else{
                $('#error_country').show();
                error = true;
            }
        }

        var pincode = $('form input[id="pincode"]').val();
        if(!( typeof country_id == 'undefined' || country_id == "" ) && country_id == "99" ){
            if(pincode.length != 6  || isNaN(parseFloat(pincode)) ){
                $('#error_pincode').html('*Please enter a valid 6 digit pincode').show();
                 error = true;
            }
        }
        else
        {
            if(pincode.length > 10 || pincode.length < 2){
                $('#error_pincode').html('*Pincode must be between 2 and 10 characters').show();
                error = true;
            }
        }

        var city = $('form input[id="city"]').val().trim();
        if(city.length > 32 || city.length < 2){
            $('#error_city').show();
            error = true;
        }
        var zone_id = $('#input-shipping-zone option:selected').val();
        if( typeof zone_id == 'undefined' || zone_id == "" || parseInt(zone_id) < 1){
            $('#error_zone').show();
            error = true;
        }
         
        var address_telephone = $('#address_telephone').val();
        var address_telephone_arr = address_telephone.split(",");
        var self = this;
        address_telephone_arr.forEach(function(element) {
           if(!self.props.international_store && element.length != 0 )
          {
            /*if(isNaN(parseFloat(address_telephone)) ) {
                $('#error_address_telephone').html("*Contact no should contain only digits!").show();
                error = true;
            }*/
            if(isNaN(element)){
                $('#error_address_telephone').html("*Contact no should be numeric!").show();
                error = true;
            }
            else if(element.length > 15){
                $('#error_address_telephone').html("*Contact no should be less than 15 digits!").show();
                error = true;
            }
          }
        });

        if(error)
        {
            return;
        }



        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');

        var url="";
        if(parseInt(address_id) == 0){
            url = "api/address/addAddress" ;
        }
        else{
            url = "api/address/editAddress/"+address_id;
        }

        $.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: "name=" + name + "&company=" + company + "&address_1=" + address_1+ "&address_2=" + address_2 + "&postcode=" + pincode+ "&city=" + city+ "&zone_id=" + zone_id + "&country_id=" + country_id+ '&access_token='+access_token+'&customer_id='+customer_id+'&address_telephone='+address_telephone,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            $('#add_new_address').modal('hide');
            var newAddresses = json['addresses'];
            var address_id  = json['customer_address_id'];
            var customer = this.state.customer;
            customer.address_id = address_id;

            this.setState({shipping_addresses:newAddresses});
            this.setState({customer});

            var current = '#'+$('.panel-collapse.collapse.in').attr('id');
            if(current == '#collapseBilling')
            {
              $('.address_penal_billing').removeClass('selected_delivery_address');
              $('#select_billing_button').css('display','none');
            }
            else
            {
              $('.address_penal').removeClass('selected_delivery_address');
              $('#select_shipping_button').css('display','none');
              $('#select_payment_button').css('display','none');
            }  

        }.bind(this))
        .then(function(){
            $("#notification").fadeIn("slow").html(this.props.language.text_address_update);
            $("#notification").fadeOut(3000);
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });

    }

    askForTelephone(){
        var selectList = document.getElementById("select_country_code");
        var country_code_list = getccPhone();
        for( var i=0;i<country_code_list.length;i++){
            var option = document.createElement("option");
            option.value = country_code_list[i].mobile_code;
            var class_name = "flagstrap-icon flagstrap-"+country_code_list[i].country_code.toLowerCase();//<i class="'+class_name+'" style="margin-right: 10px;"></i>
            option.text = country_code_list[i].country_code + ' ' + country_code_list[i].mobile_code;
            selectList.appendChild(option);
        }
        $('#error_telephone').html('');
        $('#add_telephone').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    // place order
    placeOrder() {
        var gst_option = 0;
        if(this.state.customer.hasOwnProperty('gst_option')){
            gst_option = this.state.customer.gst_option;
        }

        if(parseInt(this.state.cart_summary.disable_place_order) == 1){
            window.location = '\cart';
        }
        var upi_vpa = $('#upi_vpa').val();
        var payment_method = $('.payment_view .active .button-register').attr('data-payment-method');

        if(payment_method == 'paytabs' && this.state.customer.telephone == ""){
            this.askForTelephone();
            return;
        }
 
        var shipping_address_id = this.state.shipping.shipping_address.address_id;
        var payment_address_id = this.state.shipping.billing_address.address_id;
        if(parseInt(this.state.customer.is_dropshipper) == 1){
            payment_address_id = this.state.customer.address_id;
        }

        if(payment_method == 'lazypay')
        {
          var data = "payment_address=existing&shipping_address=existing&shipping_address_id="+shipping_address_id+"&payment_address_id="+payment_address_id+"&shipping_method="+this.state.shipping.shipping_method+"&payment_method="+payment_method+"&agree=1&gst_number="+this.state.customer.gst_number+"&gst_unregister_declared="+gst_option+"&upi_vpa="+upi_vpa;
        }
        else
        {
          var data = "payment_address=existing&shipping_address=existing&shipping_address_id="+shipping_address_id+"&payment_address_id="+payment_address_id+"&shipping_method="+this.state.shipping.shipping_method+"&payment_method="+payment_method+"&agree=1&gst_number="+this.state.customer.gst_number+"&gst_unregister_declared="+gst_option+"&upi_vpa="+upi_vpa+"&one_page_checkout_payment_method=one_page_checkout_payment_method";  
        }  
        
        $.ajax({
            url: 'index.php?route=checkout/one_page_checkout/validate',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $('.button-register').button('loading');
                $('#loading-indicator').show();
            },
            success: function(json) {
                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    $('#loading-indicator').hide();
                    $('#alert_body').html(json['error']['warning']);
                    $('#alert_popup').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('.button-register').button('reset');
                } else
                {
                    $.ajax({
                        url: 'index.php?route=checkout/one_page_checkout/confirm',
                        type: 'post',
                        data: data,
                        success: function(html)
                        {
                           if(html == 0)
                           {
                             $('.button-register').button('reset');
                             $('#loading-indicator').hide();
                             $('#alert_body').html("Oops,Something went wrong.Please try again.");
                             $('#alert_popup').modal({
                                backdrop: 'static',
                                keyboard: false
                             });
                           }
                           else
                           { 
                            $('#loading-indicator').hide();
                        
                            if(payment_method == 'lazypay')
                            {
                              this.setState({lazypay_otp:true});   
                              jQuery("#payment_lazypay_div").hide(); 
                              jQuery("#confirm_lazypay_div").html(html);
                              jQuery("#confirm_lazypay_div").show(); 
                            }
                            else
                            {
                              jQuery("#confirm_payment_div").html(html);  
                              jQuery("#button-confirm").click();   
                            }
                           }
                        }.bind(this),
                        error: function(xhr, ajaxOptions, thrownError) {
                            $('.button-register').button('reset');
                            $('#loading-indicator').hide();
                            $('#alert_body').html("Oops,Something went wrong.Please try again.");
                            $('#alert_popup').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });

                }
            }.bind(this),
            error: function(xhr, ajaxOptions, thrownError) {
                $('.button-register').button('reset');
                $('#loading-indicator').hide();
                $('#alert_body').html("Oops,Something went wrong.Please try again.");
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

            }
        });
    }


    clearGST(){
        var customer = this.state.customer;
        customer.gst_number = "";
        this.setState({customer:customer});
    }

    changeTab(tab){
        this.setState({tab:tab})
        if(window.location.hash && window.location.hash.substr(1) == tab){
            //do nothing
        }
        else{
            this.addTabInHistoryState(tab);
        }

        var target = '#'+this.tabCollapseMap[tab];
        var current = '#'+$('.panel-collapse.collapse.in').attr('id');
        if(target == current) return;
        $(target).collapse('show');
        $(current).collapse('hide');
        $(current+"_icon").removeClass('fa-circle-o').addClass('fa-check-circle').addClass('green_text');
        $('#accordion .cart_main_title').removeClass('main_title_active');
        $(target).prev().addClass('main_title_active');

        $(current+"_preview").show();
        $(target+"_preview").hide();

        if(target == '#collapsePayment' && parseInt(this.state.have_gst_tab) == 1){
            var customer = this.state.customer;
            customer.gst_number = $('#gst_number').val().toUpperCase();
            var gst_option = $('input[name="accept_non_gst_declaration"]:checked').val();
            if(typeof gst_option == 'undefined'){
                gst_option = 0;
            }

            customer.gst_option = gst_option;
            this.setState({customer:customer});
            $("#collapseGST_icon").removeClass('fa-circle-o').addClass('fa-check-circle').addClass('green_text');
            $("#collapseGST_preview").show();
        }


        if(target == '#collapseShipping' || target == '#collapsePayment' || target == '#collapseGST' ){
            $("#collapseBilling_icon").removeClass('fa-circle-o').addClass('fa-check-circle').addClass('green_text');
            $("#collapseBilling_preview").show();
        }


        if(target == '#collapsePayment' || target == '#collapseGST'){
            $("#collapseShipping_icon").removeClass('fa-circle-o').addClass('fa-check-circle').addClass('green_text');
            $("#collapseShipping_preview").show();
        }
    }

    addTabInHistoryState(new_tab){
        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
            var tab_string = url_page + "#" + new_tab;
        } else {
            var tab_string = window.location + "#" + new_tab;
        }
        window.location.replace(tab_string);
    }

    componentDidUpdate() {
        localStorage.setItem(this.state.customer.customer_id+'_cart_data', JSON.stringify(this.state));
    }
    componentDidMount(){

        if(typeof this.tabCollapseMap[this.state.tab]  == 'undefined'){
            this.setState({tab:'delivery'});
        }
        this.changeTab(this.state.tab);

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "api/cart/getCart",
            type: "post",
            dataType: "json",
            data: "access_token="+access_token+"&customer_id="+customer_id+"&page=checkout"
        }).promise()
        .then(function(json){
            if(json['is_empty'] == '1' || json['cart_summary']['disable_place_order'] == '1'){
                localStorage.removeItem(this.state.customer.customer_id+'_cart_data');
                window.location = '\cart';
            }
            var new_cart_data = json['cart_data'];
            var new_cart_summary = json['cart_summary'];
            var surface_shipping = json['surface_shipping'];
            if(this.state.customer == ""){
                var new_customer = json["customer"];
                if(this.props.international_store){
                    new_customer.gst_option = 1;
                    new_customer.gst_number = "";
                    this.setState({have_gst_tab:0});
                }
            }
            else{
                var new_customer = this.state.customer;
            }
            this.setState({cart_data:new_cart_data,customer:new_customer, cart_summary: new_cart_summary, surface_shipping:surface_shipping});
            $('.address_penal').removeClass('selected_delivery_address');
            $('.address_penal_billing').removeClass('selected_delivery_address');
            $('#select_shipping_button').css('display','none');
            $('#select_payment_button').css('display','none');
            $('#select_billing_button').css('display','none');
            var cart = {"customer":this.state.customer, "cart_data":new_cart_data,"cart_summary":new_cart_summary ,"shipping":this.state.shipping,"tab":this.state.tab,"have_gst_tab":this.state.have_gst_tab};
            localStorage.setItem(this.state.customer.customer_id+'_cart_data', JSON.stringify(cart));
        }.bind(this))
        .then(function(){
            if(this.state.shipping.hasOwnProperty('shipping_address')) {
                this.updateDeliveryAddress(this.state.shipping.shipping_address,false);
            }
            if(this.state.shipping.hasOwnProperty('billing_address')) {
                this.updateBillingAddress(this.state.shipping.billing_address,false);
            }
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });

        $('.skeleton').remove();

        $('[data-toggle="tooltip"]').tooltip();

        $('.disabled').click(function (e) {
            e.preventDefault();
        });

        $(".panel-title a").click(function () {
            var target = $(this).attr('href');
            if(typeof target == "undefined"){
                return;
            }
            var current = '#'+$('.panel-collapse.collapse.in').attr('id');
            if(target == current)
                return;
            if(parseInt($(target).attr('data-priority')) > parseInt($(current).attr('data-priority'))){
                return;
            }
            $(target).collapse('show');
            $(current).collapse('hide');

            $(current+"_preview").show();
            $(target+"_preview").hide();

            if(parseInt($(target).attr('data-priority')) == parseInt($(current).attr('data-priority'))){
                $(current+"_icon").removeClass('fa-circle-o').addClass('fa-check-circle').addClass('green_text');
            }
            $(target+"_icon").removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
           var tab = 'gst';
            if( target == '#collapseCart' ){
                $('#collapseDelivery_icon').removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $('#collapseBilling_icon').removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $('#collapseShipping_icon').removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $("#collapseGST_icon").removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $('#collapseShipping_preview').hide();
                $('#collapseDelivery_preview').hide();
                $('#collapseBilling_preview').hide();
                $("#collapseGST_preview").hide();

                tab = 'cart'
            }
            if( target == '#collapseDelivery' ){
                $('#collapseShipping_icon').removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $('#collapseBilling_icon').removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $("#collapseGST_icon").removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
               
                $('#collapseBilling_preview').hide();
                $('#collapseShipping_preview').hide();
                $("#collapseGST_preview").hide();

                tab = 'delivery'
            }

            if( target == '#collapseBilling' ){
                $('#collapseShipping_icon').removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $("#collapseGST_icon").removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                
                $('#collapseShipping_preview').hide();
                $("#collapseGST_preview").hide();

                tab = 'billing'
            }

            if( target == '#collapseShipping' ){
                $("#collapseGST_icon").removeClass('fa-check-circle').removeClass('green_text').addClass('fa-circle-o');
                $("#collapseGST_preview").hide();
                tab = 'shipping'
            }
            $('#accordion .cart_main_title').removeClass('main_title_active');
            $(target).prev().addClass('main_title_active');

            if (window.location.hash) {
                var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
                var tab_string = url_page + "#" + tab;
            } else {
                var tab_string = window.location + "#" + tab;
            }
            window.location.replace(tab_string);
        });

        $('.panel-collapse').on('hide.bs.collapse', function (e) {
            var $panel = $(this).closest('.panel');
            $('html,body').animate({
                scrollTop: $('#cart_full_box').offset().top - 120
            }, 800);
        });

        $('#submit_telephone').click(function () {
            var mobile_code = $('#select_country_code').val();
            var telephone = $('#input_telephone').val();
            if(telephone.length < 6){
                $('#error_telephone').html('Mobile number should contain at least 6 digits.');
                return;
            }
            if(isNaN(telephone)){
                $('#error_telephone').html('Mobile number should contain only numeric digits.');
                return;
            }

            $.ajax({
                url: "api/checkout/updateCustomerTelephone/",
                type: "post",
                dataType: "json",
                data: "mobile_code=" + mobile_code + "&telephone=" + telephone,
                beforeSend:function(){
                    $('#loading-indicator').show();
                },
                complete:function () {
                    $('#loading-indicator').hide();
                }
            }).promise()
                .then(function(json){
                    if(json['error']){
                        $('#error_telephone').html(json['error']['warning']);
                    }
                    else{
                        var customer = this.state.customer;
                        customer.telephone = telephone;
                        this.setState({customer:customer});
                        $('#add_telephone').modal('hide');
                        this.placeOrder();
                    }
                }.bind(this))
                .fail(function(xhr,exception){
                    $('#alert_body').html("Oops,Something went wrong.Please try again.");
                    $('#alert_popup').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    console.log("Error: "+xhr.status+", exception: "+exception);
                });

        }.bind(this));

       $('#address_telephone').tagsinput({
           confirmKeys: [13, 44, 32]
        });






    }
    componentWillMount(){

        if(this.state.customer.gst_number == "" || this.state.have_gst_tab == 1)
            this.setState({have_gst_tab:1});

        if(this.props.international_store  && this.state.customer!=""){
            var customer = this.state.customer;
            customer.gst_option = 1;
            customer.gst_number = "";
            this.setState({have_gst_tab:0,customer:customer});
        }

        var tab = this.state.tab;
        if(tab == 'billing') {
            if (!this.state.shipping.hasOwnProperty('shipping_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0) {
                tab = 'delivery';
            }
        }
        else if(tab == 'shipping') {
            if (!this.state.shipping.hasOwnProperty('shipping_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0) {
                tab = 'delivery';
            }
            else if (!this.state.shipping.hasOwnProperty('billing_address') || parseInt(this.state.shipping.billing_address.address_id) == 0) {
                tab = 'billing';
            }
        }
        else if(tab == 'gst'){
            if (!this.state.shipping.hasOwnProperty('shipping_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0) {
                tab = 'delivery';
            }
            else if (!this.state.shipping.hasOwnProperty('billing_address') || parseInt(this.state.shipping.billing_address.address_id) == 0) {
                tab = 'billing';
            }
            else if(!this.state.shipping.hasOwnProperty('shipping_method')){
                tab = 'shipping';
            }
        }
        else if(tab == 'payment'){
            if (!this.state.shipping.hasOwnProperty('shipping_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0) {
                tab = 'delivery';
            }
            else if (!this.state.shipping.hasOwnProperty('billing_address') || parseInt(this.state.shipping.billing_address.address_id) == 0) {
                tab = 'billing';
            }
            else if(!this.state.shipping.hasOwnProperty('shipping_method')){
                tab = 'shipping';
            }
            else{
                if(!this.state.customer.hasOwnProperty('gst_option') ||
                    ( this.state.customer.gst_option == 0 && this.state.customer.gst_number == "" ) ){
                    tab = 'gst';
                }
            }

            if(tab == 'payment'){
                this.updatePaymentMethods(this.state.shipping.shipping_address.address_id,this.state.shipping.shipping_method);
            }
        }

        if(tab != this.state.tab){
            this.setState({tab:tab});
        }

        this.getCifStatus(); 

    }

    updateState(key, value) {
        this.setState({ [key]: value });
    }

    render() {

        return (
            <div>
                <div className="col-sm-9">
                    <div className="panel-group" id="accordion">

                        <section className="panel cart_box">
                            <div className="cart_main_title  ">
                                <h4 className="panel-title shopping_cart_title">
                                    <a className="disabled accordion-toggle" data-parent="#accordion" href="#collapseCart"><i id="collapseCart_icon" className="fa fa-check-circle green_text" aria-hidden="true"></i> &nbsp;{this.props.language.text_cart}
                                        <div id="collapseCart_preview">
                                            <button type="button" className="btn btn_clear_cart top_to_title pull-right" >Review</button>
                                            <div className="col-sm-12 table_title_detailes">
                                                <p>Total Quantity: {this.state.cart_data.total_sets} Sets, {this.state.cart_data.total_pieces} Pieces &nbsp;| &nbsp;Weight: {this.state.cart_data.weight}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <div className="clearfix"></div>
                                </h4>
                            </div>
                            <div id="collapseCart" data-priority="1" className="panel-collapse collapse">
                                <div className="panel-body nopadding">
                                    <div className="cart_top_bar" id="cart_top_bar">
                                        <div className="col-sm-6 top_left_box"><h3>Total Quantity: {this.state.cart_data.total_sets} Sets, {this.state.cart_data.total_pieces} Pieces &nbsp;| &nbsp;Weight: {this.state.cart_data.weight}</h3></div>
                                    </div>
                                    <div className="clearfix"></div>
                                    <div className="cart_table" style={{maxHeight:'1000px',overflowY:'auto'}}>
                                        <div className="col-sm-12 nopadding cart_table_title">
                                            <div className="col-sm-9 cart_table_title_line">PRODUCT</div>
                                            <div className="col-sm-2 cart_table_title_line text-center">AMOUNT</div>
                                            <div className="col-sm-1 cart_table_title_line text-center">GST</div>
                                        </div>
                                        <div className="clearfix"></div>
                                        { Object.keys(this.state.cart_data.products).map(
                                            (city) =>
                                                {
                                                    return (
                                                        <div  key={city} >
                                                            <PickupCity city={city} key={city} clearPickupCityCart={this.clearPickupCityCart.bind(this)} language = {this.props.language} />
                                                            <div className="clearfix"></div>
                                                            <div className="col-sm-12 collapse in" id={"shipment_"+city}>
                                                                {Object.keys(this.state.cart_data.products[city]).map((combo_product_key,key) =>
                                                                  <div key = {key} style={{ margin: '5px', border:Object.keys(this.state.cart_data.products[city][combo_product_key]).length > 1 ? "1px solid grey" : "0px"}}>
                                                                    {this.state.cart_data.products[city][combo_product_key].map((item, key) => 
                                                                      <Item key = {key} data = {item} removeItem = {this.removeItem.bind(this,item)} updateItem = {this.updateItem.bind(this,item)} moveToWishlist={this.moveToWishlist.bind(this,item)} language={this.props.language} />
                                                                    )}
                                                                  </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                    );
                                                }
                                            )
                                        }

                                    </div>
                                    <div className="cart_table" style={{display:'block'}} id="select_delivery_button">
                                        {parseInt(this.state.cart_summary.disable_place_order) == 1?(
                                            <div style={{display:'none'}}></div>
                                            ):(
                                                <button className="btn deliver_btn pull-right" data-parent="#accordion" onClick={this.changeTab.bind(this,'delivery')} data-target="#collapseDelivery">CONTINUE &nbsp;<i className="fa fa-caret-right" aria-hidden="true"></i></button>
                                            )
                                        }

                                    </div>
                                </div>
                            </div>
                        </section>

                        <section className="panel cart_box">
                            <div className="cart_main_title main_title_active">
                                <h4 className="panel-title shopping_cart_title">
                                    {parseInt(this.state.cart_summary.disable_place_order) == 1?(
                                            <a className="accordion-toggle disabled" data-parent="#accordion"  disabled><i className="fa fa-circle-o" aria-hidden="true"></i> &nbsp;Delivery Address</a>
                                        ):(
                                            <a className="accordion-toggle disabled" data-parent="#accordion"  href="#collapseDelivery"><i id="collapseDelivery_icon" className="fa fa-circle-o" aria-hidden="true"></i>  &nbsp;Delivery Address
                                                {this.state.shipping.hasOwnProperty("shipping_address")?(
                                                    <div id="collapseDelivery_preview" style={{display:'none'}}>
                                                        <button type="button" className="btn btn_clear_cart top_to_title pull-right" >Change</button>
                                                        <div className="col-sm-12 table_title_detailes">
                                                            <p>{this.state.shipping.shipping_address.firstname} {this.state.shipping.shipping_address.lastname}, {this.state.shipping.shipping_address.company}, {this.state.shipping.shipping_address.address_1}, {this.state.shipping.shipping_address.address_2}, {this.state.shipping.shipping_address.city} {this.state.shipping.shipping_address.zone}, {this.state.shipping.shipping_address.postcode}</p>
                                                        </div>
                                                    </div>
                                                ):null}
                                            </a>
                                        )
                                    }
                                    <div className="clearfix"></div>
                                </h4>
                            </div>
                            <div id="collapseDelivery" data-priority="1" className="panel-collapse collapse in">
                                <div className="panel-body">
                                    {parseInt(this.state.cart_summary.disable_place_order) == 1?(<div style={{display:'none'}}></div>):(
                                        <CartDelivery
                                            customer={this.state.customer}
                                            shipping_addresses={this.state.shipping_addresses}
                                            updateDeliveryAddress={this.updateDeliveryAddress}
                                            addAddress={this.addAddress}
                                            shipping={this.state.shipping}
                                            language={this.props.language}
                                            international_store={this.props.international_store}
                                            countries={this.props.countries}
                                            changeTab={this.changeTab}
                                            updateState = {this.updateState.bind(this)}
                                        />
                                    )}
                                </div>
                            </div>
                        </section>

                        <section className="panel cart_box">
                            <div className="cart_main_title">
                                <h4 className="panel-title shopping_cart_title">
                                   {this.state.shipping.hasOwnProperty("billing_address")?(
                                                    <a className="accordion-toggle disabled" data-parent="#accordion"  href="#collapseBilling"><i id="collapseBilling_icon" className="fa fa-circle-o" aria-hidden="true"></i>  &nbsp;Billing Address
                                                    <div id="collapseBilling_preview" style={{display:'none'}}>
                                                        <button type="button" className="btn btn_clear_cart top_to_title pull-right" >Change</button>
                                                        <div className="col-sm-12 table_title_detailes">
                                                            <p>{this.state.shipping.billing_address.firstname} {this.state.shipping.billing_address.lastname}, {this.state.shipping.billing_address.company}, {this.state.shipping.billing_address.address_1}, {this.state.shipping.billing_address.address_2}, {this.state.shipping.billing_address.city} {this.state.shipping.billing_address.zone}, {this.state.shipping.billing_address.postcode}</p>
                                                        </div>
                                                    </div>
                                                    </a>
                                                ): ( <a className="accordion-toggle disabled" data-parent="#accordion"  disabled><i id="collapseBilling_icon" className="fa fa-circle-o" aria-hidden="true"></i>  &nbsp;Billing Address </a>)}
                                            
                                    <div className="clearfix"></div>
                                </h4>
                            </div>
                             <div id="collapseBilling" data-priority="1" className="panel-collapse collapse">
                                <div className="panel-body">
                                    {parseInt(this.state.cart_summary.disable_place_order) == 1?(<div style={{display:'none'}}></div>):(
                                        <CartBilling
                                            customer={this.state.customer}
                                            shipping_addresses={this.state.shipping_addresses}
                                            updateBillingAddress={this.updateBillingAddress}
                                            addAddress={this.addAddress}
                                            shipping={this.state.shipping}
                                            language={this.props.language}
                                            international_store={this.props.international_store}
                                            countries={this.props.countries}
                                            changeTab={this.changeTab}
                                            updateState = {this.updateState.bind(this)}
                                        />
                                    )}
                                </div>
                            </div>
                        </section>
                        <section className="panel cart_box">
                            <div className="cart_main_title">
                                <h4 className="panel-title">
                                    <a className="accordion-toggle disabled" data-parent="#accordion" href="#collapseShipping"><i id="collapseShipping_icon" className="fa fa-circle-o" aria-hidden="true"></i> &nbsp;Shipping Method
                                        {this.state.shipping.hasOwnProperty("shipping_method")?(
                                            <div id="collapseShipping_preview" style={{display:'none'}}>
                                                <button type="button" className="btn btn_clear_cart top_to_title pull-right" >Change</button>
                                                <div className="col-sm-12 table_title_detailes">
                                                    {this.state.shipping.shipping_methods.hasOwnProperty(this.state.shipping.shipping_method.split(".")[1])?(
                                                        <p>{this.state.shipping.shipping_methods[this.state.shipping.shipping_method.split(".")[1]]['title']}</p>
                                                    ):(<p></p>)}

                                                </div>
                                            </div>
                                        ):(<div style={{display:'none'}}></div>)}
                                    </a>
                                    <div className="clearfix"></div>
                                </h4>
                            </div>
                            <div id="collapseShipping" data-priority="3" className="panel-collapse collapse">

                                <div className="panel-body">
                                    {parseInt(this.state.cart_summary.disable_place_order) == 1?(<div style={{display:'none'}}></div>):(
                                        <CartShipping surface_shipping={this.state.surface_shipping} customer={this.state.customer} shipping={this.state.shipping} updateShippingMethod={this.updateShippingMethod} language={this.props.language} changeTab={this.changeTab} have_gst_tab={this.state.have_gst_tab}/>
                                    )}
                                </div>

                            </div>

                        </section>

                        {parseInt(this.state.have_gst_tab) == 1?(
                            <section className="panel cart_box">
                                <div className="cart_main_title">
                                    <h4 className="panel-title">
                                        <a className="accordion-toggle disabled" data-parent="#accordion" href="#collapseGST"><i id="collapseGST_icon" className="fa fa-circle-o" aria-hidden="true"></i> &nbsp;GST
                                            {this.state.customer.hasOwnProperty("gst_number")?(
                                                <div id="collapseGST_preview" style={{display:'none'}}>
                                                    <button type="button" className="btn btn_clear_cart top_to_title pull-right" >Change</button>
                                                    <div className="col-sm-12 table_title_detailes">
                                                        {this.state.customer.gst_number != ""?(
                                                            <p>{this.props.language.text_gst_already} {this.state.customer.gst_number}</p>
                                                        ):(<p>{this.props.language.text_gst_declaration}</p>)}

                                                    </div>
                                                </div>
                                            ):(<div style={{display:'none'}}></div>)}
                                        </a>
                                        <div className="clearfix"></div>
                                    </h4>
                                </div>
                                <div id="collapseGST" data-priority="4" className="panel-collapse collapse">
                                    <div className="panel-body row">
                                        {parseInt(this.state.cart_summary.disable_place_order) == 1?(<div style={{display:'none'}}></div>):(
                                            <CartGST customer={this.state.customer} language={this.props.language} clearGST={this.clearGST} changeTab={this.changeTab} />
                                        )}
                                    </div>
                                </div>
                            </section>
                        ):(<section style={{display:'none'}}></section>)}


                        <section className="panel cart_box">
                            <div className="cart_main_title">
                                <h4 className="panel-title">
                                    <a className="accordion-toggle disabled" data-parent="#accordion" href="#collapsePayment"><i id="collapsePayment_icon" className="fa fa-circle-o" aria-hidden="true"></i> &nbsp;Payment</a>
                                </h4>
                            </div>
                            <div id="collapsePayment" data-priority="5" className="panel-collapse collapse">
                                <div className="panel-body row">
                                    {parseInt(this.state.cart_summary.disable_place_order) == 1?(<div style={{display:'none'}}></div>):(
                                        <CartPayment 
                                          payment_data={this.state.payment_data} 
                                          shipping={this.state.shipping} 
                                          cart_summary = {this.state.cart_summary} 
                                          updatePaymentMethod={this.updatePaymentMethod} 
                                          placeOrder={this.placeOrder} 
                                          international_store = {this.props.international_store} 
                                          customer={this.state.customer} 
                                          cartlimitcross={this.state.cart_data.cartlimitcross} 
                                          neo_credit_user_credit_limit={this.props.neo_credit_user_credit_limit}
                                          rbl_credit_user_credit_limit={this.state.rbl_credit_user_credit_limit}
                                          text_rbl_limit_error={this.props.text_rbl_limit_error}
                                          RBL_ORDER_LIMIT={this.props.RBL_ORDER_LIMIT}
                                          lazypay_eligibility={this.state.lazypay_eligibility} 
                                          changeTab={this.changeTab}
                                          cod_available_limit={this.props.cod_available_limit}
                                          cod_advance_amount_limit={this.props.cod_advance_amount_limit}
                                        />
                                    )}
                                </div>
                            </div>
                        </section>

                    </div>
                </div>

                <div className="col-sm-3" id="checkoutBlock" style={{padding:'0'}}>
                  <div className="col-sm-12 cart_statement cart_statement_pree_load" style={{marginTop:'0.5%',marginBottom:'1%',width:'100%'}} >
                      <CartSummary customer={this.state.customer} cart_summary = {this.state.cart_summary} language = {this.props.language} checkout_page={this.state.checkout_page} />
                  </div>
                  {parseInt(this.props.international_store) == 0 ?
                    <CartOffer language={this.props.language} customer={this.state.customer} surface_shipping={this.state.surface_shipping} surface_shipping_country={this.state.shipping.shipping_address ? this.state.shipping.shipping_address.country : 'India'} />
                  : null}
                </div>
                <div className="clearfix"></div>
                <div style={{display:'none'}} id="confirm_payment_div"></div>
            </div>
        );
    }

}
