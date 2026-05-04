{/*
 * class Cart: This will render cart
 * @Params: {
 *    cart_data: {
 *           cart_data: list of products,
 *           total_sets:total sets,
 *           total_pieces:total pieces,
 *           clear_cart: contains the list of product keys city wise(beacuse we are providing clear at pickup city level)
 *       },
 *    cart_summary:cart_summary,
 *    is_empty: boolean (1: cart is empty,0:cart is not empty),
 *    language: language text for different fields
 *  }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
 */}

class Cart extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            customer:this.props.customer,
            products:this.props.cart_data.products,
            total_sets: this.props.cart_data.total_sets,
            total_pieces:this.props.cart_data.total_pieces,
            cart_summary:this.props.cart_summary,
            clear_cart:this.props.cart_data.clear_cart,
            cartlimitcross:this.props.cart_data.cartlimitcross,
            coupon:this.props.cart_data.coupon,
            weight:this.props.cart_data.weight,
            is_empty:this.props.is_empty

        }
        this.estimateShipping = this.estimateShipping.bind(this);
        this.updateShippingMethod = this.updateShippingMethod.bind(this);
        this.applyCoupon = this.applyCoupon.bind(this);
        this.removeCoupon = this.removeCoupon.bind(this);
        this.placeOrder = this.placeOrder.bind(this);
        this.clearCart = this.clearCart.bind(this);
        this.createMarkup = this.createMarkup.bind(this);
        this.clear = this.clear.bind(this);
        this.getZones = this.getZones.bind(this);
    }

    removeItem(item){
        var name = item.name;

        var commentboxid = "comment_"+item.key.replace(/=/g, "");
        $('#'+commentboxid).remove();

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');

        $.ajax({
            url: 'api/cart/remove',
            type: 'post',
            data: 'key=' + item.key +'&access_token='+access_token+'&customer_id='+customer_id,
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
                localStorage.removeItem('cart_data');
            }
            else{
                total = json['total_sets'];
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.disable_place_order = json['disable_place_order'];
                new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});

            }
            $('#cart-total').html(total);
        }.bind(this))
		.then(function(){
			if(this.props.cart_data.hasOwnProperty('shipping') ){
				if(this.props.cart_data.shipping.hasOwnProperty('shipping_address')){
					this.getShippingMethods('',this.props.cart_data.shipping.shipping_address.zone_id, this.props.cart_data.shipping.shipping_address.country_id);
				}
			}
		}.bind(this))
        .then(function(){
            $("#notification").fadeIn("slow").html(this.props.language.text_success_alert);
            $("#notification").fadeOut(3000);
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
            data: 'key=' + item.key +'&quantity='+item.quantity +'&access_token='+access_token+'&customer_id='+customer_id,
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
            new_cart_summary.totals = json['totals'];
            new_cart_summary.tax_refund = json['tax_refund'];
            new_cart_summary.disable_place_order = json['disable_place_order'];
            new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
            new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
            new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
            this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});

            $('#cart-total').html(json['total_sets']);
        }.bind(this))
		.then(function(){
			if(this.props.cart_data.hasOwnProperty('shipping') ){
				if(this.props.cart_data.shipping.hasOwnProperty('shipping_address')){
					this.getShippingMethods('',this.props.cart_data.shipping.shipping_address.zone_id, this.props.cart_data.shipping.shipping_address.country_id);
				}
			}
		}.bind(this))
        .then(function(){
            $("#notification").fadeIn("slow").html(this.props.language.text_success_alert);
            $("#notification").fadeOut(3000);

        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    moveToWishlist(item){

        var product_id = item.product_id;

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');

        $.ajax({
            url: 'api/cart/addToWishlist',
            type: 'post',
            data: 'product_id=' + product_id +'&access_token='+access_token+'&customer_id='+customer_id,
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
                total = json['total_wishlist_items'];
            }
            else{
                total = json['total_wishlist_items'];
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.disable_place_order = json['disable_place_order'];
                new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});

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

    clearCart(){
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/clearCart',
            type: 'post',
            dataType: 'json',
            data:'access_token='+access_token+'&customer_id='+customer_id,
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            var total = 0;
            if(json['empty'] == '1'){
                this.setState({is_empty:1});
                localStorage.removeItem('cart_data');
            }
            $('#cart-total').html(total);

        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    clear(){
		var modal_body = "Are you sure to remove all items from cart?";
        var modal_footer = '<button type="button" data-dismiss="modal" class="btn deliver_btn clear_popup_btn" id="clear_complete_cart">Yes</button>'+
            '<button type="button" data-dismiss="modal" class="btn popup_close_btn">No</button>';
        $('#confirm_body').html(modal_body);
        $('#confirm_footer').html(modal_footer);

        $('#confirm_popup').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('#clear_complete_cart').click(function(){
            this.clearCart();
        }.bind(this));
    }

    clearPickupCityCart(city,add_to_wishlist = 0){
        var keys = this.state.clear_cart[city];
        keys = keys.join();

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/clearPickupCityCart',
            type: 'post',
            data: 'keys=' + keys +'&access_token='+access_token+'&customer_id='+customer_id+'&add_to_wishlist='+add_to_wishlist,
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
                this.setState({is_empty:1});
                localStorage.removeItem('cart_data');
            }
            else {
                total = json['total_sets'];
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.disable_place_order = json['disable_place_order'];
                new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});

            }
            $('#cart-total').html(total);

        }.bind(this))
		.then(function(){
			if(this.props.cart_data.hasOwnProperty('shipping') ){
				if(this.props.cart_data.shipping.hasOwnProperty('shipping_address')){
					this.getShippingMethods('',this.props.cart_data.shipping.shipping_address.zone_id, this.props.cart_data.shipping.shipping_address.country_id);
				}
			}
		}.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    updateShippingMethod(){

        var shipping_method = $('input[name="shipping_charge"]:checked').val();
        if(typeof shipping_method == 'undefined'){
            return;
        }
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "api/checkout/cartSummary",
            type: "post",
            dataType: "json",
            data: "shipping_method=" + shipping_method +'&access_token='+access_token+'&customer_id='+customer_id,
            beforeSend: function() {
                $('#loading-indicator').show();
            },
            complete: function() {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            var new_cart_summary = this.state.cart_summary;
            new_cart_summary.totals = json['totals'];
            new_cart_summary.tax_refund = json['tax_refund'];

            this.setState({cart_summary:new_cart_summary});
            this.props.cart_data.shipping.shipping_method = shipping_method;

        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    getShippingMethods(pincode,zone_id,country_id){

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "api/checkout/shippingMethod",
            type: "post",
            dataType: "json",
            data: "country_id=" + country_id + "&zone_id=" + zone_id + "&postcode=" + pincode +'&access_token='+access_token+'&customer_id='+customer_id+'&cartlimitcross='+this.state.cartlimitcross,

        }).promise()
        .then(function(json){
            if(json.hasOwnProperty('error') ){
                var target = document.getElementById('shipping_method_table');
                target.innerHTML = '<div class="panel-body estimate_shipping_box">'+json['error']['warning']+'</div>';
                return;
            }
            
            if ( typeof json['shipping_method']['free'] !== 'undefined' ) {
                var shipping_methods = Object.assign(json['shipping_method']['free']['quote'], json['shipping_method']['weight']['quote']);
            } else {
                var shipping_methods = json['shipping_method']['weight']['quote'];
            }

            var target = document.getElementById('shipping_method_table');
            target.innerHTML = ' <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">'+
                '<thead><tr class="cart_table_title"><th></th><th>SHIPPING MODE</th><th>AMOUNT</th></tr></thead>'+
                '<tbody id ="shipping_table_data" ></tbody></table>';

            if(pincode != '' && json['ess_service']){
                target.innerHTML += '<br/><p style="font-size: 14px;">Your PINCODE comes under ESS (Extra Service Station).So, you have to pay extra amount for Delivery charges: </p>'
            }

            var table_data = document.getElementById('shipping_table_data');
            for (var shipping_method in shipping_methods) {
                var newtr = document.createElement('tr');
                newtr.setAttribute('class', 'shipping_methods');
                var newtd = document.createElement('td');
                newtd.innerHTML = '<input type="radio" style="margin-top:13%;" name="shipping_charge" value="' + shipping_methods[shipping_method]['code'] + '">';
                newtr.appendChild(newtd);
                var newtd2 = document.createElement('td');
                newtd2.innerHTML = shipping_methods[shipping_method]['title']+'<br /><span style="color:#999;">('+shipping_methods[shipping_method]['description']+')</span>';
                newtr.appendChild(newtd2);
                var newtd3 = document.createElement('td');
                newtd3.innerHTML = shipping_methods[shipping_method]['text'];
                newtr.appendChild(newtd3);
                table_data.appendChild(newtr);
            }

            $('.shipping_methods').click(function(){
                var code = $(this).find('input').val();
                $('input[value="'+code+'"]').prop("checked",true);
            });

			if(this.props.cart_data.hasOwnProperty('shipping') ){
				if(this.props.cart_data.shipping.hasOwnProperty('shipping_method')){
					$('input[value="'+this.props.cart_data.shipping.shipping_method+'"]').prop('checked',true);
					this.props.cart_data.shipping = {"shipping_address":json['shipping_address'],"shipping_methods":shipping_methods,"shipping_method":this.props.cart_data.shipping.shipping_method}
					this.updateShippingMethod();
				}
				else{
					this.props.cart_data.shipping.shipping_address = json['shipping_address'];
                    this.props.cart_data.shipping.shipping_methods = shipping_methods;
                }
			}
			else{
				this.props.cart_data.shipping = {"shipping_address":json['shipping_address'],"shipping_methods":shipping_methods}
			}

        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    getZones(zone_id = ""){
        var country_list = document.getElementById('country_list');
        if(country_list == null || country_list.value == ""){
            return;
        }
        var country_id = country_list.value;
        $.ajax({
            url: 'api/checkout/country/'+country_id,
            type: "post",
            dataType: 'json'
        }).promise()
        .then(function(json){
            var select_list = document.getElementById('input-shipping-zone');
            select_list.innerHTML = '<option value="0" selected>---Select Your State---</option>';
            for(var i = 0; i < json['zone'].length; i++){
                var option = document.createElement('option');
                option.value = json['zone'][i]['zone_id'];
                option.text = json['zone'][i]['name'];
                select_list.appendChild(option);
            }
            select_list.disabled = false;
            if(zone_id != "")
                select_list.value = zone_id;

            this.getShippingMethods('',select_list.value,country_id);
        }.bind(this))
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    estimateShipping(){

        var pincode = document.getElementById("estimate_pincode").value;
        if(pincode.length < 2 || pincode.length > 10){
            $('#alert_body').html(this.props.language.error_postcode);
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
            return;
        }

        var zone_id = 0;
        var country_id = 0;
        var data = [];

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: "api/address/autoPopulateAddress",
            type: "post",
            dataType: "json",
            data: "pincode=" + pincode +'&access_token='+access_token+'&customer_id='+customer_id,
            beforeSend: function() {
                $('#button_estimate').button('loading');
                $('#loading-indicator').show();
            },
            complete: function() {
                $('#loading-indicator').hide();
                $('#button_estimate').button('reset');
            },
        }).promise()
        .then(function(json){
            if(json['city'] != '') {
                zone_id = json['zone_id'];
                country_id = json['country_id'];
                var pin = document.getElementById('shipping_method_header');
                pin.innerHTML = '<p>Please select preferred Shipping method</p><p>Shipping Estimate for <span class="blue_text" >PINCODE ' + pincode + ' (' + json['city'] + ', ' + json['state'] + ')</span></p>';

                this.getShippingMethods(pincode,zone_id,country_id);

            }
            else{
                var pin = document.getElementById('shipping_method_header');
                pin.innerHTML = '<p>Please select your Country for Shipping Estimate</p>'+
                                '<div class="clearfix"></div>' +
                                '<select class="form-control width50" id="country_list"><option value="0" selected>--Select Country--</option></select>' +
                                '<br><select class="form-control width50" id="input-shipping-zone"></select>'+
                    '<br>';
                document.getElementById('shipping_method_table').innerHTML = '';
                var select_list = document.getElementById('country_list');
                for (var i = 0; i < json['countries'].length; i++) {
                    var option = document.createElement('option');
                    option.value = json['countries'][i]['country_id'];
                    option.text = json['countries'][i]['name'];
                    select_list.appendChild(option);
                }
                $('#country_list').change(function(){
                    this.getZones();
                }.bind(this));
                $('#input-shipping-zone').change(function(){
                    this.getShippingMethods('',$('#input-shipping-zone').val(), $('#country_list').val());
                }.bind(this));
                if(json['country_id'] != 0)
                    select_list.value = json['country_id'];
                else
                    select_list.value = 99;
                this.getZones();
            }
        }.bind(this)).then(function(){
            $('#estimate_shipping_success').modal({
                backdrop: 'static',
                keyboard: false
            });
        })
        .fail(function(xhr,exception){
            $('#loading-indicator').hide();
            console.log("Error: "+xhr.status+", exception: "+exception);
        });

    }

    componentDidUpdate(){
        $('#review_out_of_stock').click(function () {
            var id = $('#last_moq_error_product').val();
            if(id == "") {
                id = $('#last_out_of_stock_product').val();
            }
            if(id == ""){
                id = $('#last_quantity_reduced_product').val();
            }
            if(id == "")
                return;
            $('html, body').animate({
                scrollTop:  $('div[id="'+id+'"]').offset().top - $('#cart_full_box').offset().top - 80
            },'slow');
        });
    }

    placeOrder(){

        if(parseInt(this.state.cart_summary.disable_place_order) == 1 ){
            $('#alert_body').html(this.props.cart_summary.text_cart_minimum);
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
            return;
        }

        // first check wether customer is logged in or not, if not then first ask for login.
        if( this.state.customer.customer_id == null || parseInt(this.state.customer.customer_id) == 0){
            $('input[name=referrers]').val('cart');
            $("#login_link_header").trigger('click');
            return;
        }

        var value = 0;
        if ($('#c-form-check').prop("checked")) {
            value = 1;
        }

        var customer_id = getCookie('customer_id');

        var shipping = {};
        if(this.props.cart_data.hasOwnProperty('shipping')){
            shipping = this.props.cart_data.shipping;
        }
        var cart = {"customer":this.state.customer,"cart_data":{"products":this.state.products ,"total_sets":this.state.total_sets,"total_pieces":this.state.total_pieces,
            "clear_cart":this.state.clear_cart,"cartlimitcross":this.state.cartlimitcross,"weight":this.state.weight},"cart_summary":this.state.cart_summary ,"shipping":shipping,"tab":"delivery","have_gst_tab":0};
        localStorage.setItem(customer_id+'_cart_data', JSON.stringify(cart));
        window.location = this.props.cart_summary.checkout;

    }

    applyCoupon(){
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/cart/applyCoupon',
            type: 'post',
            data: 'coupon=' + encodeURIComponent($('input[name=\'promo_code\']').val()) + '&access_token=' + access_token + '&customer_id=' + customer_id,
            dataType: 'json',
            beforeSend: function () {
                $('#button-coupon').button('loading');
                $('#loading-indicator').show();
            },
            complete: function () {
                $('#button-coupon').button('reset');
                $('#loading-indicator').hide();
            }
        }).promise()
            .then(function(json){
                $('.promo_code_penal').next().remove();
                if (json['error']) {
                    $('.promo_code_penal').after('<div class="alert alert-danger" style="margin-top: 5px;"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
                else{

                    var new_cart_summary = this.state.cart_summary;
                    new_cart_summary.totals = json['totals'];
                    new_cart_summary.tax_refund = json['tax_refund'];
                    new_cart_summary.disable_place_order = json['disable_place_order'];
                    new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                    new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                    new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                    this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});
                    $('#cart > button').html('<i class="fa fa-shopping-cart"></i><span id="cart-total-desktop"> Cart <span class="cart_number">' + json['total_sets'] + '</span></span>');
                    $("#notification").fadeIn("slow").html(this.props.language.text_promo_success);
                    $("#notification").fadeOut(2500);
                }
            }.bind(this));

    }

    removeCoupon(){
        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $('#remove_coupon').tooltip('hide');
        $.ajax({
            url: 'api/cart/removeCoupon',
            type: 'post',
            data: 'access_token=' + access_token + '&customer_id=' + customer_id,
            dataType: 'json',
            beforeSend: function () {
                $('#loading-indicator').show();
            },
            complete: function () {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            var new_cart_summary = this.state.cart_summary;
            new_cart_summary.totals = json['totals'];
            new_cart_summary.tax_refund = json['tax_refund'];
            new_cart_summary.disable_place_order = json['disable_place_order'];
            new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
            new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
            new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
            this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});
            $('#cart > button').html('<i class="fa fa-shopping-cart"></i><span id="cart-total-desktop"> Cart <span class="cart_number">' + json['total_sets'] + '</span></span>');
        }.bind(this))
        .then(function(){
            $("#notification").fadeIn("slow").html(this.props.language.text_promo_remove);
            $("#notification").fadeOut(2500);
        }.bind(this));

    }

    showOrderSummary(){
        $('html, body').animate({
            scrollTop:  $('#order_summary').offset().top - $('#cart_full_box').offset().top - 80
        },'slow');
    }

    componentDidMount(props){
        $('.skeleton').remove();

        if(parseInt(this.props.international_store) == 0){
            $('#estimate_pincode').bind('keyup blur',function(){
                var node = $(this);
                node.val(node.val().replace(/[^0-9]/g,'') ); }
            );
        }

        $('#save_estimate_shipping').click(function () {
                this.updateShippingMethod();
            }.bind(this)
        )

        $('#review_out_of_stock').click(function () {
            var id = $('#last_moq_error_product').val();
            if(id == "") {
                id = $('#last_out_of_stock_product').val();
            }
            if(id == ""){
                id = $('#last_quantity_reduced_product').val();
            }
            if(id == "")
                return;
            $('html, body').animate({
                scrollTop:  $('div[id="'+id+'"]').offset().top - $('#cart_full_box').offset().top - 80
            },'slow');
        });

        $('#submit_product_comment').click(function(){
            var product_key = $('#comment_product_key').val();
            var product_comment = $('#product_comment').val();
            var cleanComment = product_comment.replace(/<\/?[^>]+(>|$)/g, "");
            if(cleanComment.length < 10){
                $('#error_product_comment').html("Comment should have at least 10 characters.");
                return;
            }
            $.ajax({
                url: 'api/cart/addComment',
                type: 'post',
                data: 'key=' + product_key + '&comment='+ cleanComment,
                dataType: 'json',
                beforeSend:function(){
                    $('#loading-indicator').show();
                },
                complete:function () {
                    $('#loading-indicator').hide();
                    $('#product_comment_popup').modal('hide');
                }
            }).promise()
            .then(function (json) {
                var new_cart_summary = this.state.cart_summary;
                new_cart_summary.totals = json['totals'];
                new_cart_summary.tax_refund = json['tax_refund'];
                new_cart_summary.disable_place_order = json['disable_place_order'];
                new_cart_summary.text_cart_minimum = json['text_cart_minimum'];
                new_cart_summary.total_quantity_reduced_products = json['total_quantity_reduced_products'];
                new_cart_summary.total_out_of_stock_products = json['total_out_of_stock_products'];
                this.setState({products:json['products'], total_sets:json['total_sets'], total_pieces:json['total_pieces'],cart_summary:new_cart_summary,clear_cart:json['clear_cart'],cartlimitcross:json['cartlimitcross'],coupon:json['coupon'],weight:json['weight']});
            }.bind(this))
            .fail(function(xhr,exception){
                console.log("Error: "+xhr.status+", exception: "+exception);
            });
        }.bind(this));
    }

    createMarkup(html) {
        return {__html: html};
    }

    renderCart(){
        var total_payable_amount = this.state.cart_summary.totals.total;
        if("net_payable_amount" in this.state.cart_summary.totals == true) {
            total_payable_amount = this.state.cart_summary.totals.net_payable_amount;
        }
        return (
            <div>
                <div className="col-xs-12 mobile_section">
                    <div className="panel-group" id="accordion">
                        <section className="panel cart_box">
                            <div className="cart_main_title main_title_active">
                                <h4 className="panel-title shopping_cart_title">
                                    <a className="accordion-toggle" data-parent="#accordion">
                                        {this.props.language.heading_title}
                                        <small><button className="btn btn-estimate pull-right" onClick={this.clear} ><i className="fa fa-trash-o" aria-hidden="true"></i> clear</button></small>
                                        <br/><small>({this.state.total_sets} Sets, {this.state.total_pieces} Pieces  &nbsp;| &nbsp;{this.state.weight})</small>

                                    </a>
                                </h4>

                            </div>
                            <div id="collapseOne" className="collapse in">
                                <div className="panel-body nopadding">
                                    { Object.keys(this.state.products).map(
                                        (city) =>
                                            {
                                                return (
                                                    <div  key={city} >
                                                        <PickupCity city={city} key={city} clearPickupCityCart={this.clearPickupCityCart.bind(this)} language={this.props.language} />
                                                        <div className="clearfix"></div>
                                                        <div>
                                                            {Object.keys(this.state.products[city]).map((combo_product_key,key) =>
                                                              <div key = {key} style={{ margin: '2px', border:Object.keys(this.state.products[city][combo_product_key]).length > 1 ? "1px solid grey" : "0px"}}>
                                                                {this.state.products[city][combo_product_key].map((item, key) => 
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

                                    <div className="clearfix"></div>
                                    <div className="cart_detail col-xs-12">
                                        <div className="cart_top_bar">
                                            <div className="col-xs-12 top_right_box">
                                                <span className="col-xs-12 nopadding">ESTIMATE SHIPPING:</span>
                                                <div style={{display:'block',textAlign:'center'}}>
                                                    <input type="text" className="form-control top-pincode" id="estimate_pincode" maxLength="10" placeholder="Enter PINCODE " />
                                                <button type="button" className="btn btn-estimate estimate_shipping" id="button_estimate" onClick={this.estimateShipping}>ESTIMATE SHIPPING</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <div className="col-xs-12 mobile_section" id="order_summary">
                    <CartSummary customer={this.state.customer} cart_summary = {this.state.cart_summary} language = {this.props.language} checkout_page={0} placeOrder={this.placeOrder} applyCoupon={this.applyCoupon} removeCoupon={this.removeCoupon} coupon={this.state.coupon} />
                    {parseInt(this.state.cart_summary.total_out_of_stock_products) > 0 || parseInt(this.state.cart_summary.total_quantity_reduced_products) > 0?(
                            <div className="col-xs-12" style={{marginTop:'2%',border:'solid lightgray 1px',background:'#f0f3ff'}} >
                                <br/>
                                <ul className="text_blink">
                                    {parseInt(this.state.cart_summary.total_out_of_stock_products) > 0?(<li style={{color:'#cc0000',fontSize:'13px'}}><span >{this.state.cart_summary.total_out_of_stock_products}{this.props.language.text_out_of_stock_review}</span></li>):(<span></span>)}
                                    {parseInt(this.state.cart_summary.total_quantity_reduced_products) > 0?(<li style={{color:'#6666ff',fontSize:'13px'}}><strong>{this.state.cart_summary.total_quantity_reduced_products}{this.props.language.text_quantity_reduced_review}</strong></li>):(<span></span>)}
                                </ul>
                                <h4 className="row center" id="review_out_of_stock" style={{textAlign:'center',color:'#000000',cursor:'pointer'}}><u>{this.props.language.text_review}</u></h4>
                            </div>
                        ):(
                            <div>
                            </div>
                        )
                    }
                </div>

                {parseInt(this.props.international_store) == 0 ?
                  <CartOffer language={this.props.language}/>
                : null}

                <div className="clearfix"></div>
                <div  className="col-xs-12 nopadding bottom_row">
                    <div className="col-xs-6 nopadding pull-left view_order_summary">
                        <a href="javascript:void(0);" onClick={this.showOrderSummary} className="view_order_summary_price" >
                          <span dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span>
                          <span className="view_order_summary_detail">view order summary</span>
                        </a>
                    </div>
                    <div className="col-xs-6 nopadding pull-right checkout_cart_button">
                        <a href="javascript:void(0)" onClick={this.placeOrder} className="btn button_continue" id="button_checkout">Checkout</a>
                    </div>
                </div>
                {/*<div className="down_btn"><a href="javascript:void(0)" onClick={this.placeOrder}>PLACE ORDER  <i className="fa fa-angle-right" aria-hidden="true"></i></a></div>
*/}
                <input id = "last_out_of_stock_product" type="hidden" value=""></input>
                <input id = "last_quantity_reduced_product" type="hidden" value=""></input>
                <input id = "last_moq_error_product" type="hidden" value=""></input>
            </div>
        );
    }

    render() {
        return (
            <div className="cart_full_box" >
                { parseInt(this.state.is_empty) == 1 ?
                    (
                        <div className="cart_empty_content">
                            <div className="cart_empty_img">
                                <img src="image/cart_empty.jpg" className="img-responsive" alt="cart empty"/>
                            </div>
                            <div className="cart_empty_btn" dangerouslySetInnerHTML={this.createMarkup(this.props.language.cart_empty)}>
                            </div>
                            <div className="cart_empty_btn">
                                <a href="">{this.props.language.text_start_shopping}</a>
                            </div>
                        </div>
                    ):( this.renderCart() )
                }
            </div>
        );
    }

}
