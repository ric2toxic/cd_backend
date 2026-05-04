class Bankdetails extends React.Component {
	
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
		    bank_data:false,
		    success_msg:false,
		    error_msg:false,
        otp_api:false
       	}

       	 this.getBankData      = this.getBankData.bind(this);
       	 this.handleChange     = this.handleChange.bind(this);
       	 this.handleValidation = this.handleValidation.bind(this);
       	 this.formSubmit       = this.formSubmit.bind(this);
       	 this.send_otp         = this.send_otp.bind(this);
         this.bankPopup        = this.bankPopup.bind(this);
	}

	componentDidMount()
	{
		this.getBankData();
      //$('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();
         
	}

	getBankData()
	{
    var self = this;
		var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/bank/user_bank_detail&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

      if(response.data.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

			this.setState({ bank_data: response.data.data,
							loading:false,
						     something_went_wrong:false });
		});
	}

	handleChange(field, e)
	{  
        let fields = this.state.bank_data;
        fields[field] = e.target.value;  
        this.setState({fields});
        this.handleValidation();
    }

     handleValidation()
    {
        let fields = this.state.bank_data;
        let errors = {};
        let formIsValid = true;
        
        if(!fields["bank_ac_holder_name"]){
           formIsValid = false;
           errors["bank_ac_holder_name"] = "Please enter Account Holder Name";
        }

        if(typeof fields["bank_ac_holder_name"] !== "undefined"){
             if(!fields["bank_ac_holder_name"].match(/^[a-z A-Z.]+$/)){
                 formIsValid = false;
                 errors["bank_ac_holder_name"] = "Only letters";
             }
             if(fields["bank_ac_holder_name"].length < 2 || fields["bank_ac_holder_name"].length > 64){
                 formIsValid = false;
                 errors["bank_ac_holder_name"] = "Name must be between 1 and 32 characters!";
             }          
        }

       
        if(!fields["bank_ac_number"]){
           formIsValid = false;
           errors["bank_ac_number"] = "Please enter Account number";
        }

        if(typeof fields["bank_ac_number"] !== "undefined"){
             if(fields["bank_ac_number"].length < 2 || fields["bank_ac_number"].length > 64){
                 formIsValid = false;
                 errors["bank_ac_number"] = "Account number must be between 1 and 32 characters!";
             }          
        }

        if(!fields["ifsc_code"]){
           formIsValid = false;
           errors["ifsc_code"] = "Please enter IFSC Code";
        }

        if(typeof fields["ifsc_code"] !== "undefined"){
             if(fields["ifsc_code"].length < 2 || fields["bank_ac_number"].length > 64){
                 formIsValid = false;
                 errors["ifsc_code"] = "Code must be between 1 and 32 characters!";
             }          
        }

       this.setState({errors: errors});
       return formIsValid;
   }

   formSubmit()
   {
     
		var self = this;
        if(this.handleValidation()){
          
        if(this.state.otp_api)
        {
          self.setState({otp_api: false});
          $("#submit_btn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
          var actionurl = './api/bank/user_bank_detail_update';
          var form_data = $("#bank_detail_form").serialize();

          form_data = form_data+'&customer_access_token='+getCookie('customer_access_token');
          form_data = form_data+'&customer_id='+getCookie('customer_id');
          
              $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response)
                {
                   if(response.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }
                 
                   $("#submit_btn").html('Submit');	
                   $("#otp").val('');
                   //$("#otp").prev('label').css('margin-top', '20px'); 
                   if(response.statusCode == 200)
                   {
                     $(".account_bank_success").html(response.message);
                     $(".account_bank_error").hide();
                     $(".account_bank_success").show();
                     setTimeout(function(){ $("#bank_popup").modal("hide"); }, 1000);
                   }
                   else
                   {
                     $(".account_bank_error").html(response.message);
                     $(".account_bank_success").hide();
                     $(".account_bank_error").show();
                   }
                  
                }
            }); 
          }
          else
          {
            this.send_otp();
          }     
        }	
   }

   send_otp()
   {
        $("#submit_btn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        $("#otp").val('');
        //$("#otp").prev('label').css('margin-top', '20px'); 
		    var self = this;
        var actionurl = './api/bank/update_bank_otp';
        var form_data = '';
        form_data = form_data+'&customer_access_token='+getCookie('customer_access_token');
        form_data = form_data+'&customer_id='+getCookie('customer_id');
          
              $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(response)
                {
                   if(response.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }
                    $("#submit_btn").html('Submit');

                   if(response.statusCode == 200)
                   {
                     self.setState({otp_api: true});
                     $(".account_bank_success").html(response.data.msg);
                     $(".account_bank_error").hide();
                     $(".account_bank_success").show();
                   }
                   else
                   {
                     $(".account_bank_error").html(response.data.msg);
                     $(".account_bank_success").hide();
                     $(".account_bank_error").show();
                   }
                  
                }
            });    
       
   }

   bankPopup()
   {
     $("#bank_popup").modal("show");

     $(".account_bank_success").hide();
     $(".account_bank_error").hide();

    /* $("form#bank_detail_form :input").each(function(){
        if($(this).val() != '')
        {
          $(this).prev('label').css('margin-top', '0px'); 
        }
        else
        {
          $(this).prev('label').css('margin-top', '20px');    
        }
      });*/
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
		     	          	   sub_active="Saved Bank Details"
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
		     				    bank_data={this.state.bank_data}
		     				    bankPopup={this.bankPopup}
		     				   />
			            	</div>
			              </div> 


           <div className="modal fade add_new_address" id="bank_popup" role="dialog">
            <div className="modal-dialog">
                  <div className="modal-content">
                   <div className="modal-header address_popup_head">
                   <button type="button" className="close" data-dismiss="modal">&times;</button>
                    <h4 className="modal-title">Edit Bank Details</h4>
                  </div>

                 <div className="modal-body">
                  <div className="success_msg account_bank_success" style={{display:'none'}}></div>
                  <div className="danger_msg account_bank_error" style={{display:'none'}}></div>
                  <form className="form-horizontal" id="bank_detail_form">
                   <div className="row">
                    <div className="col-sm-12">
                      <input type="text" name="bank_ac_holder_name" id="bank_ac_holder_name" className="account_input" placeholder="Account Holder Name" value={this.state.bank_data.bank_ac_holder_name} onChange={this.handleChange.bind(this, "bank_ac_holder_name")} />
                      <span className="error">{this.state.errors["bank_ac_holder_name"]}</span>
                    </div>
                 </div>

                 <div className="row">
                    <div className="col-sm-12">
                      <input type="text" name="bank_ac_number" id="bank_ac_number" className="account_input" placeholder="Account Number" value={this.state.bank_data.bank_ac_number} onChange={this.handleChange.bind(this, "bank_ac_number")} />
                      <span className="error">{this.state.errors["bank_ac_number"]}</span>
                    </div>
                 </div>


                 <div className="row">
                    <div className="col-sm-12">
                      <input type="text" name="ifsc_code" id="ifsc_code" className="account_input" placeholder="IFSC Code" value={this.state.bank_data.ifsc_code} onChange={this.handleChange.bind(this, "ifsc_code")} />
                      <span className="error">{this.state.errors["ifsc_code"]}</span>
                    </div>
                 </div>

                 <div className="row">
                    <div className="col-sm-12">
                      <input type="text" name="customer_vpa" id="customer_vpa" className="account_input" placeholder="UPI Vpa" value={this.state.bank_data.customer_vpa} onChange={this.handleChange.bind(this, "customer_vpa")} />
                      <span className="error">{this.state.errors["customer_vpa"]}</span>
                    </div>
                 </div>
                {this.state.otp_api ?
                 <div className="row">
                    <div className="col-sm-12">
                      <input type="text" name="otp" id="otp" className="account_input" placeholder="OTP" onChange={this.handleChange.bind(this, "otp")} />
                       <br />
                      <a href="javascript:;" onClick={this.send_otp}>Didn't get OTP?</a>
                    </div>
                 </div>
                 : ''}

                 <div className="row">
                    <div className="col-sm-12">
                     <button className="verify_btn pull-right" id="submit_btn" type="button" onClick={this.formSubmit}>Submit</button>
                    </div>
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