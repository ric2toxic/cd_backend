import React, { Component } from 'react'
import $ from 'jquery'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { bindActionCreators } from 'redux'
import Api from '../../../api/Api'
import  './index.css'
import { postYourRequirementCategory, postYourRequirement} from '../../../actions/AccountAction'

class PostYourRequirement  extends Component {

  constructor(props) {
    super(props);

    this.state = {
          fields: {},
          errors: {}
     };

    this.handleChange     = this.handleChange.bind(this);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
    this.handleValidation        = this.handleValidation.bind(this);
  }

  componentDidMount()
  {  
      this.props.dispatch(postYourRequirementCategory());

        //called when key is pressed in textbox
        $(".only_number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                    return false;
            }
        });
  } 


    handleValidation(){
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        //Category
        if(!fields["category"]){
           formIsValid = false;
           errors["category"] = 'Please select a category';
        }
        //quantity
        if(!fields["quantity"]){
           formIsValid = false;
           errors["quantity"] = 'Please enter quantity!';
        }

        if(typeof fields["quantity"] !== "undefined")
        {
          if(isNaN(parseInt(fields["quantity"], 10)) || parseInt(fields["quantity"], 10) < 1)
          {
            formIsValid = false;
           errors["quantity"] = 'Please enter a valid quantity!';
          }
        }  

        //require_days
        if(!fields["require_days"]){
           formIsValid = false;
           errors["require_days"] = 'Please enter require days!';
        }

        if(typeof fields["require_days"] !== "undefined")
        {
          if(isNaN(parseInt(fields["require_days"], 10)) || parseInt(fields["require_days"], 10) < 1)
          {
            formIsValid = false;
            errors["require_days"] = 'Please enter a valid days!';
          }
        } 

        //price_from
        if(!fields["price_from"]){
           formIsValid = false;
           errors["price_from"] = 'Please enter a start price!';
        }

        if(typeof fields["price_from"] !== "undefined")
        {
          if(isNaN(parseInt(fields["price_from"], 10)) || parseInt(fields["price_from"], 10) < 1)
          {
            formIsValid = false;
            errors["price_from"] = 'Please enter a valid price!';
          }
        } 

        //price to
        if(!fields["price_to"]){
           formIsValid = false;
           errors["price_to"] = 'Please enter a end price!';
        } 

        if(typeof fields["price_to"] !== "undefined")
        {
          if(isNaN(parseInt(fields["price_to"], 10)) || parseInt(fields["price_to"], 10) < 1)
          {
            formIsValid = false;
            errors["price_to"] = 'Please enter a valid price!';
          }
        }


        if(typeof fields["price_from"] !== "undefined" && typeof fields["price_to"] !== "undefined")
        {
          if(parseInt(fields["price_from"], 10) > parseInt(fields["price_to"], 10))
          {
            formIsValid = false;
           errors["price_to"] = 'Please enter price greater then price from!';
          }
        }

        //Name
        if(!fields["name"]){
           formIsValid = false;
           errors["name"] = 'Please enter name';
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
           errors["email"] = 'Please enter email';
        }
        if(typeof fields["email"] !== "undefined")
        {
            let lastAtPos = fields["email"].lastIndexOf('@');
            let lastDotPos = fields["email"].lastIndexOf('.');

            if (!(lastAtPos < lastDotPos && lastAtPos > 0 && fields["email"].indexOf('@@') === -1 && lastDotPos > 2 && (fields["email"].length - lastDotPos) > 2)) {
              formIsValid = false;
              errors["email"] = 'E-Mail address does not appear to be valid!';
            }
       }
        
        //Mobile No
        if(!fields["mobile_no"]){
           formIsValid = false;
           errors["mobile_no"] = 'Please enter mobile no';
        }
        if(typeof fields["mobile_no"] !== "undefined"){
             if(this.props.international_store === 0 && fields["mobile_no"].length < 10){
                 formIsValid = false;
                 errors["mobile_no"] = 'Mobile  does not appear to be valid!';
             } 
             if(this.props.international_store === 1 && fields["mobile_no"].length < 6){
                 formIsValid = false;
                 errors["mobile_no"] = 'Mobile  does not appear to be valid!';
             }         
        }


       this.setState({errors: errors});
       return formIsValid;
   }

   handleChange(field, event) 
   {
     let fields = this.state.fields;
     fields[field] = event.target.value;
     this.setState({fields});
     this.handleValidation();
   }


  handleFormSubmit(e)
  {
    e.preventDefault();
     if(this.handleValidation())
     {
           $('body').removeClass('loaded').addClass('loading');
          var form    = $('#post_your_requirment_form'),
          formData = new FormData(),
          params   = form.serializeArray(),
          files    = form.find('[name="reference_image[]"]')[0].files;
          $.each(files, function(i, file) {
                formData.append('reference_image['+i+']', file);
          });
          $.each(params, function(i, val) {
                formData.append(val.name, val.value);
          });

        var response = this.props.dispatch(postYourRequirement(formData));
         response.then(function(data){
          var success_div = '';
          $('#post_your_requirment_form')[0].reset();
          if(data.statusCode===200)
          {
             success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+data.message+'</p></div></div>';
           }
          else
           {
          success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-ok"></span> <hr class="message-inner-separator"><p>'+data.message+'</p></div></div>';
           }

           $(document.body).append(success_div);   
                  $('#notification').fadeOut(3000);
                   setTimeout(function() {
                    $('#notification').remove();
            }, 3000);
            $('body').removeClass('loading').addClass('loaded');
        });


      }    
  }


 render(){
    return (
      <div>
       <div className="contner head_margin side-collapse-container">
        <section className="panel cart_box">
          <h2 className="about_title">Post Your Requirement</h2>

         <div className="col-xs-12">
              <form id="post_your_requirment_form" onSubmit={this.handleFormSubmit} encType="multipart/form-data">
              <div className="mobile_details_panel  delivery_box">
                <div className="new_add_popup_input_box"> 
                <select className="address_select_input" name="category" onChange={this.handleChange.bind(this, "category")}>
                    <option value=""> --- Please Select Category --- </option>
                     {
                      this.props.post_req_category ?
                      this.props.post_req_category.map((menu, index) => {
                        return (<option key={index} value={menu}>{menu}</option>)
                        })
                        : ''
                    }
                  
                 </select>
                 <span className="error">{this.state.errors["category"]}</span> 
              </div>

                <div className="new_add_popup_input_box account_info"> 
                    <input name="quantity" type="text" className="pr_info_input" placeholder="Quantity*" alt="Quantity" onChange={this.handleChange.bind(this, "quantity")} />
                     <span className="error">{this.state.errors["quantity"]}</span> 
                </div>

                <div className="new_add_popup_input_box account_info"> 
                    <input name="require_days" type="text" className="pr_info_input"  placeholder="Required Days!*" alt="Required Days!" onChange={this.handleChange.bind(this, "require_days")} />
                     <span className="error">{this.state.errors["require_days"]}</span> 
                </div>

                <div className="order_history_card_view" style={{margin:'0px'}}>
                  <div className="cart_table_title_seller">
                    <h3>Price Range</h3>
                  </div>
                </div>

                <div className="new_add_popup_input_box account_info"> 
                    <input name="price_from" type="text" className="pr_info_input"  placeholder="From Rs.*" alt="From Rs." onChange={this.handleChange.bind(this, "price_from")} />
                    <span className="error">{this.state.errors["price_from"]}</span> 
                </div>

                <div className="new_add_popup_input_box account_info"> 
                    <input name="price_to" type="text" className="pr_info_input"  placeholder="To Rs.*" alt="To Rs." onChange={this.handleChange.bind(this, "price_to")} />
                    <span className="error">{this.state.errors["price_to"]}</span>
                </div>

                <div className="clearfix"></div>
                <div className="new_add_popup_input_box account_info"> 
                <label>Upload Refernce Image</label>
                    <input name="reference_image[]" multiple type="file" className="pr_info_input"/>
                </div>
                     <div className="clearfix"></div>
                     <div className="order_history_card_view" style={{margin:'0px'}}>
                        <div className="cart_table_title_seller">
                          <h3>Don't have refernce image? DO you have any refernce link from any website?</h3>
                        </div>
                      </div>
                      <div className="clearfix"></div>  

                <div className="new_add_popup_input_box account_info"> 
                    <input name="refrenece_link" type="text" className="pr_info_input"  placeholder="Please Provide link in box" alt="Please Provide link in box"/>
                </div>

                <div className="new_add_popup_input_box account_info"> 
                    <textarea name="description" rows="3" cols="151" className="pr_info_input"  placeholder="Please describe your buying requirement. i.e Fabric,material and many other preferences." alt="Please describe your buying requirement. i.e Fabric,material and many other preferences."></textarea>
                </div>

                 <div className="new_add_popup_input_box account_info"> 
                    <input name="name" type="text" className="pr_info_input"  placeholder="Please Provide Name" alt="Please Provide Name" onChange={this.handleChange.bind(this, "name")} />
                    <span className="error">{this.state.errors["name"]}</span> 
                </div>

                <div className="new_add_popup_input_box account_info"> 
                    <input name="email" type="text" className="pr_info_input"  placeholder="Please Provide Email" alt="Please Provide Email" onChange={this.handleChange.bind(this, "email")} />
                    <span className="error">{this.state.errors["email"]}</span>
                </div>

                <div className="new_add_popup_input_box account_info"> 
                    <input name="mobile_no" type="text" className="pr_info_input"  placeholder="Please Provide Mobile No." alt="Please Provide Mobile No." onChange={this.handleChange.bind(this, "mobile_no")} />
                    <span className="error">{this.state.errors["mobile_no"]}</span>
                </div>

                <div className="clearfix"></div>
                <div className="col-xs-6">
                         <Link to={Api.folder_path}  className="btn deliver_btn pull-left" style={{margin:'0px', padding:'10px 8px'}} >CANCEL</Link>
                        </div> 
                        <div className="col-xs-6">
                         <button type="submit"  className="btn add_btn_new_popup pull-right" style={{marginBottom:'4px'}} data-dismiss="modal" data-direction='right'>SAVE</button>
                        </div>
              </div>
              </form>
              <div className="clearfix"></div>
         </div>
           
         </section>

         <div className="clearfix"></div>
       
   </div>
   </div>
)}}


function mapStateToProps(state){
  return {
     post_req_category: state.accountReducer.post_req_category,
     international_store: state.headerReducer.userlogin.international_store,
     actions: bindActionCreators(postYourRequirement,postYourRequirementCategory)
  };
}
export default connect(mapStateToProps)(PostYourRequirement);