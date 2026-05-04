import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import $ from 'jquery'
import Helmet from 'react-helmet';
import  './index.css'

import { contactusData, contactus_formData } from '../../../actions/InformationAction';

class Contact  extends Component {

   constructor(props)
   {
     super(props);

       this.state = {
           fields: {},
           errors: {}
       }
   } 

    componentDidMount()
  {   
     this.props.dispatch(contactusData());
  } 

   contactSubmit(e){
        e.preventDefault();
        if(this.handleValidation()){
          this.contact_form();
        }
    }

    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        this.setState({fields});  
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

        //Email
         if(!fields["email"]){
           formIsValid = false;
           errors["email"] = 'E-Mail address does not appear to be valid!';
        }

        if(typeof fields["email"] !== "undefined"){
            let lastAtPos = fields["email"].lastIndexOf('@');
            let lastDotPos = fields["email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["email"].indexOf('@@') === -1 && lastDotPos > 2 && (fields["email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["email"] = 'E-Mail address does not appear to be valid!';
            }
       }

        //Telephone
        if(!fields["telephone"]){
           formIsValid = false;
           errors["telephone"] = 'Mobile must have 10 numbers!';
        }   

        if(typeof fields["telephone"] !== "undefined"){
             if(this.props.international_store === 0 && fields["telephone"].length < 10){
                 formIsValid = false;
                 errors["telephone"] = 'Mobile  does not appear to be valid!';
             } 
             if(this.props.international_store === 1 && fields["telephone"].length < 6){
                 formIsValid = false;
                 errors["telephone"] = 'Mobile  does not appear to be valid!';
             }         
        }
  
        //address
        if(!fields["enquiry"]){
           formIsValid = false;
           errors["enquiry"] = 'Enquiry must be between 5 and 128 characters!';
        }  
        if(typeof fields["enquiry"] !== "undefined"){
             if(fields["enquiry"].length < 5 || fields["enquiry"].length > 128){
                 formIsValid = false;
                 errors["enquiry"] = 'Address must be between 5 and 128 characters!';
             }          
        }  

       this.setState({errors: errors});
       return formIsValid;
   }

  handleChange(field, e)
     {  
        let fields = this.state.fields;  
        fields[field] = e.target.value;   
        this.setState({fields});
        this.handleValidation();
    }

   contact_form() {
        var form_data = $("#contact_form").serialize();
        var response =this.props.dispatch(contactus_formData(form_data));
        response.then(function(data){
                    if(data.error)
                    {
                      $(".contact_success").hide();
                      $(".contact_error").html(data.msg);
                      $(".contact_error").show();
                    }
                    else
                    {
                      $(".contact_error").hide();
                      $(".contact_success").html(data.msg);
                      $(".contact_success").show();
                      $("#contact_form input").val('');
                      $("#contact_form textarea").val('');
                    }
         })
        .catch(function() {
          console.log('data fetch error');
         });
    }

 render(){
    return (
    <div className="contner head_margin side-collapse-container">
     <Helmet title="Contact Us" />
      <h2 className="about_title">Contact Us</h2>
        <section style={{marginTop:'10px'}}>
        <div className="col-xs-12">
        <h4 className="contact_we_text">We’d love to hear from you!</h4>
        <p>Got a question? Send us a message and we’ll respond as soon as possible.</p>
            
                 <div className="col-xs-12 contact_info">
                   <div className="col-xs-2 contact_info_icon"><i className="fa fa-tag" aria-hidden="true"></i></div>
                    <div className="col-xs-10 contact_info_text">
                        <span className="sub_contact_title">Sales</span>
                        <span className="contact_info_id">saleswholesalebox.in</span>
                    </div>
               </div>
             
                 <div className="col-xs-12 contact_info">
                   <div className="col-xs-2 contact_info_icon"><i className="fa fa-life-ring" aria-hidden="true"></i></div>
                    <div className="col-xs-10 contact_info_text">
                        <span className="sub_contact_title">Support</span>
                        <span className="contact_info_id"><a href="mailto:support@wholesalebox.in"> support@wholesalebox.in</a></span>
                    </div>
                 </div>
                 <div className="col-xs-12 contact_info">
                   <div className="col-xs-2 contact_info_icon"><i className="fa fa-phone" aria-hidden="true"></i></div>
                    <div className="col-xs-10 contact_info_text">
                        <span className="sub_contact_title">Phone</span>
                        <span className="contact_info_id"><a href="tel:+91-141-4049163"> (+91) 141 - 4049163</a></span>
                    </div>
                 </div>
                 <div className="col-xs-12 contact_info">
                   <div className="col-xs-2 contact_info_icon"><i className="fa fa-info-circle" aria-hidden="true"></i></div>
                    <div className="col-xs-10 contact_info_text">
                        <span className="sub_contact_title">Info</span>
                        <span className="contact_info_id"><a href="mailto:info@wholesalebox.in"> info@wholesalebox.in</a></span>
                    </div>
                 </div> 
               <div className="clearfix"></div>
              <div className="col-sm-12 contact_head_office">

                 
              <div className="head_office_contact">
               <h3 className="contact_subtitle">Head Office</h3> 
                 <div className="col-xs-2 head_office_box"><i className="fa fa-map-marker" aria-hidden="true"></i></div>
               <div className="col-xs-10 head_office_text">
                 <h3>Jaipur</h3>    
                 <p> Wholesalebox Pvt. Ltd. B-1, Crystal Mall, Banipark,<br />
                Jaipur-302016 Rajasthan </p>
               </div>     
              </div>
              <div className="head_office_contact">
                 <div className="col-xs-2 head_office_box"><i className="fa fa-volume-control-phone" aria-hidden="true"></i></div>
               <div className="col-xs-10 head_office_text"> 
                 <p>(+91) 141-4049163, +91-8696491521</p>
               </div>     
              </div>
              <div className="clearfix"></div>
                
              </div> 

              <div className="clearfix"></div>

          </div>

         <div className="col-xs-12">
           <div className="contact_box">
            <div className="contact_card_logo"><i className="fa fa-handshake-o" aria-hidden="true"></i></div>
             <h4>Say Hello!</h4>
             <div className="success_msg contact_success"></div>
             <div className="danger_msg contact_error"></div>
              <form id="contact_form" method="post"  onSubmit={this.contactSubmit.bind(this)}>
                <div className="col-xs-12 contact_input_box"><input name="name" className="password_box" type="text" placeholder="Your Name*" alt="Name" onChange={this.handleChange.bind(this, "name")} /> <span className="error">{this.state.errors["name"]}</span></div>
                <div className="col-xs-12 contact_input_box"><input name="email"  className="password_box" type="E-mail" placeholder="Email Address*" alt="Email" onChange={this.handleChange.bind(this, "email")} /> <span className="error">{this.state.errors["email"]}</span></div>
                <div className="col-xs-12 contact_input_box has-error"><input name="telephone" className="password_box" type="text" placeholder="Your Phone*" alt="Phone*" onChange={this.handleChange.bind(this, "telephone")} /> <span className="error">{this.state.errors["telephone"]}</span></div>
                <div className="col-xs-12 contact_input_box"><input name="company" className="password_box" type="text" placeholder="Company Name" alt="Company" /></div>
                <div className="col-xs-12 contact_teaxtarea_box"><textarea name="enquiry" rows="2" className="password_box" placeholder="Enter your Address" alt="Address" onChange={this.handleChange.bind(this, "enquiry")}></textarea> <span className="error">{this.state.errors["enquiry"]}</span></div>
                <div className="clearfix"></div>
                <button type="submit" className="btn deliver_btn margin_none" data-dismiss="modal" data-direction="right">Submit</button>
              </form>
              <div className="clearfix"></div>
           </div>
         </div>
        <div className="clearfix"></div>
        <div className="col-xs-12 store_details">
           <h3 className="contact_subtitle">Our Store</h3> 

          <ul>
              <li>
                <div className="col-xs-12 store_details_box">
                   <div className="col-xs-2 head_office_box"><i className="fa fa-map-marker" aria-hidden="true"></i></div>
                 <div className="col-xs-10 nopadding">
                   <h3>Surat</h3> 
                   <p>A 2009/10, Millennium Textile Market,<br />
                   Ring Road, Surat-395002<br/>
                  Gujarat</p>
                 </div>     
                 <div className="clearfix"></div>
                 <div className="col-xs-2 head_office_box"><i className="fa fa-volume-control-phone" aria-hidden="true"></i></div>
                 <div className="col-xs-10 nopadding">  
                   <p>+91-8696491521</p>
                 </div>     
                </div>
              </li>           
          </ul>
        </div>
         </section>
         <div className="clearfix"></div>
   </div>
)}}

function mapStateToProps(state){
  return {
    contact_us: state.informationReducer.contact_us,
    international_store: state.headerReducer.userlogin.international_store,
    actions: bindActionCreators(contactusData, contactus_formData)
  };
}

export default connect(mapStateToProps)(Contact);