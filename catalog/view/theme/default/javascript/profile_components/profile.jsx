class Profile extends React.Component {
	
	constructor(props)
	{
		super(props);
		 this.state = {
		 	fields: {},
        errors: {},
	      language: this.props.language,
	      international_store: this.props.international_store,
		    loading: true,
		    something_went_wrong: false,
		    logout:this.props.logout,
		    profile_data:false,
		    success_msg:false,
		    error_msg:false,
        email:false,
        mobile:false
       	}

       this.getProfileData       = this.getProfileData.bind(this);
       this.handleValidation     = this.handleValidation.bind(this);
       this.handleChange         = this.handleChange.bind(this); 
       this.formSubmit           = this.formSubmit.bind(this);
       this.check_reg_telephone  = this.check_reg_telephone.bind(this);
       this.edit_register_no     = this.edit_register_no.bind(this);
       this.openPopup            = this.openPopup.bind(this);

	}

	componentDidMount()
	{
		var self = this;
		$('body').addClass('my_account_body'); 
		this.setState({ loading:true, 
						something_went_wrong:false});

		if(this.props.international_store == 1)
        {
         var cc = $.parseJSON('{"AE":"+971","AU":"+61","BD":"+880","CA":"+1","IN":"+91","MY":"+60","OM":"+968","SA":"+966","GB":"+44"}');
         $('#select_update_country').attr('data-selected-country','CA');
        }
        else
        {
          var cc = $.parseJSON('{"IN":"+91"}');
          $('#select_update_country').attr('data-selected-country','IN');
        }

        $('#select_update_country').flagStrap({
           countries:cc
         });

        $('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();

		$('#update_telephone').keyup(function(e)
        {
           self.check_reg_telephone();
        });

        $('#update_telephone').change(function(e)
         {
           self.check_reg_telephone();
         });  

		this.getProfileData();
         
	}


	getProfileData()
	{
      var self = this;
	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/account/user_detail&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

      if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }

			this.setState({ profile_data: response.data.data,
              email: response.data.data.email,
              mobile: response.data.data.telephone,
							loading:false,
						     something_went_wrong:false });
		});  
	}

	formSubmit(e)
	{ 
		var self = this;
	    e.preventDefault();
        if(this.handleValidation()){
          $("#submit_btn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
          var actionurl = './api/account/user_profile_update';
          var form_data = $("#edit_profile_form").serialize();

          form_data = form_data+'&customer_access_token='+getCookie('customer_access_token');
          form_data = form_data+'&customer_id='+getCookie('customer_id');
          
              $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response)
                {
                   $("#submit_btn").html('Submit');	

                   if(response.statusCode == 900){
                      window.location.href = self.state.logout;
                      return false;
                    }

                   if(response.statusCode == 200)
                   {
                     self.setState({ success_msg: response.message,
                                     error_msg: false,
                                     email: self.state.profile_data.email,
                                     mobile: self.state.profile_data.telephone});

                   }
                   else
                   {
                     self.setState({ error_msg: response.message,
                                     success_msg: false});
                   }
                  
                }
            });    
        }	
	}

	handleChange(field, e)
	{  
        let fields = this.state.profile_data;
        fields[field] = e.target.value;  
        this.setState({fields});
        this.handleValidation();
    }

    handleValidation()
    {
        let fields = this.state.profile_data;
        let errors = {};
        let formIsValid = true;
        //Name
        if(!fields["firstname"]){
           formIsValid = false;
           errors["firstname"] = "Please enter first name";
        }
        if(typeof fields["firstname"] !== "undefined"){
             if(!fields["firstname"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["firstname"] = "Only letters";
             }
             if(fields["firstname"].length < 2 || fields["firstname"].length > 64){
                 formIsValid = false;
                 errors["firstname"] = "First Name must be between 1 and 32 characters!";
             }          
        }

        //Last Name
        if(!fields["lastname"]){
           formIsValid = false;
           errors["lastname"] = "Please enter last name";
        }
        if(typeof fields["lastname"] !== "undefined"){
             if(!fields["lastname"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["lastname"] = "Only letters";
             }
             if(fields["lastname"].length < 2 || fields["lastname"].length > 64){
                 formIsValid = false;
                 errors["lastname"] = "Last Name must be between 1 and 32 characters!";
             }          
        } 

        //Gst Number
        if(typeof fields["gst_number"] !== "undefined" && fields["gst_number"].length  > 0) 
        {  
         var gst_number = fields["gst_number"];
         var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/;
          if (reggstin.test(gst_number) == false) {
            formIsValid = false;
            errors["gst_number"] = "Incorrect GST Number";
          }
        }
 

       this.setState({errors: errors});
       return formIsValid;
   }


   check_reg_telephone()
  {
    var mobile       = $('#update_telephone').val();
    var country_code = $('#country_code').val();
    var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    
    $(".flagstrap").hide();

    if(this.props.international_store == 1)
      {   
          if (mobile != '' && /\D/g.test(mobile))
                 {
                   $('#update_telephone').prev("label").html("Email");
                   $(".flagstrap").hide();
                 }

                 if (email_pattern.test(mobile))
                 {
                   $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                 }
                 else
                 {
                   $("#update_number_form input[type=submit]").val('continue').prop("disabled", true);
                 }

        }
        else
        {
                if(country_code == 'IN')
                {
                   var mobile_pattern = new RegExp(/^\d{10}$/);
                }
                else
                {
                   var mobile_pattern = new RegExp(/^\d{7,10}$/);
                } 

                if (mobile != '' && !/\D/g.test(mobile))
                 {
                   $('#update_telephone').prev("label").html("Mobile");
                   $(".flagstrap").show();
                 }

                if (mobile != '' && /\D/g.test(mobile))
                 {
                   $('#update_telephone').prev("label").html("Email");
                   $(".flagstrap").hide();
                 }

                if (email_pattern.test(mobile))
                 {
                   $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                 }
                else if (mobile_pattern.test(mobile))
                 {
                   if (mobile.charAt(0) != 0 && country_code == 'IN')
                    {
                       $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                    }
                   if (country_code != 'IN')
                    {
                      $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                    }

                  }
                 else
                  {
                     $("#update_number_form input[type=submit]").val('continue').prop("disabled", true);
                  }
            }
    }


   edit_register_no()
    {
        $("#edit_telephone").hide();
        $("#save_account_telephone, #send_otp_title").show();
        $('.account_mobile_success, .account_mobile_error').hide(500);
        $("#verify_otp_form :input[name=otp]").val('');
        $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
        $("#collapseverify").hide();
        this.check_reg_telephone();
    }

    openPopup(value, e)
    {
       $("#update_number_form :input[name=update_telephone]").val(value);
       $("#update_number_form :input[name=update_telephone]").prev('label').css('margin-top', '0px');
       this.edit_register_no(); 	 	
       $("#update_number_popup").modal("show");
    }

	render(){
    let self = this;
    $("#update_number_form").submit(function(e) {
         e.preventDefault();
         $("#update_number_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl         = './api/account/update_number_otp';
         var country_code_text = $("#country_code option:selected" ).text();
         var country_code_val  = $("#country_code" ).val();
         var customer_id = getCookie('customer_id');
         var customer_access_token = getCookie('customer_access_token');
         var form_data         = $("#update_number_form").serialize();
         
             form_data         = form_data.replace("country_code="+country_code_val, "country_code="+country_code_text+"&country_iso_code="+country_code_val+"&customer_id="+customer_id+"&customer_access_token="+customer_access_token);

            $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response) {
                  var data = response.data;
                  if(data['error'])
                    {
                      $("#update_number_form input[type=submit]").val('continue').prop("disabled", false);
                      $('.account_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                      $('.account_mobile_error').show();
                      $('.account_mobile_success').hide();
                      $("#collapseverify").css('visibility','hidden').hide();
                    }
                  else
                    {
                      $(".account_mobile_nmr").html(data['country_code']+' '+data['reg_telephone']);
                      $("#verify_otp_form input[name=reg_telephone]").val(data['reg_telephone']);
                      $("#verify_otp_form input[name=country_code]").val(data['country_code']);
                      $("#verify_otp_form input[name=country_iso_code]").val(data['country_iso_code']);
                      if(data['country_iso_code'] != '')
                           {
                             $(".country-flag").removeClass().addClass('country-flag flagstrap-icon flagstrap-'+data['country_iso_code'].toLowerCase());
                             $(".flag_area").show();
                           }
                           else
                           {
                              $(".flag_area").hide();
                           }
                      $('.account_mobile_error').hide();
                      $('.account_mobile_success').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                      $('.account_mobile_success').show();
                      $("#save_account_telephone, #send_otp_title").hide();
                      
                      $("#edit_telephone").show();
                      $("#collapseverify").css('visibility','visible').show();
                    }  
                }
         });
          e.stopImmediatePropagation();
          return false;
  });

       $("#verify_otp_form").submit(function(e) {
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);

         var customer_id = getCookie('customer_id');
         var customer_access_token = getCookie('customer_access_token');

         $("#verify_otp_form input[name=customer_id]").val(customer_id);
         $("#verify_otp_form input[name=customer_access_token]").val(customer_access_token);
         var actionurl = './api/account/verify_otp';
         var form_data = $("#verify_otp_form").serialize();
         $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response)
                {
                    var data = response.data;
                    if(data['error'])
                    {
                      $("#verify_otp_form :input[name=otp]").val('');
                      $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
                      $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
                      $('.account_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                      $('.account_mobile_error').show();
                      $('.account_mobile_success').hide();
                    }
                    else
                    {
                      $("#update_number_popup").modal("hide");
                      self.getProfileData();
                    }
                }
         });
         e.stopImmediatePropagation();
         return false;
       });


 $(document).delegate('.send_otp_agin', 'click', function(e){
           $('.account_mobile_success, .account_mobile_error').hide();
           $("#verify_otp_form input[name=otp]").val('');
           $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px');
           e.preventDefault();
           $('#update_number_form').submit();
 });

		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						   <div className="order_list_block">
			             	<div className="col-sm-3 customer_account_section">
		     	          	<LeftSection 
		     	          	   active="profile"
		     	          	   sub_active="Profile Information"
		     				   language={this.props.language}
		     				    />
			             	</div>
			            	<div className="col-sm-9 my_account_page">
			   		         
			   		         {this.state.success_msg ?
			   		         	<div className="alert alert-success"><i className="fa fa-exclamation-circle"></i> &nbsp; 
			   		         	 <span dangerouslySetInnerHTML={{ __html: this.state.success_msg }} />
			   		         	</div>
			   		         	: ''}

			   		         {this.state.error_msg ?
			   		         	<div className="alert alert-danger"><i className="fa fa-exclamation-circle"></i> &nbsp; 
			   		         	<span dangerouslySetInnerHTML={{ __html: this.state.error_msg }} />
			   		         	</div>
			   		         	: ''}
			   		          <RightSection 
		     				    language={this.props.language}
		     				    loading={this.state.loading}
		     				    profile_data={this.state.profile_data}
		     				    errors={this.state.errors}
		     				    handleChange={this.handleChange}
		     				    formSubmit={this.formSubmit}
		     				    international_store={this.props.international_store}
		     				    openPopup={this.openPopup}
                    email={this.state.email}
                    mobile={this.state.mobile}
		     				   />
			            	</div>
			              </div>

            <div className="modal fade add_new_address" id="update_number_popup" role="dialog">
            <div className="modal-dialog login_register_popup">
                  <div className="modal-content">
                   <div className="modal-header address_popup_head">
                   <button type="button" className="close" data-dismiss="modal">&times;</button>
                  <h4 className="modal-title" id="send_otp_title">Please enter your mobile number or Email address</h4>
                  </div>

                 <div className="modal-body">
                  <div className="success_msg account_mobile_success" style={{display:'none'}}></div>
                  <div className="danger_msg account_mobile_error" style={{display:'none'}}></div>
                  <div className="otp_model">
                   <form name="update_number_form" id="update_number_form">
                   <div className="mobile_details_panel" id="edit_telephone" style={{display:'none'}}>
                   <span className="flag_area"><i className="country-flag flagstrap-icon flagstrap-"></i></span>
                    <div className="account_mobile_nmr"> </div> &nbsp;
                     <a href="javascript:;" onClick={this.edit_register_no}><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                   </div>

                   <div className="mobile_details_panel" id="save_account_telephone">
                  <div className="flagstrap flag_box" id="select_update_country" data-input-id="country_code" data-input-name="country_code" data-selected-country="IN"></div>
                     <input type="text" name="update_telephone" className="mobile_input" placeholder="Mobile Number Or Email" alt="Mobile Number Or Email" id="update_telephone" autoComplete="off" autoFocus="autofocus" /> 
                    <input type="submit" name="send_otp" value="Continue" className="btn deliver_btn pull-right" id="send_otp" disabled="disabled" />
                     
                     <div className="clearfix"></div>
                   </div>
               </form>
            
              <form name="verify_otp_form"  id="verify_otp_form">                
              <div id="collapseverify" className="panel-collapse collapse">
                  <input type="text" name="otp" className="password_box" placeholder="Please Enter OTP" maxLength="6" alt="OTP" />                 
                   <input type="submit" name="verify_otp" value="Verify" className="btn deliver_btn pull-right" id="verify_otp" />               
                    <div className="clearfix"></div>
                  <div className="otp_agin"><a href="javascript:;" className="send_otp_agin">Didn't get OTP?</a></div>
              </div>
                
              <input type="hidden" name="reg_telephone" />            
              <input type="hidden" name="country_code" />            
              <input type="hidden" name="country_iso_code" />
              <input type="hidden" name="customer_id" />
              <input type="hidden" name="customer_access_token" />            
               </form>          
                </div>
               </div>
              </div>
             </div>
         </div>
             
             <PasswordPopup language={this.props.language} international_store={this.props.international_store} />
			</section>
		  </div>
		</div>
	)
 }
}