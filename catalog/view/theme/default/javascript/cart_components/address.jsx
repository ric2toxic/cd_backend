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
        //$('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();
        var select_list = document.getElementById('country_id');
        select_list.innerHTML = '<option value="">---Select Your Country---</option>';
        for(var i = 0; i < this.props.countries.length; i++){
            var option = document.createElement('option');
            option.value = this.props.countries[i]['country_id'];
            option.text = this.props.countries[i]['name'];
            select_list.appendChild(option);
        }

        $('#pincode').keyup(function () {
            var char_count = $(this).val().length;
            if ( char_count == 6 && !isNaN(parseFloat($(this).val())) ) {
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
                        $('#city').val(json.city);
                        $('#country_id').val(json.country_id).trigger('change',json.zone_id);
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
    }

    render(){
        return(
            <form>
                <div className="mobile_details_panel delivery_box">
                    <h4 className="modal-title">Please provide following details</h4>
                    <input type="hidden" id="address_id" value="0" />
                    <input id="name" type="text"  placeholder="Please Enter Your Name" alt="FirstName"/>
                    <div className="text-danger error_address" id="error_name" style={{marginLeft:'2.5%',marginTop:'0',display:'none'}}>*{this.props.language.error_name}</div>

                    <input id="company"  type="text" placeholder="Please Enter Business Name" alt="Business Name"/>
                    <div className="text-danger error_address" id="error_company" style={{marginLeft:'2.5%',marginTop:'0',display:'none'}}>*{this.props.language.error_company}</div>

                    <input id="address_1" type="text"  placeholder="Enter your address line 1" alt="PINCODE"/>
                    <div className="text-danger error_address" id="error_address_1" style={{marginLeft:'2.55%',marginTop:'0',display:'none'}}>*{this.props.language.error_address_1}</div>

                    <input id="address_2" type="text"   placeholder="Enter your address line 2" alt="City"/>
                    <div className="text-danger error_address" id="error_address_2" style={{marginLeft:'2.55%',marginTop:'0',display:'none'}}>*{this.props.language.error_address_2}</div>

                    <input id="address_telephone" type="text"   placeholder="Enter your contact no" alt="Mobile No" data-role="tagsinput" />
                    <div className="text-danger error_address" id="error_address_telephone" style={{marginLeft:'2.55%',marginTop:'0',display:'none'}}>*{this.props.language.error_address_telephone}</div>

                    <input id="pincode" type="text" className={this.props.class} style={{marginBottom:'0px'}} placeholder="Enter your PINCODE" alt="PINCODE"/>
                    <input id="city" type="text" className={this.props.class} placeholder="Enter your City" alt="City"/>
                    <div >
                        <div className={"text-danger error_address "+this.props.class} id="error_pincode" style={{marginLeft:'2.5%',marginTop:'0',display:'none'}}>*{this.props.language.error_postcode}</div>
                        <div className={"text-danger error_address pull-right "+this.props.class} id="error_city" style={{marginRight:'4%',display:'none'}}>*{this.props.language.error_city}</div>
                    </div>
                    <div className="clearfix"></div>
                    <div >
                        <select id="country_id" className={"form-control "+this.props.class} ></select>
                        <select name="shipping_zone_id" id="input-shipping-zone"  className={"form-control zone input-shipping-zone "+this.props.class} disabled></select>
                        <div className={"text-danger error_address "+this.props.class} id="error_country" style={{marginLeft:'2.5%',marginTop:'0',display:'none'}}>*{this.props.language.error_country}</div>
                        <div className={"text-danger error_address pull-right "+this.props.class} id="error_zone" style={{marginRight:'4%',marginTop:'0',display:'none'}}>*{this.props.language.error_zone}</div>
                    </div>
                     <br/>
                    <div className="clearfix"></div>
                    <button type="button"  className="btn deliver_btn pull-right" onClick={this.props.addAddress} data-direction='right' id="address_button">Add</button>
                    <div className="clearfix"></div>
                </div>
            </form>
        );
    }
}
