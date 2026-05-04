class PasswordPopup extends React.Component {
	
	constructor(props)
	{
		super(props);
		 this.state = {
		 	fields: {},
      errors: {},
	    language: this.props.language,
	    international_store: this.props.international_store
      }

    this.change_password = this.change_password.bind(this);

	}
  
  change_password()
  {
    $("#Submit_password").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
    var self = this;
          var actionurl = './api/account/user_password_update';
          var form_data = $("#password_form").serialize();
          form_data = form_data+'&customer_access_token='+getCookie('customer_access_token');
          form_data = form_data+'&customer_id='+getCookie('customer_id');
          
          var ajax = $.ajax({
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
                    
                   $("#Submit_password").html('Continue'); 
                   if(response.statusCode == 200)
                   {
                    $("#password_success").html('<i class="fa fa-exclamation-circle"></i> '+response.message);
                    $("#password_success").show();
                    $("#password_error").hide();
                    setTimeout(function(){ $("#password_popup").modal("hide"); }, 1000);
                   }
                   else
                   {
                     $("#password_error").html('<i class="fa fa-exclamation-circle"></i> '+response.message);
                     $("#password_error").show();
                     $("#password_success").hide();
                  }
                  
                }
            });    
  } 

	render(){

		return (
			     <div className="modal fade add_new_address" id="password_popup" role="dialog">
            <div className="modal-dialog login_register_popup">
             <div className="modal-content">

                   <div className="modal-header address_popup_head">
                     <button type="button" className="close" data-dismiss="modal">&times;</button>
                     <h4 className="modal-title" id="send_otp_title">Password Change</h4>
                    </div>

                
                 <div className="modal-body">
                  <div id="password_success" className="alert alert-success" style={{display:'none'}}></div>
                  <div id="password_error" className="alert alert-danger" style={{display:'none'}}></div>

                  <div className="otp_model">
                   <form name="password_form" id="password_form">
                    <input type="password" name="password" className="mobile_input" placeholder="password" id="password" /> <br />
                    <input type="password" name="confirm_password" className="mobile_input" placeholder="Confirm Password" id="confirm_password" /> 
                    <input type="button" name="Submit_password" id="Submit_password" value="Continue" className="btn deliver_btn pull-right" onClick={this.change_password} />
                     <div className="clearfix"></div>
                  </form>
                </div>
               </div>

              </div>
             </div>
         </div>)
 }
}