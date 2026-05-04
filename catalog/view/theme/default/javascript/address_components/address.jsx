class Address extends React.Component {
	
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
		    address_data:false,
		    success_msg:false,
		    error_msg:false,
		    country_list:false
       	}

       this.getAddressData       = this.getAddressData.bind(this);
       this.getCountryList       = this.getCountryList.bind(this);
       this.addressPopup         = this.addressPopup.bind(this);
       this.handleChange         = this.handleChange.bind(this);
       this.get_state_list       = this.get_state_list.bind(this);
       this.handleFormSubmit     = this.handleFormSubmit.bind(this);
       this.handleValidation     = this.handleValidation.bind(this);
       this.deleteAddress        = this.deleteAddress.bind(this);
       this.edit_address_popup_open = this.edit_address_popup_open.bind(this);
	}

	componentDidMount()
	{
		var self = this;
		$('body').addClass('my_account_body'); 
		this.setState({ loading:true, 
						something_went_wrong:false});
		
    this.getAddressData();
		this.getCountryList();

    $('#address_telephone').tagsinput({
           confirmKeys: [13, 44, 32]
        });

     $(document).delegate('#address_telephone', 'change', function(e)
     {
       var fields = self.state.fields;
       fields['address_telephone'] = $(this).val(); 
       self.setState({fields});
       if($(this).val() != '')
        {
         self.handleValidation();
        }

     });
         
	}


	getAddressData()
	{
      var self = this;
	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/address/getAddresses&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

      if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }

			this.setState({ address_data: response.data.data,
							loading:false,
						     something_went_wrong:false });
		});  
	}

	deleteAddress(address_id=0)
	{
		var self = this;
      var filters = 'address_id='+address_id;
        filters = filters+'&customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/address/user_address_delete&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

       if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }

			 self.getAddressData();
		});  

	}

	edit_address_popup_open(address, customer_address_id, submit=false)
   {  
     let self              = this;
     let fields            = this.state.fields;
     let address_telephone = this.state.address_telephone;
     fields['name']       = address.firstname+' '+address.lastname;
     fields['address_1']  = address.address_1;
     fields['postcode']   = address.postcode;
     fields['country_id'] = address.country_id;
     fields['zone_id']    = address.zone_id;
     fields['city']       = address.city;
     address_telephone    = address.address_telephone;

     this.setState({fields});
     this.setState({address_telephone});
    
      $(".modal-title").html("Update Address");
      $("#add_address_form input[name=address_id]").val(address.address_id);
      $("#add_address_form input[name=name]").val(address.firstname+' '+address.lastname);
      $("#add_address_form input[name=company]").val(address.company);
      $("#add_address_form input[name=address_1]").val(address.address_1);
      $("#add_address_form input[name=address_2]").val(address.address_2);
      $("#add_address_form input[name=postcode]").val(address.postcode);
      $("#country_id option[value=" + address.country_id + "]").prop("selected",true);
       self.get_state_list(address.zone_id);
      $("#add_address_form input[name=city]").val(address.city);

      if(customer_address_id == address.address_id)
      {
         $("#default_yes").prop("checked", true);
      }
      else
      {
         $("#default_no").prop('checked', true);
      }
     

      $(".account_address_error").hide(); 
      $(".account_address_success").hide();

      $('#address_telephone').tagsinput('removeAll');
      $('#address_telephone').tagsinput('add', address.address_telephone.join(","));

     if(submit)
     {
       $("#default_yes").prop('checked', true);
       $("#defaultbox_"+address.address_id).html('<i class="fa fa-circle-o-notch fa-spin"></i>');	
       setTimeout(function(){ self.handleFormSubmit(); }, 300);
     }
     else
     {
       $("#address_popup").modal("show");	
     }
     
}

	getCountryList()
	{
	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/login/country_list&data='+encode,
			dataType:'json'
		}) 
		.then(response => {
			this.setState({ country_list: response.data.data});
		});  
	}



  handleFormSubmit()
  {
  	var self = this;
    if(this.handleValidation())
    { 
      $("#address_btn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
       var address_id  = $("#add_address_form input[name=address_id]").val();
       var formData    = $('#add_address_form').serialize();

       formData = formData+'&customer_id='+getCookie('customer_id');
       formData = formData+'&customer_access_token='+getCookie('customer_access_token');

       if(address_id && address_id > 0)
       {
         var actionurl = './api/address/user_address_book_update';
       }
       else
       {
         var actionurl = './api/address/user_address_book_add';
       }

            $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: formData,
                success: function(response)
                {
                   if(response.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

                   $("#address_btn").html('Save');
                   if(response.statusCode == 200)
                   {
                     self.getAddressData();
                     $(".account_address_success").html(response.message);
                     $(".account_address_success").show();
                     $(".account_address_error").hide();
                     setTimeout(function(){ $("#address_popup").modal("hide"); }, 1000);
                   }
                   else
                   {
                     $(".account_address_error").html(response.message);
                     $(".account_address_error").show(); 
                     $(".account_address_success").hide();
                   }
                  
                }
            }); 
       
    }  
  }

    get_state_list(zone_id=0) 
    {
        let country_id = $("#country_id").val();
    
        $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
       
       axios({
			method:'GET',
			url:'./api/login/state_list&country_id='+country_id,
			dataType:'json'
		}) 
		.then(response => {
			  var data = response.data.data;
			  $("i.fa-circle-o-notch").remove();
              var result = '<option value="">'+data['zone_title']+'</option>';
              for(var i=0; i<data['zone_data'].length; i++)
                {
                  result = result+'<option value="'+data['zone_data'][i].zone_id+'">'+data['zone_data'][i].name+'</option>';
                }
               $("#zone_id").html(result);
               if(zone_id > 0)
               {
               	 $("#zone_id option[value=" + zone_id + "]").prop("selected",true);
               }
		});
    }

  postcode() 
   {
      var self = this;
      var value = $("#postcode").val();

      let fields = this.state.fields;
        fields['postcode'] = value;
        this.setState({fields});
        this.handleValidation();

      if (value && /^\d{6,}$/.test(value.trim()) && value.length === 6)
      {

      	    axios({
			method:'GET',
			url:'./api/address/autoPopulateAddress&pincode='+value,
			dataType:'json'
	     	}) 
		   .then(response => {

		      var data = response.data;
              if(!data['error'])
                  {
                    if(data['zone_id'] === '')
                    {
                      $("#country_id option[value='99'").prop('selected',true);
                      self.get_state_list();
                      $("#city").val('');
                      //$("#city").prev('label').css('margin-top', '20px');
                      fields['city']       = '';
                      fields['zone_id']    = '';
                      fields['country_id'] = 99;
                      self.setState({fields});
                      self.handleValidation();
                    }
                    else
                    {
                     // $("#city").prev('label').css('margin-top', '0px');
                      $("#city").val(data['city']);
                      $("#country_id option[value=" + data['country_id'] + "]").prop("selected",true);
                      self.get_state_list(data['zone_id']);
                      fields['city']       = data['city'];
                      fields['zone_id']    = data['zone_id'];
                      fields['country_id'] = data['country_id'];
                      self.setState({fields});
                      self.handleValidation();
                    }
                  }
            });
      }
  }

   addressPopup()
   {
   	  $(".modal-title").html("Add New Address");
      $("#add_address_form input").val('');
      $("#zone_id option").prop("selected",false);
      $("#default_yes").attr('checked', false);
      $("#default_no").attr('checked', true);
     // $("form#add_address_form :input").prev('label').css('margin-top', '20px');
      $(".account_address_error").hide(); 
      $(".account_address_success").hide();
      $('#address_telephone').tagsinput('removeAll');	
      $("#address_popup").modal("show");
   }


    handleValidation(){
        var self = this;
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;
        //Name
        if(!fields["name"]){
           formIsValid = false;
           errors["name"] = 'Please provide your name';
        }
        if(typeof fields["name"] !== "undefined"){
             if(!fields["name"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["name"] = "Only letters";
             }
             if(fields["name"].length < 2 || fields["name"].length > 64){
                 formIsValid = false;
                 errors["name"] = 'Name must be between 2 and 64 characters!';
             }          
        } 
   
        //address
        if(!fields["address_1"]){
           formIsValid = false;
           errors["address_1"] = 'Address must be between 3 and 128 characters!';
        }  
        if(typeof fields["address_1"] !== "undefined"){
             if(fields["address_1"].length < 3 || fields["address_1"].length > 128){
                 formIsValid = false;
                 errors["address_1"] = 'Address must be between 3 and 128 characters!';
             }          
        }

         //address telephone
         if(typeof fields["address_telephone"] !== "undefined")
         {
           var address_telephone_arr = fields["address_telephone"].split(",");
            address_telephone_arr.forEach(function(element) { 
            if(!self.state.international_store && element.length != 0 )
            {
              if(isNaN(element))
              {
                errors["address_telephone"] = "Contact no should be numeric!";
                formIsValid = false;
              }
              else if(element.length > 15){
                errors["address_telephone"] = "Contact no should be less than 15 digits!";
                formIsValid = true;
              }
            }
          });          
        } 
        //postcode
        if(!fields["postcode"]){
           formIsValid = false;
           errors["postcode"] = 'Please enter postcode!';
        } 
        if(typeof fields["postcode"] !== "undefined"){
             if(fields["postcode"].length < 2 || fields["postcode"].length > 10){
                 formIsValid = false;
                 errors["postcode"] = 'Invalid postcode!';
             }          
        } 

        //country
        if(!fields["country_id"]){
           formIsValid = false;
           errors["country_id"] = 'Please select a country!';
        }  
        if(typeof fields["country_id"] !== "undefined"){
             if(fields["country_id"] < 1)
             {
                 formIsValid = false;
                 errors["address_1"] = 'Please select a country!';
             }          
        }

        //zone
        if(!fields["zone_id"]){
           formIsValid = false;
           errors["zone_id"] = 'Please select a region / state!';
        }  
        if(typeof fields["zone_id"] !== "undefined"){
             if(fields["zone_id"] < 1)
             {
                 formIsValid = false;
                 errors["zone_id"] = 'Please select a region / state!';
             }          
        }

        //city
        if(!fields["city"]){
           formIsValid = false;
           errors["city"] = 'City must be between 2 and 128 characters!';
        }  
        if(typeof fields["city"] !== "undefined"){
             if(fields["city"].length < 2 || fields["city"].length > 128)
             {
                 formIsValid = false;
                 errors["city"] = 'City must be between 2 and 128 characters!';
             }          
        } 

       this.setState({errors: errors});
       return formIsValid;
   }

   handleChange(field, event) 
   {
    let fields = this.state.fields;
     if(event.target.name === 'postcode') { this.postcode(); }
     else { fields[field] = event.target.value; } 
     this.setState({fields});
     this.handleValidation();
  
   }

	render(){
    let self = this;

		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						   <div className="order_list_block">
			             	<div className="col-sm-3 customer_account_section">
		     	          	<LeftSection 
		     	          	   active="profile"
		     	          	   sub_active="Manage Address"
		     				   language={this.props.language}
		     				    />
			             	</div>
			            	<div className="col-sm-9 my_account_page">
			   		         
			   		      <RightSection 
		     				    language={this.props.language}
		     				    loading={this.state.loading}
		     				    address_data={this.state.address_data}
		     				    addressPopup={this.addressPopup}
		     				    deleteAddress={this.deleteAddress}
		     				    edit_address_popup_open={this.edit_address_popup_open}
		     				   />
			            	</div>
			              </div>

            <div className="modal fade add_new_address" id="address_popup" role="dialog">
            <div className="modal-dialog">
                  <div className="modal-content">
                   <div className="modal-header address_popup_head">
                   <button type="button" className="close" data-dismiss="modal">&times;</button>
                    <h4 className="modal-title">Add New Address</h4>
                  </div>

                 <div className="modal-body">
                  <div className="success_msg account_address_success" style={{display:'none'}}></div>
                  <div className="danger_msg account_address_error" style={{display:'none'}}></div>
                   
                <form id="add_address_form">
                 <input id="address_id" type="hidden"  name="address_id" />

                <div className="delivery_box">  

                 <div className="row">

                 <div className="col-sm-6">                    
                 <div className="new_add_popup_input_box"> 
                 <input id="name" type="text" className="account_input" placeholder="Enter your Name" alt="Name" name="name" onChange={this.handleChange.bind(this, "name")} />
                   <span className="error">{this.state.errors["name"]}</span> 
                 </div>
                 </div>
                 
                 <div className="col-sm-6"> 
                 <div className="new_add_popup_input_box"> 
                 <input id="company" type="text" className="account_input" placeholder="Enter your Business Name" alt="Business Name"  name="company" onChange={this.handleChange.bind(this, "company")} />
                  <span className="error">{this.state.errors["company"]}</span> 
                 </div>
                 </div>

                  <div className="clearfix"></div>

                <div className="col-sm-12"> 
                 <div className="new_add_popup_input_box"> 
                    <input id="address_1" type="text" className="account_input" placeholder="Enter your Address" alt="Address"  name="address_1" onChange={this.handleChange.bind(this, "address_1")} />
                   <span className="error">{this.state.errors["address_1"]}</span> 
                 </div>
                </div>

                 <div className="clearfix"></div>

                <div className="col-sm-12"> 
                <div className="new_add_popup_input_box"> 
                <input id="address_2" type="text" className="account_input" placeholder="Enter your Address2" alt="Address"  name="address_2" onChange={this.handleChange.bind(this, "address_2")} />
                <span className="error">{this.state.errors["address_2"]}</span> 
                </div>
                </div>

                 <div className="clearfix"></div>

                <div className="col-sm-12"> 
                <div className="new_add_popup_input_box"> 
                 <input id="address_telephone" type="text" className="account_input" placeholder="Mobile Number"  name="address_telephone" onChange={this.handleChange.bind(this, "address_telephone")} data-role="tagsinput" />
                  <span className="error">{this.state.errors["address_telephone"]}</span>
                </div>
                </div>

                 <div className="clearfix"></div>

              <div className="col-sm-6"> 
              <div className="new_add_popup_input_box"> 
                <input id="postcode" type="text" className="account_input" placeholder="Enter your Post Code" alt="Post Code"  name="postcode" onChange={this.handleChange.bind(this, "postcode")} />
                <span className="error">{this.state.errors["postcode"]}</span>
              </div>
              </div>

              <div className="col-sm-6"> 
              <div className="new_add_popup_input_box"> 
                <select name="country_id" id="country_id" className="account_input" onChange={this.get_state_list}>
                  <option value=""> --- Please Select Country --- </option>
                   {
                      this.state.country_list ?
                      this.state.country_list.country_data.map((country, index) => {
                        return (<option key={index} value={country.country_id}>{country.name}</option>)
                        })
                        : ''
                    }
                 </select>
                 <span className="error">{this.state.errors["country_id"]}</span>
              </div>
              </div>

               <div className="clearfix"></div>

             <div className="col-sm-6"> 
              <div className="new_add_popup_input_box"> 
                <select name="zone_id" id="zone_id" className="account_input" onChange={this.handleChange.bind(this, "zone_id")}>
                  <option value="">--- Please Select Region / State ---</option>
                </select>
                 <span className="error">{this.state.errors["zone_id"]}</span>
              </div>
            </div>  

            <div className="col-sm-6"> 
              <div className="new_add_popup_input_box"> 
                <input id="city" type="text" className="account_input" placeholder="Enter your City Name" alt="City Name" name="city" onChange={this.handleChange.bind(this, "city")} />
                <span className="error">{this.state.errors["city"]}</span>
              </div>
            </div>  

             <div className="clearfix"></div>

             <div className="col-sm-6"> 
             <label className="col-xs-12 control-label nopadding" style={{fontSize:'14px', marginTop: '15px', marginLeft: '10px'}}>Default Address</label>
             <div className="col-xs-12 nopadding">
             <div className="new_add_popup_input_box" style={{marginLeft: '10px', padding: '0px'}}> 
                <input style={{width:'12px', margin:'0px'}} type="radio" name="default" value="1" className="address_radio" id="default_yes" /> Yes
                <input style={{width:'12px', margin:'0px', marginLeft:'20px'}} type="radio" name="default" value="0" className="address_radio" id="default_no" /> No
             </div>
             </div>
             </div> 

             <div className="col-sm-6" style={{marginTop:'25px'}}> 
             <button type="button" onClick={this.handleFormSubmit} id="address_btn" className="btn verify_btn pull-right" data-direction='right'>Save</button> 
              </div> 
             </div>  

                 <div className="clearfix"></div>

             </div>
            </form>

               </div>
              </div>
             </div>
            </div>

			</section>
		  </div>
		</div>
	)
 }
}