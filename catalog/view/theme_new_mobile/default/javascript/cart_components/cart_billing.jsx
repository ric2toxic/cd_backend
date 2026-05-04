{/*
 * class CartDelivery: This will render the delivery address of customer (if not, then render the new address form)
 * @Params: {
 *      shipping_addresses: customer's addresses,
 *      shipping:{
 *          shipping_address: address which is selected
 *      },
 *      language: language text for different fields
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017
 *
 */}

class CartBilling extends React.Component{
    constructor(){
        super();
        this.getZones = this.getZones.bind(this);
        this.updateAddress = this.updateAddress.bind(this);
        this.addNewAddress = this.addNewAddress.bind(this);
    }

    editAddress(address,evt){
        if(typeof evt != 'undefined')
            evt.stopPropagation();
        $('#address_popup_header').html(this.props.language.text_edit_address);
        $('#address_button').html(this.props.language.text_update);
        $('#country_id').val(address.country_id);
        if(parseInt(this.props.international_store) == 1){
            $('#country_id').change();
        }
        $('#address_id').val(address.address_id);
        var name = address.firstname+" "+address.lastname;
        $('#name').val(name.trim());
        $('#company').val(address.company);
        $('#address_1').val(address.address_1);
        $('#address_2').val(address.address_2);
        $('#city').val(address.city);
        $('#pincode').val(address.postcode);
        $('#country').val(address.country);
        $('#input-shipping-zone').val(address.zone_id);
        $('.error_address').hide();

        this.getZones(address.zone_id);


        $('#add_new_address').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('#address_telephone').tagsinput('removeAll');
         $('#address_telephone').tagsinput('add', address.address_telephone.join(","));

    }
    addNewAddress(){
        $('form').find("input[type=text], select").val("");
        $('#address_id').val(0);
        $('.error_address').hide();
        $('#address_popup_header').html(this.props.language.text_new_address);
        $('#address_button').html(this.props.language.text_add);
        $('#country_id').val(99);
        this.getZones("");
        $('#add_new_address').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#address_telephone').tagsinput('removeAll');

    }
    getZones(zone_id = ""){
        var self = this;
        if($('#country_id').val() == '') return;
        $.ajax({
            url: 'api/checkout/country/'+$('#country_id').val(),
            type: "post",
            dataType: 'json'
        }).promise()
        .then(function(json){
            var select_list = document.getElementById('input-shipping-zone');
            select_list.innerHTML = '<option value="">---'+self.props.language.text_select_state+'---</option>';
            for(var i = 0; i < json['zone'].length; i++){
                var option = document.createElement('option');
                option.value = json['zone'][i]['zone_id'];
                option.text = json['zone'][i]['name'];
                select_list.appendChild(option);
            }
            if(json['zone'].length == 0){
                var option = document.createElement('option');
                option.value = 0;
                option.text = '--none--';
                select_list.appendChild(option);
                select_list.value = 0;
            }
            select_list.disabled = false;
            if(zone_id != "")
                select_list.value = zone_id;
        })
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
    }

    validateAddress(address){
        var error=false;
        $('.error_address').hide();
        var name = address.firstname+" "+address.lastname;

        if(name.length > 32 || name.length < 1){
            $('#error_name').show();
            error = true;
        }
        var company = address.company;

        var address_1 = address.address_1;
        if(address_1.length > 128 || address_1.length < 3){
            $('#error_address_1').show();
            error = true;
        }
        var address_2 = address.address_2;
        if(address_2.length > 128){
            $('#error_address_2').show();
            error = true;
        }

        var country_id = address.country_id;
        if(!this.props.international_store){
            if( typeof country_id == 'undefined' || country_id == "" ){
                $('#error_country').show();
                error = true;
            }
        }

        var pincode = address.postcode;
        if(!( typeof country_id == 'undefined' || country_id == "" ) && country_id == "99" ){
            if(pincode.length != 6 || isNaN(parseFloat(pincode)) ){
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

        var city = address.city;
        if(city.length > 32 || city.length < 2){
            $('#error_city').show();
            error = true;
        }
        var zone_id = address.zone_id;
        if( zone_id == "" || parseInt(zone_id) < 1){
            $('#error_zone').show();
            error = true;
        }

        return error;
    }

    updateAddress(address){
        var invalid_address = this.validateAddress(address);
        if(invalid_address == true){
            this.editAddress(address,window.event);
            this.props.addAddress();
        }
        else{
            this.props.updateBillingAddress(address,1);
        }

    }

    componentDidUpdate(){
        if(this.props.shipping_addresses.length == 1){
            this.getZones();
        }

        var el = document.getElementById("address_card");
        if(el != null &&  typeof el != 'undefined'){
            el.addEventListener("touchstart", touchStart, false);
            el.addEventListener("touchend", touchEnd, false);
            el.addEventListener("touchcancel", touchCancel, false);
            el.addEventListener("touchmove", touchMove, false);
        }
    }

    componentDidMount(){
        if(this.props.shipping.hasOwnProperty('billing_address')){
            $('.address_penal_billing a').removeClass('selected_delivery_address');
            if(this.props.shipping.billing_address.address_id != 0){
                $('#billing_'+this.props.shipping.billing_address.address_id).addClass('selected_delivery_address');
                //$('#select_shipping_button').css('display','block');
            }

        }
        else if(this.props.shipping_addresses.length == 1){
            //this.props.updateBillingAddress(this.props.shipping_addresses[0]);
        }

        $("#myCarousel").carousel({interval: false});
		ReactDOM.render(<Address addAddress = {this.props.addAddress} language={this.props.language} international_store={this.props.international_store} countries={this.props.countries} getZones={this.getZones} />,document.getElementById('address_popup'));

        //$('.address_penal').on('swipeleft',function(){ $('.carousel_btn_box .right').trigger('click'); });
        //$('.address_penal').on('swiperight',function(){ $('.carousel_btn_box .left').trigger('click'); });

        var el = document.getElementById("address_card");
        if( el != null && typeof el != 'undefined'){
            el.addEventListener("touchstart", touchStart, false);
            el.addEventListener("touchend", touchEnd, false);
            el.addEventListener("touchcancel", touchCancel, false);
            el.addEventListener("touchmove", touchMove, false);
        }

        if (typeof this.default_address !== 'undefined' && this.default_address.incomplete_address == 1) {
            this.editAddress(this.default_address);
        }
    }

    render(){
        var shipping_address_id = 0;
        if(this.props.shipping.hasOwnProperty('billing_address')){
            shipping_address_id = this.props.shipping.billing_address.address_id;
        }
        return(
            <div>

                {jQuery.isEmptyObject(this.props.shipping_addresses)?(
                    <div className="col-xs-12 nopadding">
                        <Address addAddress = {this.props.addAddress} language={this.props.language} international_store={this.props.international_store} countries={this.props.countries} getZones={this.getZones} />
                    </div>
                ):(
                    <div>
                        <div className="col-xs-12 nopadding">
                            <div className="address_card" id="address_card">
                                <div id="myCarousel" className="carousel slide" data-ride="carousel">
                                    {/* Wrapper for slides */}
                                    <div className="carousel-inner" role="listbox">
                                        {this.props.shipping_addresses.map((address,key) =>
                                            {
                                              if(parseInt(this.props.customer.is_dropshipper) == 1)
                                             {
                                                if (address.address_id == this.props.customer.address_id) {
                                                    this.default_address = address;
                                                
                                                var item_class = "item active";

                                                return(
                                                    <div className={item_class} key={key}>
                                                        <div className="address_penal_billing" style={{margin:'5px',padding:'4%'}} >
                                                            <a href="javascript:void(0);" id={"billing_"+address.address_id} onClick={this.updateAddress.bind(this,address)}>
                                                                <ul>
                                                                    <li><span><h4><strong>{address.firstname} {address.lastname}</strong></h4></span></li>
                                                                    <li><span><strong>{address.company}</strong></span></li>
                                                                    <li><span>{address.address_1}</span></li>
                                                                    <li><span>{address.address_2}</span></li>
                                                                    <li><span>{address.address_telephone.join(", ")}</span></li>
                                                                    <li><span>{address.city} {address.zone != "" ? ('('+address.zone+')'):''}</span></li>
                                                                    <li>{address.postcode}</li>
                                                                </ul>

                                                                <div className="selected_address">
                                                                    <button type="button" onClick={this.editAddress.bind(this,address)} className="btn address_btn pull-right"><i className="fa fa-pencil" aria-hidden="true"></i>
                                                                    </button>
                                                                    <div className="clearfix"></div>
                                                                    <button type="button" className="btn deliver_address_btn">{this.props.language.button_billing}</button>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                );
                                               }
                                              }
                                              else
                                              {
                                                if (address.address_id == this.props.customer.address_id) {
                                                    this.default_address = address;
                                                    }
                                                var item_class = "item";
                                                if(key == 0 && shipping_address_id == 0) item_class = "item active";
                                                if(address.address_id == shipping_address_id) item_class = "item active";

                                                return(
                                                    <div className={item_class} key={key}>
                                                        <div className="address_penal_billing" style={{margin:'5px',padding:'4%'}} >
                                                            <a href="javascript:void(0);" id={"billing_"+address.address_id} onClick={this.updateAddress.bind(this,address)}>
                                                                <ul>
                                                                    <li><span><h4><strong>{address.firstname} {address.lastname}</strong></h4></span></li>
                                                                    <li><span><strong>{address.company}</strong></span></li>
                                                                    <li><span>{address.address_1}</span></li>
                                                                    <li><span>{address.address_2}</span></li>
                                                                    <li><span>{address.address_telephone.join(", ")}</span></li>
                                                                    <li><span>{address.city} {address.zone != "" ? ('('+address.zone+')'):''}</span></li>
                                                                    <li>{address.postcode}</li>
                                                                </ul>

                                                                <div className="selected_address">
                                                                    <button type="button" onClick={this.editAddress.bind(this,address)} className="btn address_btn pull-right"><i className="fa fa-pencil" aria-hidden="true"></i>
                                                                    </button>
                                                                    <div className="clearfix"></div>
                                                                    <button type="button" className="btn deliver_address_btn">{this.props.language.button_billing}</button>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                );
                                               
                                              }  
                                            }
                                        )}
                                    </div>
                                    {this.props.shipping_addresses.length == 1 || parseInt(this.props.customer.is_dropshipper) == 1 ?(
                                        <div style={{display:'none'}}></div>
                                    ):(
                                        <div className="carousel_btn_box">
                                            <a className="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                                <i className="fa fa-angle-left" aria-hidden="true"></i>
                                                <span className="sr-only">{this.props.language.button_prev}</span>
                                            </a>
                                            <a className="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                                <i className="fa fa-angle-right" aria-hidden="true"></i>
                                                <span className="sr-only">{this.props.language.button_next}</span>
                                            </a>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                        <div className="clearfix"></div>
                        {parseInt(this.props.customer.is_dropshipper) !== 1 ?
                        <div className="col-xs-12">
                            <button type="button"  onClick={this.addNewAddress} className="btn btn_clear_cart add_delivery_address"><i className="fa fa-plus-square-o" aria-hidden="true"></i>
                                &nbsp;&nbsp;{this.props.language.text_new_address}</button>
                        </div>
                        : ''}
                        <div className="clearfix"></div>
                    </div>
                )
                }
            </div>
        );
    }
}
