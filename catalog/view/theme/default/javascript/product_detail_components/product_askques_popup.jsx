class ProductAskQuesPopup extends React.Component {

    constructor(props){
        super(props);
        this.state = {
           //data: [],
            is_login : 1,
            customer_name : '',
            telephone : '',
            email : '',
            popup_question : '',
        };
    } 

    componentDidMount(){
        if(customer_data.length == 0){
            this.setState({is_login : 0 })
        }
    }
    
    
    render() {  
        return (<AskQuesPopup 
                        language = {this.props.language} 
                        product_id = {this.props.product_id}  
                        customer_data = {this.props.customer_data}
                    />)  
    }
}


class AskQuesPopup extends React.Component {

    constructor(props){
        super(props);
        this.closeAskPopup = this.closeAskPopup.bind(this);

        this.state = {
           is_error : 0, 
       error_msg : '',
        };
    } 
    
    SubmitProductAskQuesPopup(form_name) {
  
    var popup_question = $('#'+form_name+' .question_login').val();
    var product_id = $('#'+form_name+' .product_id').val();
    var error = 'Fill out all the given fields to ask a question.';
    var error_email = 'Please provide your Mobile No or Email';

    if(form_name == 'guest')
    {
        var customer_name = $('#'+form_name+' .customer_name').val();
        var telephone = $('#'+form_name+' .telephone').val();
        var email = $('#'+form_name+' .email').val();
        
        if(customer_name == ''){
            $('.alert_msg').remove();
            $('.ask_question_popup').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-danger">'+error+'</div></div>');
            return false;
        }

        if(telephone == '' && email == ''){
            $('.alert_msg').remove();
            $('.ask_question_popup').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-danger">'+error_email+'</div></div>');
            return false;
        }
    }
    else
    {
      var customer_name = '';
      var telephone = '';
      var email = '';  
    }

       if(popup_question == ''){
            $('.alert_msg').remove();
            $('.ask_question_popup').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-danger">'+error+'</div></div>');
            return false;
        }

        $.ajax({
            url: 'api/product/askQuestion', 
            method: 'POST',
            data: {
                customer_name: customer_name,
                telephone: telephone,
                email: email,
                popup_question: popup_question,
                product_id: product_id,
            },
            success: function(result) {
                $('.alert_msg').remove(); 
                $('.popup-q-body textarea').val('');
                $("#askQuestionFormLogin")[0].reset();  
                $('.ask_question_popup').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-success"><i class="fa fa-check-circle"></i>'+result['message']+'</div></div>');
            }.bind(this)
        }); 

    }

    closeAskPopup(e){
        $("#ask_question_popup").modal("hide");
        $('.popup-q-body textarea').val('');
        $('.alert_msg').hide();
        $("#askQuestionFormLogin")[0].reset(); 
       
        if($('#product_popup').hasClass('in'))
        {  
          $('#ask_question_popup').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
          });
        }  
    }

    render() { 

let customer_id = getCookie("customer_id");

function openAskPopup()
{
   customer_id = getCookie("customer_id");
   if(customer_id > 0) { $("#login").show(); $("#guest").hide(); }
   else { $("#guest").show(); $("#login").hide(); }
  $("#ask_question_popup").modal("show");
    
}

        return (
            <div>
                    <div className="ask_question col-sm-4 pull-right" onClick={()=>openAskPopup()}>
                      Ask A Question
                    </div>

                    <div className="modal fade add_new_address question_popup" id="ask_question_popup" role="dailog" data-backdrop="static" data-keyboard="false">
                        <div className="modal-dialog">
                            <div className="modal-content">
                                <div id="ask_question_popup_title" className="modal-header ask_question_popup">
                                    <button type="button" className="close" onClick={this.closeAskPopup}  data-product-id={this.props.product_id}>&times;</button>
                                    <h4 className="modal-title">Ask a question about this product.</h4>
                                </div>
                                
                                <div className="modal-body popup-q-body">

                                  
                                    <form id="askQuestionFormLogin">
                                      <div id="login" className="mobile_details_panel">    
                                        <div className="clearfix"></div>
                                        <input className="customer_name" type="hidden" name="customer_name" value={customer_data.customer_name} placeholder="Please Enter your Name" alt="Name" />
                                        <div className="clearfix"></div>
                                        <input className="telephone" type="hidden" name="telephone" value={customer_data.telephone}  placeholder="Please Enter your Mobile Number" alt="Mobile Number" />
                                        <div className="clearfix"></div>
                                        <input className="email" type="hidden" name="email" value={customer_data.email} placeholder="Please Enter your Email ID" alt="Email ID" />
                                        <div className="clearfix"></div>
                                        <textarea rows="4" className="question_login" name="popup_question"  placeholder="Enter your Question" alt="Question"></textarea>
                                        <div className="clearfix"></div>
                                        <input className="product_id" type="hidden" name="product_id" value={this.props.product_id} />
                                        <button onClick={()=>this.SubmitProductAskQuesPopup('login')} type="button" className="btn deliver_btn pull-right">Submit</button>
                                      </div>
                                
                                      <div id="guest" className="mobile_details_panel">    
                                        <div className="clearfix"></div>
                                        <input className="customer_name" type="text" name="customer_name" placeholder="Please Enter your Name" alt="Name" />
                                        <div className="clearfix"></div>
                                        <input className="telephone" type="text" name="telephone"  placeholder="Please Enter your Mobile Number" alt="Mobile Number" />
                                        <div className="clearfix"></div>
                                        <input  className="email" type="text" name="email" placeholder="Please Enter your Email ID" alt="Email ID" />
                                        <div className="clearfix"></div>
                                        <textarea rows="4" className="question_login" name="popup_question"  placeholder="Enter your Question" alt="Question"></textarea>
                                        <div className="clearfix"></div>
                                        <input className="product_id" type="hidden" name="product_id" value={this.props.product_id} />
                                        <button onClick={()=>this.SubmitProductAskQuesPopup('guest')} type="button" className="btn deliver_btn pull-right">Submit</button>
                                      </div>
                                   </form>
                                 

                                    <div className="clearfix"></div>
                                </div>
                                       
                                
                            </div>
                        </div>
                    </div>


                
            </div>
        )  
    }
}