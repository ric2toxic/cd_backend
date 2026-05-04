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
            customer:this.props.cart.customer,
            cart_data:this.props.cart.cart_data,
            cart_summary:this.props.cart.cart_summary,
            shipping:this.props.cart.shipping,
            shipping_addresses:this.props.shipping_addresses,
            payment_data:this.props.payment_data,
            checkout_page:1,
            have_gst_tab:this.props.cart.have_gst_tab,
            is_initial:1,
            tab:this.props.tab,
            app_language:this.props.app_language,
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
        this.goBack = this.goBack.bind(this);
        this.goToShippingTab = this.goToShippingTab.bind(this);
        this.clearGST = this.clearGST.bind(this);

        this.getCifStatus = this.getCifStatus.bind(this);

        this.tabTitleMap = {
            "delivery":this.props.language.text_checkout_shipping_address,
            "billing" :this.props.language.text_checkout_payment_address,
            "shipping":this.props.language.text_checkout_shipping_method,
            "gst":this.props.language.text_checkout_gst,
            "payment":this.props.language.text_checkout_payment_method
        };
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
        }.bind(this))
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

    updateShippingMethod(change_tab = 1){
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
        }.bind(this))
        .then(function(){
            this.updatePaymentMethods(this.state.shipping.shipping_address.address_id,this.state.shipping.shipping_method);
        }.bind(this))
        .then(function(){
            if(change_tab == 1){
                if( (this.state.customer.gst_number == "" && !this.props.international_store) || this.state.have_gst_tab == 1){
                    this.addTabInHistoryState('gst');
                    this.setState({have_gst_tab:1,tab:'gst'})
                }
                else{
                    this.addTabInHistoryState('payment');
                    this.setState({tab:'payment'})
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
             jQuery("#payment_lazypay_div").hide(); 
             jQuery("#confirm_lazypay_div").show();
             $("#lazypay_submit").show();
             $(".checkout_cart_button .button-register").hide();
        }
        else
        {
            jQuery("#payment_lazypay_div").show(); 
            jQuery("#confirm_lazypay_div").hide();
            $("#lazypay_submit").hide();
            $(".checkout_cart_button .button-register").show();
            $('.checkout_cart_button .button-register').html(this.props.language.text_checkout_confirm);
            $('.checkout_cart_button .button-register').removeAttr("disabled");
        }
        
        var shipping_method = '';
        if(this.state.shipping.hasOwnProperty('shipping_method')){
            shipping_method = this.state.shipping.shipping_method;
        }
        if(shipping_method == '') return;
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
                $('.checkout_cart_button .button-register').removeClass('disabled');
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
                if(json.success)
                {
                   $('.checkout_cart_button .button-register').removeClass('disabled');
                }
                else
                {
                    $('.checkout_cart_button .button-register').addClass('disabled');
                }
                this.setState({lazypay_eligibility:json});
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });
    }

    updateDeliveryAddress(address,change_tab = 1){
        if(address.address_id == 0)
            return;
        if(this.state.shipping_addresses.length == 0)
            return;

        $('.address_penal a').removeClass('selected_delivery_address');
        
        
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
            var new_shipping = {"shipping_address":address,"shipping_methods":shipping_methods};
            if(this.state.shipping.hasOwnProperty('shipping_method'))
                new_shipping.shipping_method = this.state.shipping.shipping_method;
            this.setState({shipping:new_shipping});
            $('#'+address.address_id).addClass('selected_delivery_address');
            $('#select_shipping_button').css('display','block');
            if(this.state.shipping.hasOwnProperty('shipping_method'))
                $('input[value="'+this.state.shipping.shipping_method+'"]').attr('checked',true);
            var shipping_method = $('input[name="shipping_charge"]:checked').val();
            if(typeof shipping_method != 'undefined') $('#select_payment_button').css('display','block');
        }.bind(this))
        .then(function () {
            if(change_tab == 1){
                var tab = "billing";
                this.addTabInHistoryState(tab);
                this.setState({tab:tab});
            }
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }


    updateBillingAddress(address,change_tab = 1){
        if(address.address_id == 0)
            return;
        if(this.state.shipping_addresses.length == 0)
            return;

        $('.address_penal_billing a').removeClass('selected_delivery_address');
        
        
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
        dataLayer.push({'event': 'we-custom-checkout-billing-address'}); 

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
            if(json['error']){
                this.props.language.error_no_shipping = json['error']['warning'];
            }
            var new_shipping = {"billing_address":address, shipping_methods:this.state.shipping.shipping_methods, shipping_address:this.state.shipping.shipping_address};
            this.setState({shipping:new_shipping});
            $('#billing_'+address.address_id).addClass('selected_delivery_address');
            $('#select_shipping_button').css('display','block');
            if(this.state.shipping.hasOwnProperty('shipping_method'))
                $('input[value="'+this.state.shipping.shipping_method+'"]').attr('checked',true);
            var shipping_method = $('input[name="shipping_charge"]:checked').val();
            if(typeof shipping_method != 'undefined') $('#select_payment_button').css('display','block');
        }.bind(this))
        .then(function () {
            if(change_tab == 1){
                var tab = "shipping";
                this.addTabInHistoryState(tab);
                this.setState({tab:tab});
            }
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });

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
                $('#error_pincode').html(this.props.language.error_postcode).show();
                error = true;
            }
        }
        else
        {
            if(pincode.length > 10 || pincode.length < 2){
                $('#error_pincode').html(this.props.language.error_postcode_co).show();
                error = true;
            }
        }
        
        var city = $('form input[id="city"]').val().trim();
        if(city.length > 32 || city.length < 2){
            $('#error_city').show();
            error = true;
        }
        var zone_id = $('#input-shipping-zone option:selected').val();
        if( typeof zone_id == 'undefined' || zone_id == ""  || parseInt(zone_id) < 1){
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
                $('#error_address_telephone').html(this.props.language.error_telephone).show();
                error = true;
            }
            else if(element.length > 15){
                $('#error_address_telephone').html(this.props.language.error_telephone_max_limit).show();
                error = true;
            }
          }
        });

        if(error){
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
            
            if(json.hasOwnProperty('address_id')){
                var shipping_address = "";
                for(var i = 0; i < newAddresses.length; i++){
                    if(newAddresses[i].address_id == json.address_id){
                        shipping_address = newAddresses[i];
                        break;
                    }
                }
                if(shipping_address != "")
                    this.updateDeliveryAddress(shipping_address,0);
            }

            if(this.state.tab == 'delivery')
            {
              $('.address_penal a').removeClass('selected_delivery_address');
            }
            else
            {
               $('.address_penal_billing a').removeClass('selected_delivery_address');
            } 
            $('#select_shipping_button').css('display','none');
            $('#select_payment_button').css('display','none');
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
        if(parseInt(this.state.cart_summary.disable_place_order) == 1){
            window.location = '\cart';
        }
        var upi_vpa = $('#upi_vpa').val();
        var gst_option = 0;
        if(this.state.customer.hasOwnProperty('gst_option')){
            gst_option = this.state.customer.gst_option;
        }

        var payment_method = $('.payment_view_box .collapse.in .button-register').attr('data-payment-method');
        if(typeof payment_method == 'undefined') return;

        if(payment_method == 'paytabs' && this.state.customer.telephone == ""){
            this.askForTelephone();
            return;
        }

        if (payment_method == 'credit' && this.props.neo_credit_user_credit_limit <= 0) {
          $('#alert_body').html(this.props.language.error_credit_limit);
          $('#alert_popup').modal({
              backdrop: 'static',
              keyboard: false
          });
          return;
        }

        if (payment_method == 'rbl' && this.state.rbl_credit_user_credit_limit <= 0) {
          $('#alert_body').html(this.props.language.error_rbl_credit_limit);
          $('#alert_popup').modal({
              backdrop: 'static',
              keyboard: false
          });
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
         var data = "payment_address=existing&shipping_address=existing&shipping_address_id="+shipping_address_id+"&payment_address_id="+payment_address_id+"&shipping_method="+this.state.shipping.shipping_method+"&payment_method="+payment_method+"&agree=1&one_page_checkout_payment_method=one_page_checkout_payment_method&gst_number="+this.state.customer.gst_number+"&gst_unregister_declared="+gst_option+"&upi_vpa="+upi_vpa;  
        } 

        $.ajax({
            url: 'index.php?route=checkout/one_page_checkout/validate',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.button-register').button('loading');
                document.getElementById("checkout_back_btn").onclick = function (evt) {
                    evt.stopPropagation();
                };
                $('#loading-indicator').show();
            }
        }).promise()
        .then(function(json) {
            if (json['redirect']) {
                location = json['redirect'];
            }
            else if (json['error']) {
                $('#loading-indicator').hide();
                $('#alert_body').html(json['error']['warning']);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.button-register').button('reset');
            }
            else {
                $.ajax({
                    url: 'index.php?route=checkout/one_page_checkout/confirm',
                    type: 'post',
                    data: data,
                    success: function(html)
                    {
                       if(html == 0)
                       {
                         document.getElementById("checkout_back_btn").onclick = function(evt){};
                         $('.button-register').button('reset');
                         $('#loading-indicator').hide();
                         $('#alert_body').html("Oops, Something went wrong.<br/>Please try again.");
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

                              $(".checkout_cart_button .button-register").hide();
                              $("#lazypay_submit").show();
                            }
                            else
                            {
                              jQuery("#confirm_payment_div").html(html);  
                              jQuery("#button-confirm").click();   
                            }
                        }
                    }.bind(this),
                    error: function(xhr, ajaxOptions, thrownError) {
                        document.getElementById("checkout_back_btn").onclick = function(evt){};
                        $('.button-register').button('reset');
                        $('#loading-indicator').hide();
                        $('#alert_body').html(this.props.language.error_payment);
                        $('#alert_popup').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });

            }
        }.bind(this))
        .fail(function(xhr, ajaxOptions, thrownError){
            document.getElementById("checkout_back_btn").onclick = function(evt){};
            $('.button-register').button('reset');
            $('#loading-indicator').hide();
            $('#alert_body').html(this.props.language.error_payment);
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        });
    }

    clearGST(){
        var customer = this.state.customer;
        customer.gst_number = "";
        this.setState({customer:customer});
    }

    changeTab(){
        if(this.state.tab == 'delivery'){

            if(!this.state.shipping.hasOwnProperty('shipping_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0 ){
                $('#alert_body').html(this.props.language.error_delivery_address);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return;
            }
            this.addTabInHistoryState('billing');
            this.setState({tab:'billing'})
        }
        if(this.state.tab == 'billing'){

            if(!this.state.shipping.hasOwnProperty('billing_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0 ){
                $('#alert_body').html(this.props.language.error_billing_address);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return;
            }
            this.addTabInHistoryState('shipping');
            this.setState({tab:'shipping'})
        }
        else if(this.state.tab == 'shipping'){
            if(!this.state.shipping.hasOwnProperty('shipping_method')){
                $('#alert_body').html(this.props.language.error_shipping_method);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return;
            }
            if( (this.state.customer.gst_number == "" && !this.props.international_store) || this.state.have_gst_tab == 1){
                this.addTabInHistoryState('gst');
                this.setState({have_gst_tab:1,tab:'gst'})
            }
            else{
                this.addTabInHistoryState('payment');
                this.setState({tab:'payment'})
            }
        }
        else if(this.state.tab == "gst"){
            var gst_option = $('input[name="accept_non_gst_declaration"]:checked').val();
            if(typeof gst_option == 'undefined'){
                gst_option = 0;
            }
            if(parseInt(gst_option) == 0){
                var gst_number = $('#gst_number').val();
                if(!validateGSTNumber(gst_number)){
                    $('#alert_body').html(this.props.language.error_gst);
                    $('#alert_popup').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    return;
                }
            }
            else{
                $('#gst_number').val('');
            }

            var access_token = getCookie('wat');
            var customer_id = getCookie('customer_id');
            $.ajax({
                url: 'api/checkout/validateGSTNumber',
                type: 'post',
                data: 'gst_number=' + $('#gst_number').val() +'&access_token='+access_token+'&customer_id='+customer_id,
                dataType: 'json',
                beforeSend:function(){
                    $('#loading-indicator').show();
                },
                complete:function () {
                    $('#loading-indicator').hide();
                }
            }).promise()
            .then(function(json){
                if(json['error']){
                    $('#alert_body').html(json['error']['warning']);
                    $('#alert_popup').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    return;
                }
                var customer = this.state.customer;
                customer.gst_number = $('#gst_number').val().toUpperCase();
                customer.gst_option = gst_option;
                this.setState({customer:customer});
                this.addTabInHistoryState('payment');
                this.setState({tab:'payment'})
            }.bind(this))
            .fail(function(xhr,exception){
                $('#loading-indicator').hide();
                $('#alert_body').html(this.props.language.error_payment);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                console.log("Error: "+xhr.status+", exception: "+exception);
            });

        }
        else if(this.state.tab == 'payment'){
            this.placeOrder();
        }
        $('html,body').animate({
            scrollTop: $('#cart_full_box').offset().top - 120
        }, 800);

    }

    goBack(){
        history.back();
    }
    
    goToShippingTab() {
      if (this.state.have_gst_tab) {
        history.go(-2);
      } else {
        history.go(-1);
      }
    }

    addTabInHistoryState(new_tab){
        var url_page = window.location.protocol + "//" + window.location.host ;
        if(window.location.pathname.search('staging') != -1){
            url_page = url_page + "/staging";
        }
        var tab_string = url_page +"/onepagecheckout/?language="+this.state.app_language+"#" + new_tab;
        history.pushState(new_tab, null, tab_string);
    }

    componentDidMount(){
        if(!window.location.hash){
            this.addTabInHistoryState(this.state.tab);
        }
        if(parseInt(this.props.is_app) == 1){
            $('.checkout_top_header').hide();
        }
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "api/cart/getCart",
            type: "post",
            dataType: "json",
            data: "access_token="+access_token+"&customer_id="+customer_id+"&page=checkout"
        }).promise()
        .then(function(json){
            $('.skeleton').remove();
            if(json['is_empty'] == '1' || json['cart_summary']['disable_place_order'] == '1'){
                localStorage.removeItem(this.state.customer.customer_id+'_cart_data');
                window.location = '\cart';
            }
            var new_cart_data = json['cart_data'];
            var new_cart_summary = json['cart_summary'];
            var surface_shipping = json['surface_shipping'];
            if(this.state.customer == ""){
                var customer = json["customer"];
                if(this.props.international_store){
                    customer.gst_option = 1;
                    customer.gst_number = "";
                    this.setState({have_gst_tab:0});
                }
            }
            else{
                var customer = this.state.customer;
            }
            this.setState({cart_data:new_cart_data,customer:customer,cart_summary:new_cart_summary,is_initial:0,surface_shipping:surface_shipping});
            var cart = {"customer":this.state.customer, "cart_data":new_cart_data,"cart_summary":new_cart_summary ,"shipping":this.state.shipping,"tab":this.state.tab,"have_gst_tab":this.state.have_gst_tab};
            localStorage.setItem(this.state.customer.customer_id+'_cart_data', JSON.stringify(cart));
        }.bind(this))
        .then(function(){
            $('[data-toggle="tooltip"]').tooltip();

            $('.disabled').click(function (e) {
                e.preventDefault();
            });
            if(this.state.shipping.hasOwnProperty('shipping_address')){
                this.updateDeliveryAddress(this.state.shipping.shipping_address,0);
            }
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });

    }

    componentDidUpdate(){
        if (this.state.tab == 'payment'){
            $('#checkout_continue_btn').html(this.props.language.text_checkout_confirm);
        }
        else{
            $('#checkout_continue_btn').html(this.props.language.button_continue);
        }
        var payment_method = $('.payment_view_box .collapse.in .button-register').attr('data-payment-method')
        if( parseInt(this.state.payment_data.cod_available) == 0 && payment_method == "cod"){
            ('#checkout_continue_btn').prop('disabled', true);('#checkout_continue_btn').prop('disabled', true);('#checkout_continue_btn').prop('disabled', true);
        }
        else{
            $('#checkout_continue_btn').prop('disabled', true);
        }

        localStorage.setItem(this.state.customer.customer_id+'_cart_data', JSON.stringify(this.state));

        window.onpopstate = function(event) {
            // if(event.state){
            if(window.location.hash){
                var tab = window.location.hash.substr(1);
                if(typeof this.tabTitleMap[tab] == 'undefined'){
                    var url = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.location = url;
                }
                else{
                    this.setState({tab:tab});
                    //this.forceUpdate();
                }
            }
            else{
                // just to load the content of the url
                window.location = window.location;
            }

            $('html,body').animate({
                scrollTop: $('#cart_full_box').offset().top - 120
            }, 800);
            // }
        }.bind(this);

        $('#submit_telephone').click(function () {
            var mobile_code = $('#select_country_code').val();
            var telephone = $('#input_telephone').val();
            if(telephone.length < 6){
                $('#error_telephone').html(this.props.language.error_telephone_min_limit);
                return;
            }
            if(isNaN(telephone)){
                $('#error_telephone').html(this.props.language.error_telephone);
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
                    $('#alert_body').html(this.props.language.error_payment);
                    $('#alert_popup').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    console.log("Error: "+xhr.status+", exception: "+exception);
                });

        }.bind(this));
    }

    componentWillMount(){
        if(this.props.international_store && this.state.customer!=""){
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
            else if (!this.state.shipping.hasOwnProperty('shipping_address') || parseInt(this.state.shipping.shipping_address.address_id) == 0) {
                tab = 'delivery';
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
                if(document.referrer.search('cart') == -1 && document.referrer.search('checkout') == -1){
                    if(parseInt(this.props.is_app) == 0){
                        var url_page = window.location.protocol + "//" + window.location.host;
                        if(window.location.pathname.search('staging') != -1){
                            url_page = url_page + "/staging";
                        }
                        history.pushState('wholesalebox', null, url_page);
                        url_page = url_page + "/cart";
                        history.pushState('cart', null, url_page);
                    }
                    this.addTabInHistoryState('delivery');
                    this.addTabInHistoryState('billing');
                    this.addTabInHistoryState('shipping');
                    if(parseInt(this.state.have_gst_tab) == 1){
                        this.addTabInHistoryState('gst');
                    }
                    this.addTabInHistoryState('payment');
                }
            }

        }

        if(tab != this.state.tab){
            this.addTabInHistoryState(tab);
            this.setState({tab:tab});
        }

        this.getCifStatus(); 

    }


    createMarkup(html) {
        return {__html: html};
    }

    updateState(key, value) {
        this.setState({ [key]: value });
    }


    render() {
        return (
            <div>
            {this.state.is_initial == 1?(<div></div>):(
                <div>
                <div className="col-xs-12 mobile_section">
                    <div className="panel-group" id="accordion">
                        <section className="panel cart_box">
                            <div className="cart_main_title main_title_active">
                                <h4 className="panel-title shopping_cart_title">
                                    <a className="accordion-toggle">{this.tabTitleMap[this.state.tab]}</a>
                                </h4>
                            </div>
                            <div className="clearfix"></div>
                            <div id={this.state.tab+"_"} className="collapse in" style={{marginTop:'10px'}}>
                                <div className="panel-body nopadding">
                                    {parseInt(this.state.cart_summary.disable_place_order) == 1?(<div style={{display:'none'}}></div>):(
                                        <div>
                                            {this.state.tab == 'delivery'?(
                                                <CartDelivery
                                                    customer={this.state.customer}
                                                    shipping_addresses={this.state.shipping_addresses}
                                                    updateDeliveryAddress={this.updateDeliveryAddress}
                                                    addAddress={this.addAddress}
                                                    shipping={this.state.shipping}
                                                    language={this.props.language}
                                                    international_store={this.props.international_store}
                                                    countries={this.props.countries}
                                                    updateState = {this.updateState.bind(this)}
                                                />
                                            ):(<div style={{display:'none'}}></div>)
                                            }
                                            {this.state.tab == 'billing'?(<CartBilling customer={this.state.customer} shipping_addresses={this.state.shipping_addresses} updateBillingAddress={this.updateBillingAddress} language={this.props.language} addAddress={this.addAddress} shipping={this.state.shipping} international_store={this.props.international_store} countries={this.props.countries} updateState = {this.updateState.bind(this)} />):(<div style={{display:'none'}}></div>)}
                                            {this.state.tab == 'shipping'?(<CartShipping surface_shipping={this.state.surface_shipping} customer={this.state.customer} shipping={this.state.shipping} updateShippingMethod={this.updateShippingMethod} language={this.props.language} updatePaymentMethod={this.updatePaymentMethod}/>):(<div style={{display:'none'}}></div>)}
                                            {this.state.tab == 'gst'?(<CartGST customer={this.state.customer} language={this.props.language} international_store={this.props.international_store} clearGST={this.clearGST} />):(<div style={{display:'none'}}></div>)}
                                            {this.state.tab == 'payment'?(
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
                                                language={this.props.language}
                                                goToShippingTab={this.goToShippingTab}
                                                cod_available_limit={this.props.cod_available_limit}
                                                cod_advance_amount_limit={this.props.cod_advance_amount_limit}
                                              />
                                             ):(<div style={{display:'none'}}></div>)}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div className="clearfix"></div>
                <div className="col-xs-12 mobile_section" >
                <CartSummary customer={this.state.customer} cart_summary = {this.state.cart_summary} language = {this.props.language} checkout_page={this.state.checkout_page} />
                </div>
                {parseInt(this.props.international_store) == 1 ?
                    <div className="col-xs-12 mobile_section"  style={{marginTop:'15',width:'100%', minHeight: 0}} >
                        <span dangerouslySetInnerHTML={this.createMarkup(this.props.language.text_custom_duty_charge)}></span>
                    </div>
                    : null
                }
                <div className="clearfix"></div>
                <div  className="col-xs-12 nopadding bottom_row">
                <div className="col-xs-6 nopadding pull-left view_order_summary">
                <a href="javascript:void(0);" id="checkout_back_btn" onClick={this.goBack} className="view_order_summary_price back_btn_bottom">
               {this.props.language.button_back}
                </a>
                </div>
                <div className="col-xs-6 nopadding pull-right checkout_cart_button">
                <a href="javascript:void(0)" onClick={this.changeTab} className="btn button_continue button-register">
                <span id="checkout_continue_btn">{this.props.language.button_continue}</span>
                </a>
                <a href="javascript:void(0)" className="btn button_continue" id="lazypay_submit" style={{display:'none'}}>
                <span>Submit OTP</span>
                </a>
                </div>
                </div>
                <div style={{display:'none'}} id="confirm_payment_div"></div>

                    <div className="clearfix"></div>
                    <footer>
                        <div className="col-xs-12 Shipping_footer_btn">

                        {!this.state.shipping.shipping_address || (this.state.shipping.shipping_address && this.state.shipping.shipping_address.country == 'India') ? 
                             <div className="mobile_section">
                              <div className="col-xs-12 cart_offer">
                             <br/>
                            {
                            this.state.surface_shipping ?
                             <p><b>{this.props.language.text_free_surface_shipping_2}</b> <br /> <span style={{fontSize:'12px'}}>{this.props.language.text_free_surface_shipping_3} </span></p>
                              :
                             <p><b>{this.props.language.text_free_surface_shipping_1}</b> <br /> <span style={{fontSize:'12px'}}>{this.props.language.text_free_surface_shipping_3} </span></p>
                              }
                            </div>
                            </div>
                            : ''}
                            
                            {(parseInt(this.props.is_app) != 1 && parseInt(this.props.international_store) == 0)?(
                              <div className="mobile_section">
                                <div className="col-xs-12 cart_offer">
                                  <div className="app_cashback">
                                    <div className="col-xs-12 nopadding" >
                                      <b><p>{this.props.language.text_app_cashback_title}</p></b>
                                    </div>

                                    <div className="col-xs-12 nopadding" >
                                      <p>{this.props.language.text_app_cashback_body}</p>
                                    </div>

                                    <div className="col-xs-12 nopadding">
                                      <div className="col-xs-12 nopadding">
                                        <b><p>{this.props.language.text_app_download}</p></b>
                                      </div>
                                      <div className="col-xs-12 nopadding">
                                        <div className="col-xs-6 nopadding">
                                          <a href="https://play.google.com/store/apps/details?id=in.wholesalebox" target="_blank">
                                              <img src={cdn_url+"google-play-android-app.svg"} alt="Android app on google play" style={{width:'95%'}} />
                                          </a>
                                        </div>

                                        <div className="col-xs-6 nopadding">
                                          <a href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank">
                                              <img src={cdn_url+"ios_download.svg"} alt="ios app on app store" style={{width:'95%'}} className="pull-right" />
                                          </a>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            ):(
                              null
                            )}
                            
                            {parseInt(this.props.is_app) == 1?(
                                <div style={{display:'none'}}></div>
                            ):(
                              <div>
                                  <ul>
                                      <li><a href="tel:+911414049163"><label><i className="fa fa-phone" aria-hidden="true"></i></label> {this.props.language.text_checkout_call_us} </a></li>
                                      <li><a href="https://api.whatsapp.com/send?phone=918696491521&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products."><label><i className="fa fa-commenting" aria-hidden="true"></i></label> {this.props.language.text_checkout_chat}</a></li>
                                  </ul>
                              </div>
                            )}
                        </div>
                    </footer>
                </div>
            )}
            </div>
        );
    }

}

