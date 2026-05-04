{/*
 * class Address: This will render the address form
 * @Params: {
 *    class: class of the input fields
 *    language: language data
 *    countries: list of countries(require for .co)
 *    international_store: 1 - true, 0 - false
 *    getZones(method): to get the zones of a country
 *  }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017
 *
 */}

class Address extends React.Component{
    constructor(){
        super();
    }
     
    componentDidMount(){
        var select_list = document.getElementById('country_id');
        select_list.innerHTML = '<option value="">---'+this.props.language.text_select_country+'---</option>';
        for(var i = 0; i < this.props.countries.length; i++){
            var option = document.createElement('option');
            option.value = this.props.countries[i]['country_id'];
            option.text = this.props.countries[i]['name'];
            select_list.appendChild(option);
        }

        $('#pincode').keyup(function () {
            var char_count = $(this).val().length;
            if ( char_count == 6 && !isNaN(parseFloat($(this).val()))) {
                var pincode = $(this).val();

                if ($("#ctoken").get(0)) {
                    var ctoken = $("#ctoken").val();
                } else {
                    var ctoken = 0;
                }

                $.ajax({
                    url: "api/address/autoPopulateAddress/ctoken=" + ctoken,
                    type: "post",
                    dataType: "json",
                    data: "pincode=" + pincode,
                    beforeSend:function(){
                        //
                    },
                    success: function (json) {
                        $('#country_id').val(json.country_id).trigger('change',json.zone_id);
                        $('#city').val(json.city);
                    }
                });
            }
        });

        // $('#pincode').bind('keyup blur',function(){
        //     var node = $(this);
        //     node.val(node.val().replace(/[^0-9]/g,'') ); }
        // );

        $("#country_id").change(function(event, zone_id="") {
            this.props.getZones(zone_id);
        }.bind(this));

        $('#address_telephone').tagsinput({
           confirmKeys: [13, 44, 32]
        });
    }

    render(){
        return(
            <form>
                <div className="mobile_details_panel delivery_box">
                    <h4 className="modal-title">{this.props.language.text_details_title}</h4>
                    <input type="hidden" id="address_id" value="0" />
                    <input id="name" type="text" className="password_box"  placeholder={this.props.language.entry_name} alt="FirstName"/>
                    <div className="text-danger error_address" id="error_name" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_name}</div>

                    <input id="company"  type="text" className="password_box" placeholder={this.props.language.entry_business} alt="Business Name"/>
                    <div className="text-danger error_address" id="error_company" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_company}</div>

                    <input id="address_1" type="text" className="password_box" placeholder={this.props.language.entry_address_1} alt="PINCODE"/>
                    <div className="text-danger error_address" id="error_address_1" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_address_1}</div>

                    <input id="address_2" type="text" className="password_box"  placeholder={this.props.language.entry_address_2} alt="City"/>
                    <div className="text-danger error_address" id="error_address_2" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_address_2}</div>

                    <input id="address_telephone" type="text" className="password_box"  placeholder={this.props.language.entry_contact} alt="Mobile No" data-role="tagsinput" />
                    <div className="text-danger error_address" id="error_address_telephone" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_address_telephone}</div>

                    <input id="pincode" type="text" className="password_box"  placeholder={this.props.language.entry_pincode} alt="PINCODE"/>
                    <div className="text-danger error_address" id="error_pincode" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_postcode}</div>

                    <input id="city" type="text" className="password_box" placeholder={this.props.language.entry_city} alt="City"/>
                    <div className="text-danger error_address" id="error_city" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_city}</div>

                    <div className="clearfix"></div>
                    <div>
                        <select id="country_id" className="password_box" ></select>
                        <div className="text-danger error_addres " id="error_country" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_country}</div>

                        <select name="shipping_zone_id" id="input-shipping-zone"  className="zone input-shipping-zone password_box" disabled></select>
                        <div className="text-danger error_address" id="error_zone" style={{marginTop:'0',display:'none'}}>*{this.props.language.error_zone}</div>
                    </div>
                     <br/>
                    <div className="clearfix"></div>
                    <button type="button"  className="btn deliver_btn pull-right" onClick={this.props.addAddress} data-direction='right' id="address_button">{this.props.language.text_add}</button>
                    <div className="clearfix"></div>
                </div>
            </form>
        );
    }
}
