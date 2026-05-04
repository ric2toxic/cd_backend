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

class CartDelivery extends React.Component{
    constructor(){
        super();
        this.getZones = this.getZones.bind(this);
        this.changeTab = this.changeTab.bind(this);
        this.updateAddress = this.updateAddress.bind(this);
        this.addNewAddress = this.addNewAddress.bind(this);
    }

    editAddress(address,evt){
        if(typeof evt != 'undefined')
            evt.stopPropagation();
        $('#address_popup_header').html('Edit Your Address');
        $('#address_button').html('Update');
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
        $('#address_popup_header').html('Add New Address');
        $('#address_button').html('Add');
        $('#country_id').val(99);
        this.getZones("");
        $('#add_new_address').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#address_telephone').tagsinput('removeAll');

    }

    getZones(zone_id = ""){
        if($('#country_id').val() == '') return;
        $.ajax({
            url: 'api/checkout/country/'+$('#country_id').val(),
            type: "post",
            dataType: 'json'
        }).promise()
        .then(function(json){
            var select_list = document.getElementById('input-shipping-zone');
            select_list.innerHTML = '<option value="">---Select Your State---</option>';
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
            this.props.updateDeliveryAddress(address);
        }

    }

    changeTab(){

        this.props.changeTab('billing');
    }

    componentDidUpdate(){
        if(this.props.shipping_addresses.length == 1){
            this.getZones();
        }
    }

    componentDidMount(){
        if(this.props.shipping.hasOwnProperty('shipping_address')){
            // $('.address_penal').removeClass('selected_delivery_address');
            // if(this.props.shipping.shipping_address.address_id != 0){
            //     $('#'+this.props.shipping.shipping_address.address_id).addClass('selected_delivery_address');
            //     $('#select_shipping_button').css('display','block');
            // }

        }
        else if(this.props.shipping_addresses.length == 1){
            //this.props.updateDeliveryAddress(this.props.shipping_addresses[0]);
        }


		ReactDOM.render(<Address addAddress = {this.props.addAddress} class ="sm_input" language={this.props.language} international_store={this.props.international_store} countries={this.props.countries} getZones={this.getZones} />,document.getElementById('address_popup'));

        if (typeof this.default_address !== 'undefined' && this.default_address.incomplete_address == 1) {
            this.editAddress(this.default_address);
        }

    }

    render(){
        return(
            <div>

                {jQuery.isEmptyObject(this.props.shipping_addresses)?(
                    <div className="col-sm-8">
                        <Address addAddress = {this.props.addAddress} class ="width30" language={this.props.language} international_store={this.props.international_store} countries={this.props.countries} getZones={this.getZones} />
                    </div>
                ):(
                    <div>
						<div className="col-sm-12 checkout_add_address_button" id="add_address_div" >
                            <div><span className="cart_data_heading">{this.props.language.text_select_address}</span></div>
							<button type="button" className="btn btn_clear_cart pull-right" style={{marginRight:'1%'}} onClick={this.addNewAddress}>
								<i className="fa fa-plus-square-o" aria-hidden="true"></i>
								&ensp;Add Address
							</button>
                            <div className="clearfix"></div>
						</div>
                        <div>
                            {this.props.shipping_addresses.map((address) =>
                                {
                                    if (address.address_id == this.props.customer.address_id) {
                                        this.default_address = address;
                                    }
                                    return(
                                        <div className="col-sm-4 cart_table" key={address.address_id}>
                                            <div className="address_penal" id={address.address_id} style={{margin:'5px',padding:'4%'}} onClick={this.updateAddress.bind(this,address)} data-toggle="tooltip" title={address.address_1+", "+address.address_2+", "+address.address_telephone.join(", ")+", "+address.city+" "+address.zone+", "+address.postcode}>
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
                                                    <div className="deliver_address_text">DELIVER TO THIS ADDRESS</div>
                                                </div>
                                            </div>
                                        </div>
                                    );

                                }
                            )}
                        </div>
                        <div className="clearfix"></div>
                    </div>
                )
                }

                <div className="col-sm-12 cart_table" style={{display:'none'}} id="select_shipping_button">
                    <button className="btn deliver_btn pull-right" data-parent="#accordion" onClick={this.changeTab} style={{position:'relative',bottom:'-25px'}} data-target="#collapseShipping" >CONTINUE &nbsp;<i className="fa fa-caret-right" aria-hidden="true"></i> </button>
                </div>

            </div>
        );
    }
}
