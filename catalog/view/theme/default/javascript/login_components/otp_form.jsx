
class Otpform extends React.Component {

   constructor(props)
   {
   	 super(props);
   	this.state = {
      data: []
    };
  } 

   componentDidMount()
  {
   var otp_verify_success = this.props.otp_verify_success;
   setTimeout(function(){ 
   if(otp_verify_success != '' && otp_verify_success == 0)
   {
     $('.login_mobile_error').html("Invalid verify link");
     $('.login_mobile_error').show();
     $('#login_link_header').trigger('click');
   }
   else if(otp_verify_success != '' && otp_verify_success == 1)
   {
     $('#signup_popup').modal('show');
     check_reg_telephone();
    }
   else if(otp_verify_success != '' && otp_verify_success == 2)
   {
      $('.login_mobile_error').html("Warning: No match for Mobile Number and/or Password");
      $('.login_mobile_error').show();
      $('#login_link_header').trigger('click');
   }
   else if(otp_verify_success != '' && otp_verify_success == 3)
   {
     $('#login_link_header').trigger('click');
   }
   }, 2000);

}

render() {
        let international_store = this.props.international_store;
        if(this.props.international_store == 1)
        {
         var cc = $.parseJSON('{"AE":"+971","AU":"+61","BD":"+880","CA":"+1","IN":"+91","MY":"+60","OM":"+968","SA":"+966","GB":"+44"}');
         $('#select_country').attr('data-selected-country','CA');
        }
        else
        {
          var cc = $.parseJSON('{"IN":"+91"}');
          $('#select_country').attr('data-selected-country','IN');
        }

     function opt_form(e) {
         var self = this;
         $("#otp_form input[type=submit]").val('loading...').prop("disabled", true);
         e.preventDefault();
         var actionurl         = './api/login/send_otp';
         var country_code_text = $("#country_code option:selected" ).text();
         var country_code_val  = $("#country_code" ).val();
         var form_data         = $("#otp_form").serialize();
             form_data         = form_data.replace("country_code="+country_code_val, "country_code="+country_code_text+"&country_iso_code="+country_code_val);

         var ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response) {
                    
                   var data = response.data;

                    if(data['error'])
                    {
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                     $("#collapseverify").css('visibility','hidden').hide();
                     $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                    }
                    else
                    {
                         $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                         if(data['new_user'])
                         {
                           $(".login_mobile_nmr").html(data['country_code']+' '+data['reg_telephone']);
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

                           $('.login_mobile_error').hide();
                           if(!data['user_verify'])
                           {
                             $('.login_mobile_success').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                             $('.login_mobile_success').show();
                             $("#save_reg_telephone, #send_otp_title").hide();
                             $("#edit_reg_telephone").show();
                             $("#collapseverify").css('visibility','visible').show();
                           }
                           else
                           {
                             $("#collapseverify").css('visibility','hidden').hide();
                             $('#login_verify_popup_close').click();
                             if($.isNumeric( data['reg_telephone'] ))
                              { 
                                 $("#reg_telephone_field").addClass("hide"); 
                                 $("#reg_email_field").removeClass("hide"); 
                                 $("#reg_telephone_signup").val(data['reg_telephone']); 
                              }
                            else 
                              { 
                                 $("#reg_telephone_field").removeClass("hide"); 
                                 $("#reg_email_field").addClass("hide");
                                 $("#reg_email_signup").val(data['reg_telephone']);
                              }
                             
                             if($('#input-is_dropshipper').val() == 2)
                             {
                               $(".customer_type_checkbox").parent("label").parent("div").addClass('disable');
                               $(".customer_type_checkbox").prop("checked", false);  
                               $(".customer_type_dropshipper_checkbox").prop("checked", true);
                             }
                             else
                             {
                               $(".customer_type_checkbox").parent("label").parent("div").removeClass('disable');
                               $(".customer_type_checkbox").prop("checked", false);  
                              $(".customer_type_dropshipper_checkbox").prop("checked", false);
                             }
                              

                              $('#signup_popup_open').click();
                              country_list();
                           }
                         }
                         else
                         {
                           $(".login_mobile_nmr").html(data['country_code']+' '+data['reg_telephone']);
                           if(data['country_iso_code'] != '')
                           {
                             $(".country-flag").removeClass().addClass('country-flag flagstrap-icon flagstrap-'+data['country_iso_code'].toLowerCase());
                             $(".flag_area").show();
                           }
                           else
                           {
                              $(".flag_area").hide();
                           }
                           $('.login_mobile_success, .login_mobile_error').hide();
                           $("#collapseverify").css('visibility','hidden').hide();
                           $('#login_verify_popup_close').click();
                           $('#login_popup_open').click();
                           $('#login_popup').on('shown.bs.modal', function() {
                            $('#collapseverify_1 #login_form .password_box').focus();
                           });
                           
                         }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
       }

      function send_otp_agin(e) {
           $('.login_mobile_success, .login_mobile_error').hide();
           $("#verify_otp_form input[name=otp]").val('');
           $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px');
           e.preventDefault();
           opt_form(e);
      };

        function verify_otp_form(e) {
         var self = this; 
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = './api/login/verify_otp';
         var form_data = $("#verify_otp_form").serialize();
         var ajax =  $.ajax({
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
                    $("#verify_otp_form :input[name=otp]").val('');
                    $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
                    if(data['error'])
                    {
                     $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                    }
                    else
                    {

                       if(data['redirect_cart']==0 || data.redirect_cart=== "credit")
                       {
                        $('.login_mobile_success, .login_mobile_error').hide();
                        $("#collapseverify").css('visibility','hidden').hide();
                        $('#login_verify_popup_close').click();
                        $('#login_popup_close').click();
                          if($.isNumeric( data['reg_telephone'] ))
                              { 
                                 $("#reg_telephone_field").addClass("hide"); 
                                 $("#reg_email_field").removeClass("hide");
                                 $("#reg_telephone_signup").val(data['reg_telephone']); 
                              }
                            else 
                              { 
                                 $("#reg_telephone_field").removeClass("hide");
                                 $("#reg_email_field").addClass("hide");
                                 $("#reg_email_signup").val(data['reg_telephone']);
                              }
                          
                           if($('#input-is_dropshipper').val() == 2)
                             {
                               $(".customer_type_checkbox").parent("label").parent("div").addClass('disable');
                               $(".customer_type_checkbox").prop("checked", false);  
                               $(".customer_type_dropshipper_checkbox").prop("checked", true);
                             }
                             else
                             {
                               $(".customer_type_checkbox").parent("label").parent("div").removeClass('disable');
                               $(".customer_type_checkbox").prop("checked", false);  
                              $(".customer_type_dropshipper_checkbox").prop("checked", false);
                             }

                          $('#signup_popup_open').click();
                          country_list();
                      }
                      else if(data['redirect_cart'] == 2)
                      { 
                          $('#login_verify_popup_close').click();
                          $('#want_designe_popup').modal("show"); 
                      }
                      else
                      {

                        $('#login_verify_popup_close').click();
                        $('.cart_shopping_success').html(data['msg']);
                        //$('#cart_shopping_popup_open').click();

                        if(data['redirect_cart'] == 1)
                        {
                          $('#button-cart, #detail-button-cart, .button-cart-list').trigger("click");
                        }
                        else
                        {
                          var redirect_cart =  data['redirect_cart'].split("-");
                          cart_add(redirect_cart[0], redirect_cart[1], 0);
                        }

                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
       }


          function check_reg_telephone()
          {
              var mobile       = $('#reg_telephone').val();
              var country_code = $('#country_code').val();
              var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
              $(".flagstrap").hide();
         
           if(international_store == 1)
           {
             if (mobile != '' && /\D/g.test(mobile))
               {
                // $('#reg_telephone').prev("label").html("Email");
                 $(".flagstrap").hide();
               }
               if (email_pattern.test(mobile))
                {
                  $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                }
                else
                {
                   $("#otp_form input[type=submit]").val('continue').prop("disabled", true);
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
                 //$('#reg_telephone').prev("label").html("Mobile");
                 $(".flagstrap").show();
               }

                if (mobile_pattern.test(mobile))
                {
                    if (mobile.charAt(0) != 0 && country_code == 'IN')
                    {
                       $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                    }
                    if (country_code != 'IN')
                    {
                      $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                     }
                }    
               else
                {
                   $("#otp_form input[type=submit]").val('continue').prop("disabled", true);
                }
            }    
        }
 
    function edit_register_no()
    {
        $("#edit_reg_telephone").hide();
        $("#save_reg_telephone, #send_otp_title").show();
        $('.login_mobile_success, .login_mobile_error').hide(500);
        $("#verify_otp_form :input[name=otp]").val('');
        $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
        $("#collapseverify").hide();
        check_reg_telephone();
    }       


     $(document).delegate('#login_link_header, #footer-login, #button-cart, #dropshipper_login_link, #wishlist-total, .addtocart_bottem, .addtocart_new, .button-cart-list, .productInfoAjax, #credit_banner', 'click', function(e)
     {       

           if($(this).attr('id') == 'dropshipper_login_link') { $('#input-is_dropshipper').val(2); } 
           else { $('#input-is_dropshipper').val(0); } 
           if($(this).attr('id') == 'credit_banner') { $("input[name=redirect_cart]").val('credit'); } 

           $('#select_country').flagStrap({countries:cc}); 
           $('.login_mobile_success, .login_mobile_error').hide();
           $('.login_password_success, .login_password_error').hide();
           $('.login_register_success, .login_register_error').hide();
           $("#collapseverify").css('visibility','hidden').hide();
           $("#edit_reg_telephone").hide();
           $("#save_reg_telephone, #send_otp_title").show();
           $("#register_form input[type=submit]").val('continue').prop("disabled", false);
           $("#login_form input[type=submit]").val('continue').prop("disabled", false);
           $("#verify_otp_forgot_form input[type=submit]").val('verify').prop("disabled", false);
           $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
           $('#register_form').trigger("reset");
           $('#register_form').find("input, textarea").each(function (i,e) {
               $(this).attr("placeholder",$(this).prev('label').html());
               $(this).prev('label').remove();
            });
           $("label.error").remove();
           $("span.error").html("");
           if(getCookie("customer_mobile") != '')
           {
              $(".login_mobile_nmr").html(getCookie("country_code")+' '+getCookie("customer_mobile"));
              $("#reg_telephone").val(getCookie("customer_mobile"));
              $('#reg_telephone').prev("label").css('margin-top', "0px");
               if($.isNumeric(getCookie("customer_mobile") ))
               {
                  $('#reg_telephone').prev("label").html("Mobile");
                  $(".flagstrap").show();
               }
               else
               {
                  $('#reg_telephone').prev("label").html("Email");
                  $(".flagstrap").hide();
               }
           }

         if(getCookie("country_iso_code") != '')
           {
              $(".country-flag").removeClass().addClass('country-flag flagstrap-icon flagstrap-'+getCookie("country_iso_code").toLowerCase());
              $(".flag_area").show();
            }
          else
            {
              $(".flag_area").hide();
            }
           check_reg_telephone();

      });


   function login_verify_popup_close(e){
          if($('#product_popup').hasClass('in'))
          {
            $('#login_verify_popup').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
             });
          }
    }

  function country_list()
  {
     axios({
        method:'get',
        url:'./api/login/country_list',
        responseType:'json'
        })
       .then(response => {
            var data = response.data.data;
            var result = '<option value="0">'+data['country_title']+'</option>';
            var user_country = getCookie("user_country");
            for(var i=0; i<data['country_data'].length; i++)
                {
                  if(data['country_data'][i].iso_code_2 == user_country || data['country_data'][i].iso_code_3 == user_country)
                  {
                   result = result+'<option value="'+data['country_data'][i].country_id+'" selected>'+data['country_data'][i].name+'</option>';
                  }
                  else
                  {
                   result = result+'<option value="'+data['country_data'][i].country_id+'">'+data['country_data'][i].name+'</option>';
                  }
               
                }
            $("#country_id").html(result);
       });
  }


		return (
       <div className="modal fade add_new_address" id="login_verify_popup" role="dialog" data-backdrop="static" data-keyboard="false">
        <div className="modal-dialog login_register_popup">
          <div className="modal-content">
            <div className="modal-header address_popup_head">
              <button type="button" className="close" data-dismiss="modal" onClick={login_verify_popup_close} id="login_verify_popup_close">&times;</button>
              <button className="hide" data-toggle="modal" data-target="#login_verify_popup" id="login_verify_popup_open"></button>
              {this.props.international_store ?
              <h4 className="modal-title" id="send_otp_title">Please enter your Email address</h4>
               : 
              <h4 className="modal-title" id="send_otp_title">Please enter your mobile number</h4>
              }
            </div>
            <div className="modal-body">
            <div className="success_msg login_mobile_success"></div>
             <div className="danger_msg login_mobile_error"></div>
             <div className="otp_model">
            <form name="otp_form"  id="otp_form" onSubmit={opt_form}>
               <div className="mobile_details_panel" id="edit_reg_telephone">
               <span className="flag_area"><i className="country-flag flagstrap-icon flagstrap-"></i></span>
                 <div className="login_mobile_nmr"> </div> &nbsp;
                   <a href="javascript:;" onClick={() => edit_register_no() }><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
               </div>

                <div className="mobile_details_panel" id="save_reg_telephone">
                  <div className="flagstrap flag_box" id="select_country" data-input-id="country_code" data-input-name="country_code" data-selected-country="IN"></div>
                    
                    {this.props.international_store ?
                     <input type="text" onKeyUp={() => check_reg_telephone() } onChange={() => check_reg_telephone() } name="reg_telephone" className="mobile_input" placeholder="Email" alt="Email" id="reg_telephone" autoComplete="off" autoFocus="autofocus" /> 
                     : 
                    <input type="text" onKeyUp={() => check_reg_telephone() } onChange={() => check_reg_telephone() } name="reg_telephone" className="mobile_input" placeholder="Mobile" alt="Mobile" id="reg_telephone" autoComplete="off" autoFocus="autofocus" /> 
                     }
                    <input type="submit" name="send_otp" value="Continue" className="btn deliver_btn pull-right" id="send_otp" disabled="disabled" />
                    <div className="clearfix"></div>
                      {this.props.international_store ? '' :
                      <a href='./seller_panel/#' className="login_seller">Login as seller</a>
                      }
                     <div className="clearfix"></div>
                </div>
            </form>
            
              <form name="verify_otp_form"  id="verify_otp_form" onSubmit={verify_otp_form}>                
              <div id="collapseverify" className="panel-collapse collapse">
                  <input type="text" name="otp" className="password_box" placeholder="Please Enter OTP" maxLength="6" alt="OTP" />                 
                   <input type="submit" name="verify_otp" value="Verify" className="btn deliver_btn pull-right" />               
                    <div className="clearfix"></div>
                  <div className="otp_agin"><a href="javascript:;" onClick={send_otp_agin} className="send_otp_agin">Didn't get OTP?</a></div>
              </div>
              <input type="hidden" name="otp_page" value="sign_up" label="" />           
              <input type="hidden" name="reg_telephone" label="" />            
              <input type="hidden" name="country_code" label="" />            
              <input type="hidden" name="country_iso_code" label="" />            
              <input type="hidden" name="redirect_cart" value="0" label="" id="otp_redirect_cart" />          
               </form>          
                </div>
            </div>
          </div>
        </div>
   </div>
		)
	}
}

 