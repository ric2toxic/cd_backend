class RightSection extends React.Component {
	
	constructor(props){
		super(props);		
		this.state = {
      language: this.props.language,
		}

    this.passwordPopup = this.passwordPopup.bind(this);
	}

  passwordPopup()
  {
    $("#password_error, #password_success").hide();
    $("#password, #confirm").val('');
    $("#password, #confirm").prev('label').css('margin-top', '20px');
    $("#password_popup").modal("show");
  }
  

	render(){
    if(!this.props.loading)
    {
      return(<div className="right_section">
               <form className="form-horizontal" id="edit_profile_form">
                 <div className="row">
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-6 heading_title">Personal Information</div>
                     <div className="col-sm-4" style={{textAlign:'right', color:'#233c98', fontWeight:'bold', cursor:'pointer'}} onClick={this.passwordPopup}>Change Password</div>
                     
                     </div>
                    <div className="col-sm-4">
                      <input type="text" name="firstname" id="firstname" className="account_input" placeholder="First Name" value={this.props.profile_data.firstname} onChange={this.props.handleChange.bind(this, "firstname")} />
                      <span className="error">{this.props.errors["firstname"]}</span>
                    </div>

                    <div className="col-sm-4">
                    <input type="text" name="lastname" id="lastname" className="account_input" placeholder="Last Name" value={this.props.profile_data.lastname} onChange={this.props.handleChange.bind(this, "lastname")} />
                      <span className="error">{this.props.errors["lastname"]}</span>
                    </div>
                 </div>

                 <div className="row" style={{marginTop:'20px'}}>
                    <div className="col-sm-12 my_account_heading">
                      <div className="col-sm-12 heading_title">Email</div>
                     </div>
                    <div className="col-sm-6">

                      <input type="text" name="email" id="email" className={this.props.email ? 'account_input disable' : 'account_input'} placeholder="Email" value={this.props.profile_data.email} onChange={this.props.handleChange.bind(this, "email")} />

                    </div>
                   {this.props.international_store == 0 && this.props.email && this.props.email != '' ?
                    <div className="col-sm-6">
                       <button className="verify_btn" type="button" onClick={this.props.openPopup.bind(this, this.props.email)}>Edit</button>
                    </div>
                    : ''}

                 </div>


                 <div className="row" style={{marginTop:'20px'}}>
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-12 heading_title">Mobile</div>
                    </div>
                    <div className="col-sm-6">
                      <input type="text" name="telephone" id="telephone" className="account_input" placeholder="Mobile" value={this.props.profile_data.telephone} onChange={this.props.handleChange.bind(this, "telephone")} className={this.props.mobile ? 'account_input disable' : 'account_input'} />
                    </div>

                    {this.props.mobile && this.props.mobile != '' ?
                    <div className="col-sm-6">
                       <button className="verify_btn" type="button" onClick={this.props.openPopup.bind(this, this.props.mobile)}>Edit</button>
                    </div>
                    : ''}
                 </div>

                 <div className="row" style={{marginTop:'20px'}}>
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-12 heading_title">GST Number</div>
                    </div>
                    <div className="col-sm-6">
                      <input type="text" name="gst_number" id="gst_number" className={this.props.profile_data.gst_number_exist ? 'account_input disable' : 'account_input'} placeholder="GST Number" value={this.props.profile_data.gst_number} onChange={this.props.handleChange.bind(this, "gst_number")} />
                      <span className="error">{this.props.errors["gst_number"]}</span>
                    </div>
                 </div>

                 <div className="row" style={{marginTop:'20px'}}>
                    <div className="col-sm-10">
                     <button className="verify_btn pull-right" id="submit_btn" type="button" onClick={this.props.formSubmit}>Submit</button>
                    </div>
                 </div>

                </form>

              </div>
            )
    }
    else
    {
     return(<div className="right_section">
                 <OrderListLoadingRight />
            </div>
            ) 
    }
	}
}