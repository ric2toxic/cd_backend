class Register extends React.Component {

   constructor(props)
   {
   	 super(props);

       this.state = {
           fields: {},
           errors: {},
           CustomersType:[],
           CustomersDropshipperType:[],
           dropshipper:0
       }

      this.register_form        = this.register_form.bind(this);
      this.handleValidation     = this.handleValidation.bind(this);
      this.handleChange         = this.handleChange.bind(this); 
      this.registerSubmit       = this.registerSubmit.bind(this);
      this.postcode             = this.postcode.bind(this);
      this.reg_telephone_signup = this.reg_telephone_signup.bind(this);
      this.get_state_list       = this.get_state_list.bind(this);
      this.customer_type        = this.customer_type.bind(this); 

   } 

    componentDidMount()
    {
      this.customer_type();
    }

    customer_type()
    {
      axios({
        method:'get',
        url:'./api/login/customer_type',
        responseType:'json'
        })
       .then(response => {
            this.setState({CustomersType: response.data.data.CustomersType});
            this.setState({CustomersDropshipperType: response.data.data.CustomersDropshipperType})
       });
     }

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        fields['reg_telephone'] = $("#reg_telephone_signup").val();
        fields['reg_email']     = $("#reg_email_signup").val();
        fields['country_id']    = $("#country_id").val();

        this.setState({fields});  

       if(this.props.international_store == 1)
       {

        //Email
        if(!fields["reg_email"]){
           formIsValid = false;
           errors["reg_email"] = this.props.language.error_email2;
        }

        if(typeof fields["reg_email"] !== "undefined")
        {
            let lastAtPos = fields["reg_email"].lastIndexOf('@');
            let lastDotPos = fields["reg_email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["reg_email"].indexOf('@@') == -1 && lastDotPos > 2 && (fields["reg_email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["reg_email"] = this.props.language.error_email2;
            }
        }

         //Password
        if(!fields["password"]){
           formIsValid = false;
           errors["password"] = this.props.language.error_password;
        }
         if(typeof fields["password"] !== "undefined"){
             if(fields["password"].length < 4 || fields["password"].length > 20){
                 formIsValid = false;
                 errors["password"] = this.props.language.error_password;
             }          
        }

        //country
        if(!fields["country_id"]){
           formIsValid = false;
           errors["country_id"] = this.props.language.error_country;
        }
        if(typeof fields["country_id"] !== "undefined"){
             if(fields["country_id"] == 0){
                 formIsValid = false;
                 errors["country_id"] = this.props.language.error_country;
             }          
        }
         
      }
      else
      {
        var postcode_min = 6;
        var postcode_max = 6;
  
        //Name
        if(!fields["name"]){
           formIsValid = false;
           errors["name"] = this.props.language.error_name;
        }
        if(typeof fields["name"] !== "undefined"){
             if(!fields["name"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["name"] = "Only letters";
             }
             if(fields["name"].length < 2 || fields["name"].length > 64){
                 formIsValid = false;
                 errors["name"] = this.props.language.error_name_limit;
             }          
        } 
        //Password
        if(!fields["password"]){
           formIsValid = false;
           errors["password"] = this.props.language.error_password;
        }
         if(typeof fields["password"] !== "undefined"){
             if(fields["password"].length < 4 || fields["password"].length > 20){
                 formIsValid = false;
                 errors["password"] = this.props.language.error_password;
             }          
        }
        //company
        if(!fields["company"]){
           formIsValid = false;
           errors["company"] = this.props.language.error_company;
        }
         if(typeof fields["company"] !== "undefined"){
             if(fields["company"].length < 2 || fields["company"].length > 64){
                 formIsValid = false;
                 errors["company"] = this.props.language.error_company_limit;
             }          
        }
        //gst number
        if(fields["gst_uin_number"] && fields["gst_uin_number"] == 1){
           
           if(!fields["gst_number"]){
            formIsValid = false;
            errors["gst_number"] = this.props.language.error_gst_number;
           }
       
        if(typeof fields["gst_number"] !== "undefined") {  
         var gst_number = fields["gst_number"];
         var gst_number_without_checksum = gst_number.substring(0, gst_number.length - 1);
         var entered_checksum_character = gst_number.substr(-1);
         var gst_number_result = this.validateGSTNumber(gst_number);
          if(gst_number_result == false) {
            formIsValid = false;
            errors["gst_number"] = this.props.language.error_gst_number;
          } else {
            if(gst_number_result != entered_checksum_character) {
              formIsValid = false;
              errors["gst_number"] = "Invalid GST Number! Do you mean "+gst_number_without_checksum+gst_number_result+" instead? Please check and enter correct GST number again!";
            }
          }
        }


        }     
        //postcode
        if(!fields["postcode"]){
           formIsValid = false;
           errors["postcode"] = this.props.language.error_postcode;
        } 
        if(typeof fields["postcode"] !== "undefined"){
             if(fields["postcode"].length < 2 || fields["postcode"].length > 10){
                 formIsValid = false;
                 errors["postcode"] = this.props.language.error_postcode2;
             }          
        } 
        //address
        if(!fields["address_1"]){
           formIsValid = false;
           errors["address_1"] = this.props.language.error_address;
        }  
        if(typeof fields["address_1"] !== "undefined"){
             if(fields["address_1"].length < 3 || fields["address_1"].length > 128){
                 formIsValid = false;
                 errors["address_1"] = this.props.language.error_address;
             }          
        }  

        //Telephone
        if(!fields["reg_telephone"]){
           formIsValid = false;
           errors["reg_telephone"] = this.props.language.error_telephone;
        }   

        if(typeof fields["reg_telephone"] !== "undefined"){
             if(this.props.international_store == 0 && fields["reg_telephone"].length < 10){
                 formIsValid = false;
                 errors["reg_telephone"] = this.props.language.error_telephone2;
             } 
             if(this.props.international_store == 1 && fields["reg_telephone"].length < 6){
                 formIsValid = false;
                 errors["reg_telephone"] = this.props.language.error_telephone2;
             }         
        }
        //Email

        if(typeof fields["reg_email"] !== "undefined" && fields["reg_email"] != ''){
            let lastAtPos = fields["reg_email"].lastIndexOf('@');
            let lastDotPos = fields["reg_email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["reg_email"].indexOf('@@') == -1 && lastDotPos > 2 && (fields["reg_email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["reg_email"] = this.props.language.error_email2;
            }
       }

      } 

       this.setState({errors: errors});
       return formIsValid;
   }

   validateGSTNumber(gst_number) {
       var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/;
       if (reggstin.test(gst_number) == false) {
           return false;
       }
       
       var factor_even = 1;
       var factor_odd = 2;
       var sum = 0;
       var gst_number_array = gst_number.split("");
       var checksum_weight_array = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split("");
       var checksum_mod = checksum_weight_array.length;
       var factor = factor_even;
       
       if(gst_number_array.length == 15) {
           gst_number_array.pop();
       }
       
       for(index = 0; index < gst_number_array.length; ++index) {
           var current_letter_weight = checksum_weight_array.indexOf(gst_number_array[index]);
           var current_checksum_digit = 0;
           if(current_letter_weight != -1) {
               current_checksum_digit = current_letter_weight * factor;
               current_checksum_digit = parseInt((current_checksum_digit / checksum_mod) + (current_checksum_digit % checksum_mod));
               sum += current_checksum_digit;
           }
           factor = (factor == factor_even) ? factor_odd : factor_even;
       }
       
       var calculated_checksum_weight = (checksum_mod - (sum % checksum_mod)) % checksum_mod;
       var calculated_checksum_letter = (checksum_weight_array[calculated_checksum_weight])
                                       ? checksum_weight_array[calculated_checksum_weight] 
                                       : false;
       return calculated_checksum_letter;
   }
   
   registerSubmit(e){
        e.preventDefault();
        if(this.handleValidation()){
          this.register_form();
        }
    }

    handleChange(field, e){  
        let fields = this.state.fields;
        var dropshipper = $("#input-is_dropshipper").val();
        if(dropshipper > 0) { this.state.dropshipper = dropshipper; }
        
        if(e.target.name === 'postcode') { this.postcode(); }
        if(e.target.name === 'country_id') { this.get_state_list(); }
        if(e.target.name === 'reg_telephone') { this.reg_telephone_signup(); }   
        if(e.target.name === 'gst_number') { $("#gst_number").val(e.target.value.toUpperCase());} 
        if(e.target.name === 'gst_uin_number' &&  e.target.checked == false) { fields[field] = 0;  }   
        else { fields[field] = e.target.value;  }    
        this.setState({fields});
        this.handleValidation();
        
    }

   register_form() {

         let international_store = this.props.international_store;

         $("#register_form input[type=submit]").val('loading...').prop("disabled", true);
         var disabled = $("#country_id").attr('disabled');
         $("#country_id, #zone_id, #city").prop("disabled", false);
         $("#input-is_dropshipper").val(this.state.dropshipper);
        
         var actionurl = './api/login/register';
         var form_data = $("#register_form").serialize();
         var ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                { 
                    var data = response.data;
                    $("#country_id, #zone_id, #city").prop("disabled", disabled);
                    if(data['error'])
                    {
                     $("#register_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_register_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_register_error').show();
                     $('.login_register_success').hide();
                    }
                    else
                    {
                      var gst = 0;
                      var dropshipper = false;
                      if(data['gst_number'] && data['gst_number'] != '') { gst = 1; }
                      if(data['is_dropshipper'] && parseInt(data['is_dropshipper'], 0) > 0) { dropshipper = true; }    

                      dataLayer.push({'customer_id': data['customer_id'], 
                                      'first_name': data['first_name'], 
                                      'last_name': data['last_name'], 
                                      'email': data['email'], 
                                      'phone': data['telephone'],
                                      'customer_type': data['customer_type'],
                                      'store_id': international_store,
                                      'user_city': data['user_city'],
                                      'gst': gst,
                                      'dropshipper': dropshipper,
                                      'has_website': parseInt(data['has_website'], 0),
                                      'self_order': data['self_order'].toString(),
                                      'pincode': parseInt(data['pincode'], 10),
                                      'membership': data['membership']['membership'].toString(),
                                      'membership_id': data['membership']['membership_id'].toString(),
                                      'expiry_date': data['membership']['expiry_date'].toString() });

                        dataLayer.push({'event': 'we-custom-registration'}); 
                        dataLayer.push({'event': 'we-custom-login'});
                      if(data['redirect_cart'] == 'credit')
                      { 
                        setTimeout(function(){ window.location.assign(data['credit_application']); }, 1000);
                      }
                      else
                      {
                       setTimeout(function(){ window.location.assign(data['url']); }, 1000);
                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
    }
   
      get_state_list(zone_id=0) {
         var ajax = $.ajax({
                url: "./api/login/state_list",
                type: 'post',
                dataType: 'json',
                data: { country_id: $("#country_id").val() },
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                  $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                success: function(response)
                {
                    var data = response.data;
                    var result = '<option value="">'+data['zone_title']+'</option>';
                    for(var i=0; i<data['zone_data'].length; i++)
                    {
                      result = result+'<option value="'+data['zone_data'][i].zone_id+'">'+data['zone_data'][i].name+'</option>';
                    }
                    $(" #zone_id").html(result);
                    if(zone_id > 0) { $("#zone_id option[value=" + zone_id + "]").prop("selected",true); }
                },
               complete: function(data){
                var ajax = null;
                $('.fa-spin').remove();
               }
         });
       } 


       postcode() {
        var self = this;
        var value = $("#postcode").val();
        if (value && /^\d{6,}$/.test(value.trim()) && value.length == 6)
        {
          var ajax = $.ajax({
                url: "./api/login/pincodeAddress",
                type: 'post',
                dataType: 'json',
                data: { postcode: value },
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                {
                  var data = response.data;
                  if(!data['error'])
                  {
                    if(data['zone_id'] == '')
                    {
                      $("#country_id option[value='99'").prop('selected',true);
                      self.get_state_list();
                      $("#city").val('');
                      $("#city").prev('label').css('margin-top', '20px');
                    }
                    else
                    {
                      $("#city").prev('label').css('margin-top', '0px');
                      $("#city").val(data['city']);
                      $("#country_id option[value=" + data['country_id'] + "]").prop("selected",true);
                      self.get_state_list(data['zone_id']);
                    }
                  }
                },
               complete: function(data){
                var ajax = null;
               }
         });
        }
    }

      reg_telephone_signup()
        {
            var reg_telephone = $('#reg_telephone_signup').val();
            if (reg_telephone.charAt(0) == 0 )
            {
              $('#reg_telephone_signup').val(reg_telephone.slice(1));
            }
            if (/\D/g.test(reg_telephone))
            {
             var node = $('#reg_telephone_signup');
             node.val(node.val().replace(/[^0-9]/g,'') );
            }
        }     

	render() {
    
    $("#input-is_dropshipper").val();

    function show_password()
      {
          if($(".show_password").html() == 'Show Password')
            {
                $(".show_password").html('Hide Password');
                $("#register_password").attr('type','text');
            }
          else
           {
               $(".show_password").html('Show Password');
               $("#register_password").attr('type','password');
           }
      }

      $(document).delegate('#gst_uin_number', 'click', function(e){
          if($(this).prop("checked") == true){
           $("#gst_uin_number_area").removeClass("hide");
           }
          else { 
           $("#gst_uin_number_area").addClass("hide");
           $('#gst_number').val('');
           $("#gst_number").prev('label').animate({'margin-top': '20px'});
          }
      });

      $(document).delegate('.customer_type_dropshipper_checkbox', 'click', function(e){
         $(".customer_type_checkbox").prop("checked", false);     
      });

      $(document).delegate('.customer_type_checkbox', 'click', function(e){
         $(".customer_type_dropshipper_checkbox").prop("checked", false);     
      });

		return (
       <div className="modal fade add_new_address" id="signup_popup" role="dialog" data-backdrop="static" data-keyboard="false">
        <div className="modal-dialog login_register_popup">
          <div className="modal-content">
            <div className="modal-header address_popup_head">
              <button type="button" className="close" data-dismiss="modal" id="signup_popup_close">&times;</button>
              <button className="hide" data-toggle="modal" data-target="#signup_popup" id="signup_popup_open"></button>
              <h4 className="modal-title">SIGN UP</h4>
            </div>
            <div className="modal-body popup_scroll">

            <div className="success_msg login_register_success"></div>
            <div className="danger_msg login_register_error"></div>

            <form name="register_form"  id="register_form" onSubmit={this.registerSubmit.bind(this)}>
            <input type="hidden" name="is_dropshipper" value="" id="input-is_dropshipper" className="form-control" />
            <input type="hidden" name="referrers" value=""/>
            <input type="hidden" name="redirect_cart" id="redirect_cart" />  
                <div className="mobile_details_panel">
                         <span className="flag_area"><i className="country-flag flagstrap-icon flagstrap-"></i></span>
                         <div className="login_mobile_nmr"></div>
                         <div className="clearfix"></div>
                         <h4 className="modal-title">Please provide following details</h4>
                         <div className="clearfix"></div>

           
            {
             this.state.CustomersType.map((customer_type, index) => { 
              return (<div className="checkbox_information" key={index}>
                  <label>
                  <input type="checkbox" name="customer_type_id[]" className="customer_type_checkbox" value={customer_type.customer_type_id} /> {customer_type.type}
                  <div className="control__indicator"></div>
                  </label>
                  </div>)
            })
            }

            <div className="clearfix"></div>
            {this.state.CustomersDropshipperType && this.state.CustomersDropshipperType.length > 0 ?
             <p className="middle_title"><span>OR</span></p>
            :''} 
            <div className="clearfix"></div>


            {this.state.CustomersDropshipperType ?
             this.state.CustomersDropshipperType.map((customer_type, index) => { 
              return (<div className="checkbox_information" style={{width:'94%'}} key={index}>
                  <label>
                  <input type="checkbox" name="customer_type_id[]" className="customer_type_dropshipper_checkbox" value={customer_type.customer_type_id} /> {customer_type.type}
                  <div className="control__indicator"></div>
                  </label>
                  </div>)
            })
            : ''}

            <div className="clearfix"></div>
            <div id="reg_email_field">
            <div className="col-sm-12 nopadding">
            <input type="text" name="reg_email" id="reg_email_signup" className="password_box" placeholder={this.props.language.entry_email_address} alt={this.props.language.entry_email_address} autoComplete="new-password" onChange={this.handleChange.bind(this, "reg_email")} />
           <span className="error">{this.state.errors["reg_email"]}</span>
           </div>
            <div className="clearfix"></div>
            </div>
            <div  id="reg_telephone_field" className="hide">
             <div className="col-sm-12 nopadding">
            <input type="text" name="reg_telephone" className="password_box" placeholder={this.props.language.entry_telephone} alt={this.props.language.entry_telephone} id="reg_telephone_signup" maxLength="10" onChange={this.handleChange.bind(this, "reg_telephone")} />
             <span className="error">{this.state.errors["reg_telephone"]}</span>
            </div>
            <div className="clearfix"></div>
            </div>
            <div className="col-sm-12 nopadding">
            <input type="text" name="password" className="password_box" placeholder={this.props.language.entry_password} alt={this.props.language.entry_password} id="register_password" autoComplete="new-password" onChange={this.handleChange.bind(this, "password")} />
            <span className="error">{this.state.errors["password"]}</span>
            </div>
            <div className="show-password">
            <a href="javascript:;" className="show_password" onClick={show_password}>Hide Password</a>
            </div>
            <div className="clearfix"></div>
           <div className="col-sm-12 nopadding">
           <input type="text" name="name" className="password_box capitalize" placeholder={this.props.language.entry_name}  alt={this.props.language.entry_name} onChange={this.handleChange.bind(this, "name")} />
           <span className="error">{this.state.errors["name"]}</span>
            </div>
            <div className="clearfix"></div>
            {this.props.international_store ?
            <div className="col-sm-12 nopadding">
            <input type="text" name="company" className="password_box" placeholder={this.props.language.entry_company} alt={this.props.language.entry_company} onChange={this.handleChange.bind(this, "company")} /> 
            <span className="error">{this.state.errors["company"]}</span>
            </div>
            : <div className="gst_area"><div className="col-sm-6 nopadding">
            <input type="text" name="company" className="password_box" placeholder={this.props.language.entry_company} alt={this.props.language.entry_company} onChange={this.handleChange.bind(this, "company")} /> 
            <span className="error">{this.state.errors["company"]}</span>
            </div> 
             <div className="col-sm-6 nopadding">
             <div className="checkbox_information checkbox_information_full">
               <label>
                  <input type="checkbox" name="gst_uin_number" id="gst_uin_number" value="1" onChange={this.handleChange.bind(this, "gst_uin_number")} /> {this.props.language.entry_check_gst}
                  <div className="control__indicator"></div>
                </label>
            </div>
            </div>
             <div className="col-sm-12 nopadding hide" id="gst_uin_number_area">
             <input type="text" name="gst_number" id="gst_number" className="password_box uppercase" placeholder={this.props.language.entry_gst_number} alt={this.props.language.entry_gst_number} onChange={this.handleChange.bind(this, "gst_number")} />
              <span className="error">{this.state.errors["gst_number"]}</span>
             </div>
            </div>
             }
            <div className="clearfix"></div>
             <div className="clearfix"></div>
           <div className="col-sm-6 nopadding">
            <input type="text" name="postcode" onChange={this.handleChange.bind(this, "postcode")} className="password_box width92" placeholder={this.props.language.entry_postcode} alt={this.props.language.entry_postcode} id="postcode" />
            <span className="error">{this.state.errors["postcode"]}</span>
           </div>
           <div className="col-sm-6 nopadding">
            <select name="country_id" className="password_box width92" id="country_id" onChange={this.handleChange.bind(this, "country_id")}>
            <option value="0">{this.props.language.entry_country}</option> 
            </select>
            <span className="error">{this.state.errors["country_id"]}</span>
            </div>
            <div className="clearfix"></div>
            <div className="col-sm-6 nopadding">
            <select name="zone_id" className="password_box width92" id="zone_id">
            <option value="0">{this.props.language.entry_zone}</option>
            </select>
            <span className="error">{this.state.errors["zone_id"]}</span>
            </div>
            <div className="col-sm-6 nopadding">
            <input type="text" name="city" className="password_box width92" placeholder={this.props.language.entry_city} id="city" />
            <span className="error">{this.state.errors["city"]}</span> 
            </div>
            <div className="clearfix"></div>
            <div className="col-sm-12 nopadding">
            <textarea name="address_1" className="password_box" placeholder={this.props.language.entry_address} alt={this.props.language.entry_address} onChange={this.handleChange.bind(this, "address_1")}></textarea>
            <span className="error">{this.state.errors["address_1"]}</span> 
            </div>
            <div className="clearfix"></div>
            <input type="submit" name="register" value="Continue" className="btn deliver_btn pull-right" id="register" />
            <div className="clearfix"></div>
                </div>
            </form>
                <div className="clearfix"></div>
          </div>
            </div>
          </div>
        </div>
		)
	}
}
