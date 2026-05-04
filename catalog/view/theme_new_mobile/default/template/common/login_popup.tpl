  
    <!-- login(Verify) popup -->
  <div class="modal fade add_new_address" id="login_verify_popup" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header address_popup_head">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <?php if($international_store == 1) { ?>
              <h4 class="modal-title" id="send_otp_title">Please enter your Email address</h4>
              <?php } else { ?>
              <h4 class="modal-title" id="send_otp_title">Please enter your mobile number or Email address</h4>
              <?php } ?>

            </div>
            <div class="modal-body">
            <div class="success_msg login_mobile_success" style="display:none;"></div>
             <div class="danger_msg login_mobile_error" style="display:none;"></div>            
            <?php echo $send_otp_form['form_start']; ?>


               <div class="mobile_details_panel" id="edit_reg_telephone" style="display:none;">
               <span class="flag_area"><i class="country-flag flagstrap-icon flagstrap-<?php echo strtolower($country_iso_code); ?>" style="margin-right: 10px;"></i></span>
                 <div class="login_mobile_nmr"><?php echo $country_code.' '.$customer_mobile; ?></div>
                 &nbsp;
                   <a href="javascript:;" onclick="edit_register_no();"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</a>
               </div>

                <div class="mobile_details_panel" id="save_reg_telephone">
                     <div class="flagstrap flag_box" id="select_country" data-input-id="country_code" data-input-name="country_code" data-selected-country="IN"></div>

                    <?php if($international_store == 1) { ?>
                      <?php echo $send_otp_form['reg_email']; ?> <br />
                    <?php } else { ?>
                       <?php echo $send_otp_form['reg_telephone']; ?> <br />
                    <?php } ?>

                    <div class="clearfix"></div>
                     <?php echo $send_otp_form['submit']; ?>
                     <!--  <a href="<?php echo $manufacturer_link; ?>" class="login_seller">Login as seller</a>   -->
                     <div class="clearfix"></div>
                </div>

            <?php echo $send_otp_form['form_end']; ?>

            <?php echo $verify_otp_form['form_start']; ?>
                <div id="collapseverify" class="panel-collapse collapse">
                 <?php echo $verify_otp_form['otp']; ?>
                 <div class="clearfix"></div>
                 <div class="otp_agin"><a href="javascript:;" class="send_otp_agin">Didn't get OTP?</a></div>
                 <?php echo $verify_otp_form['submit']; ?>
                   <div class="clearfix"></div>
                 </div>
             <?php echo $verify_otp_form['otp_page']; ?>
             <?php echo $verify_otp_form['reg_telephone']; ?>
             <?php echo $verify_otp_form['country_code']; ?>
             <?php echo $verify_otp_form['country_iso_code']; ?>
             <?php echo $verify_otp_form['redirect_cart']; ?>
             <?php echo $verify_otp_form['form_end']; ?>
            </div>
          </div>
        </div>
   </div>
     <!-- login(Verify) popup (End) -->


     <!-- login(Verify) popup -->
    <div class="modal fade add_new_address" id="cart_shopping_popup" role="dialog">
          <div class="modal-dialog login_register_popup">
            <div class="modal-content">
              <div class="modal-header address_popup_head">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Congratulation!</h4>
              </div>
              <div class="modal-body">
                <div class="success_msg cart_shopping_success">Your mobile number verified successfully.</div>
              <div class="otp_model">
                <form method="get">
                  <button id="continue_shopping_btn" data-dismiss="modal" class="btn deliver_btn pull-right" type="button">Continue Shopping</button>
                  <a href="<?php echo $this->url->link('checkout/cart', '', 'SSL'); ?>">
                   <button id="go_to_cart_btn" class="btn deliver_btn pull-left" type="button">Go To Cart</button>
                 </a>
               </form>
              </div>
              </div>
            </div>
          </div>
     </div>
       <!-- login(Verify) popup (End) -->



         <!-- login popup -->
  <div class="modal fade add_new_address" id="login_popup" role="dialog">
        <div class="modal-dialog login_register_popup">
          <div class="modal-content">
            <div class="modal-header address_popup_head">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">SIGN IN</h4>
            </div>
            <div class="modal-body">
            <div class="success_msg login_password_success" style="display:none;"></div>
             <div class="danger_msg login_password_error" style="display:none;"></div>
                <div class="mobile_details_panel">

               <div class="mobile_details_panel" id="edit_login_telephone">
                <span class="flag_area"><i class="country-flag flagstrap-icon flagstrap-<?php echo strtolower($country_iso_code); ?>" style="margin-right: 10px;"></i></span>
                 <div class="login_mobile_nmr"><?php echo $country_code.' '.$customer_mobile; ?></div>
                 &nbsp;
                   <a href="javascript:;" onclick="edit_login_no();"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</a>
               </div>

                  <div class="clearfix"></div>
                  <div id="collapseverify_1">
                  <?php echo $login_form['form_start']; ?>
                  <?php echo $login_form['password']; ?>
                  <div class="clearfix"></div>
                   <div class="otp_agin pull-left"><a href="javascript:;" onclick="send_otp();">Login with otp</a></div>
                   <input type="hidden" name="referrers" value=""/>
                   <?php echo $login_form['redirect_cart']; ?>
                   <?php echo $login_form['submit']; ?>
                   <?php echo $login_form['form_end']; ?>
                    </div>

                   <div id="collapseverify_2" style="display:none;">
                   <?php echo $verify_otp_forgot_form['form_start']; ?>
                   <?php echo $verify_otp_forgot_form['otp']; ?>
                    <div class="clearfix"></div>
                   <div class="otp_agin pull-left"><a href="javascript:;" onclick="login_with_password();">Login with password</a>
                   &nbsp;
                    OR &nbsp;
                    <a href="javascript:;" onclick="send_otp();">Didn't get OTP?</a>
                   </div>
                   <?php echo $verify_otp_forgot_form['submit']; ?>
                   <?php echo $verify_otp_forgot_form['otp_forgot_page']; ?>
                  <?php echo $verify_otp_forgot_form['referrers']; ?>
                  <?php echo $verify_otp_forgot_form['redirect_cart']; ?>
                   <?php echo $verify_otp_forgot_form['form_end']; ?>
                    <div class="clearfix"></div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <!-- <div id="collapsepassword" class="panel-collapse collapse">
                <?php //echo $update_password_from['form_start']; ?>
                <br>
                <?php //echo $update_password_from['password']; ?>
                <br>
                <?php //echo $update_password_from['confirm']; ?>
                <?php //echo $update_password_from['submit']; ?>
                <div //class="clearfix"></div>
                <?php //echo $update_password_from['form_end']; ?>
                </div>
                <div //class="clearfix"></div> -->

                 </div>

            </div>
          </div>
        </div>
   </div>
     <!-- login popup (End) -->

         <!-- Signup popup -->
  <div class="modal fade add_new_address" id="signup_popup" role="dialog">
        <div class="modal-dialog login_register_popup">
          <div class="modal-content">
            <div class="modal-header address_popup_head">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">SIGN UP</h4>
            </div>
            <div class="modal-body popup_scroll">

            <div class="alert alert-success login_register_success" style="display:none;"></div>
            <div class="danger_msg login_register_error" style="display:none;"></div>

            <?php echo $register_form['form_start']; ?>
            <input type="hidden" name="is_dropshipper" value="<?php echo $is_dropshipper; ?>" id="input-is_dropshipper" class="form-control" />
            <input type="hidden" name="referrers" value=""/>
                <div class="mobile_details_panel">
                         <span class="flag_area"><i class="country-flag flagstrap-icon flagstrap-<?php echo strtolower($country_iso_code); ?>" style="margin-right: 10px;"></i></span>
                         <div class="login_mobile_nmr"><?php echo $country_code.' '.$customer_mobile; ?></div>
                         <div class="clearfix"></div>
                         <h4 class="modal-title">Please provide following details</h4>
                         <div class="clearfix"></div>

          <?php
            $i = 1;
            foreach($register_form['customer_type_id'] as $customer_type_id) { ?>
            <div class="checkbox_information">
               <label>
                 <?php echo $customer_type_id; ?>
                  <div class="control__indicator"></div>
                </label>
            </div>
            <?php if($i%2==0) { echo '<div class="clearfix"></div>'; }  $i++; } ?>

            <div class="clearfix"></div>
            <div id="reg_email_field">
            <div class="col-sm-12 nopadding">
            <?php echo $register_form['reg_email']; ?>
            </div>
            <div class="clearfix"></div>
            </div>
            <div  id="reg_telephone_field" style="display:none;">
             <div class="col-sm-12 nopadding">
            <?php echo $register_form['reg_telephone']; ?>
            </div>
            <div class="clearfix"></div>
            </div>
            <div class="col-sm-12 nopadding">
            <?php echo $register_form['password']; ?>
            </div>
            <div class="clearfix"></div>
            <div class="otp_agin">
            <a href="javascript:;" class="show_password" onclick="show_password();">Hide Password</a>
            </div>
            <div class="clearfix"></div>
           <div class="col-sm-12 nopadding">
            <?php echo $register_form['name']; ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6 nopadding">
            <?php echo $register_form['company']; ?>
            </div>
             <?php if($international_store == 0) { ?>
             <div class="col-sm-6 nopadding">
             <div class="checkbox_information checkbox_information_full">
               <label>
                 <?php echo $register_form['gst_uin_number']; ?>
                  <div class="control__indicator"></div>
                </label>
            </div>
            </div>
            <div class="clearfix"></div>
            <div class="hide" id="gst_uin_number_area">
            <div class="col-sm-6 nopadding">
            <?php echo $register_form['gst_number']; ?>
            </div>
             
            </div>
            <?php } ?>
            <div class="clearfix"></div>

           <div class="col-sm-6 nopadding">
            <?php echo $register_form['postcode']; ?>
           </div>
           <div class="col-sm-6 nopadding">
            <?php echo $register_form['country_id']; ?>
            </div>
             <div class="col-sm-6 nopadding">
            <?php echo $register_form['zone_id']; ?>
            </div>
            <div class="clearfix"></div>

            <div class="col-sm-6 nopadding">
            <?php echo $register_form['city']; ?>
            </div>
           <div class="clearfix"></div>

            <div class="col-sm-12 nopadding">
            <?php echo $register_form['address_1']; ?>
            </div>
            <div class="clearfix"></div>
            <?php echo $register_form['submit']; ?>
            <div class="clearfix"></div>
                </div>

            <?php echo $register_form['form_end']; ?>

                <div class="clearfix"></div>

                 </div>

            </div>
          </div>
        </div>
   </div>
     <!-- Signup (End) -->
  <script>
       var is_valid = 0;
       var ajax = null;
       var stop_postcode = 0;
       var INTERNATIONAL_STORE = '<?php echo $international_store; ?>';

       $('#otp_form').find('input, textarea').placeholderLabel();
       $('#login_form').find('input, textarea').placeholderLabel();
       $('#verify_otp_form').find('input, textarea').placeholderLabel();
       $('#verify_otp_forgot_form').find('input, textarea').placeholderLabel();
       $('#register_form').find('input, textarea').placeholderLabel();

       if(INTERNATIONAL_STORE == 1)
       {
         cc = $.parseJSON('<?php echo json_encode($this->mobile_country_code["CO"]); ?>');
         $('#select_country').attr('data-selected-country','CA');
       }
       else
       {
         cc = $.parseJSON('<?php echo json_encode($this->mobile_country_code["IN"]); ?>');
         $('#select_country').attr('data-selected-country','IN');
       }

       $('#select_country').flagStrap({
        countries:cc
       });

        $("#otp_form").submit(function(e) {
         e.preventDefault();
         $("#otp_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl         = '<?php echo $send_otp_url ?>';
         var country_code_text = $("#country_code option:selected" ).text();
         var country_code_val  = $("#country_code" ).val();
         var form_data         = $("#otp_form").serialize();
             form_data         = form_data.replace("country_code="+country_code_val, "country_code="+country_code_text+"&country_iso_code="+country_code_val);

        ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data) {

                    if(data['error'])
                    {
                     $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                     $("#collapseverify").css('visibility','hidden').hide();
                    }
                    else
                    {
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
                           else {
                             $("#collapseverify").css('visibility','hidden').hide();
                             $('#login_verify_popup').modal('hide');
                             if($.isNumeric( data['reg_telephone'] ))
                              { $("#reg_telephone_field").hide(); $("#reg_email_field").show(); }
                              else { $("#reg_telephone_field").show(); $("#reg_email_field").hide(); }
                             $('#signup_popup').modal('show');
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
                           $('#login_verify_popup').modal('hide');
                           $('#login_popup').modal('show');
                         }

                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
          e.stopImmediatePropagation();
          return false;
       });


       $("#verify_otp_form").submit(function(e) {
         e.preventDefault();
         $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = '<?php echo $verify_otp_form_url ?>';
         var form_data = $("#verify_otp_form").serialize();
       ajax =  $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {

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

                       if(data['redirect_cart']==0)
                       {
                        $('.login_mobile_success, .login_mobile_error').hide();
                        $("#collapseverify").css('visibility','hidden').hide();
                        $('#login_verify_popup').modal('hide');
                        $('#login_popup').modal('hide');
                          if($.isNumeric( data['reg_telephone'] ))
                            { $("#reg_telephone_field").hide(); $("#reg_email_field").show(); }
                          else { $("#reg_telephone_field").show(); $("#reg_email_field").hide(); }
                        $('#signup_popup').modal('show');
                      }
                      else
                      {
                        $('#login_verify_popup').modal('hide');
                        $('.cart_shopping_success').html(data['msg']);
                        $('#cart_shopping_popup').modal('show');

                        if(data['redirect_cart'] == 1)
                        {
                          $('#button-cart').trigger("click");
                        }
                        else
                        {
                          var redirect_cart =  data['redirect_cart'].split("-");
                          cart.add(redirect_cart[0], redirect_cart[1]);
                        }

                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
         e.stopImmediatePropagation();
         return false;
       });



       $("#verify_otp_forgot_form").submit(function(e) {
         e.preventDefault();
         $("#verify_otp_forgot_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = '<?php echo $verify_otp_forgot_form_url ?>';
         var form_data = $("#verify_otp_forgot_form").serialize();
        ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {

                    $("#verify_otp_forgot_form :input[name=otp]").val('');
                    $("#verify_otp_forgot_form :input[name=otp]").prev('label').css('margin-top', '20px');
                    if(data['error'])
                    {
                     $("#verify_otp_forgot_form input[type=submit]").val('verify').prop("disabled", false);
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                    }
                    else
                    {

                     if(data['redirect_cart'] == 0)
                      {
                        window.location.assign(data['url']);
                      }
                      else
                      {

                      $('#login_link_header, #footer-login').attr('href',data['url']);
                      $('#login_link_header, #footer-login').attr('data-target','');
                      $('#login_popup').modal('hide');
                      $('.cart_shopping_success').html(data['msg']);
                      $('#cart_shopping_popup').modal('show');

                        if(data['redirect_cart'] == 1)
                        {
                          $('#button-cart').trigger("click");
                        }
                        else
                        {
                          var redirect_cart =  data['redirect_cart'].split("-");
                          cart.add(redirect_cart[0], redirect_cart[1]);
                        }

                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
          e.stopImmediatePropagation();
          return false;
       });



       $("#login_form").submit(function(e) {
         e.preventDefault();
        $("#login_form input[type=submit]").val('loading...').prop("disabled", true);
         var actionurl = '<?php echo $login_form_url ?>';
         var form_data = $("#login_form").serialize();
         $("#login_form :input").prop("disabled", true);

        ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {

                    $("#login_form :input").prop("disabled", false);
                    if(data['error'])
                    {
                     $("#login_form input[type=submit]").val('continue').prop("disabled", false);
                     $('.login_password_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_password_error').show();
                     $('.login_password_success').hide();
                    }
                    else
                    {

                      if(data['redirect_cart']==0)
                      {
                        window.location.assign(data['url']);
                      }
                      else
                      {

                        $('#login_link_header, #footer-login').attr('href',data['url']);
                        $('#login_link_header, #footer-login').attr('data-target','');
                        $('#login_popup').modal('hide');
                        $('.cart_shopping_success').html(data['msg']);
                        $('#cart_shopping_popup').modal('show');

                        if(data['redirect_cart'] == 1)
                        {
                          $('#button-cart').trigger("click");
                        }
                        else
                        {
                          var redirect_cart =  data['redirect_cart'].split("-");
                          cart.add(redirect_cart[0], redirect_cart[1]);
                        }

                      }
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
          e.stopImmediatePropagation();
          return false;
       });

$( document ).ready(function() {

 if(INTERNATIONAL_STORE == 1)
 {
 var postcode_min = 2;
 var postcode_max = 10;
 }
 else
 {
 var postcode_min = 6;
 var postcode_max = 6;
 }        

    $.validator.addMethod("GSTNumberValid", function (textObj, element)
     {
         var entered_checksum_character = textObj.substr(-1);
         var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/;
         if (reggstin.test(textObj) == false) {
             return false;
         }
         
         var factor_even = 1;
         var factor_odd = 2;
         var sum = 0;
         var gst_number_array = textObj.split("");
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
         if(entered_checksum_character != calculated_checksum_letter) {
             return false;
         }
         return true;
     },
        "Please enter valid gst number."
     );


 if(INTERNATIONAL_STORE == 1)
 {

      $("#register_form").validate({
          rules: {
              reg_email: {
                 required: true,
                 email: true
              },
             password: {
                  required: true,
                  minlength: 4
              },
              country_id: {
                  required: true
              }
          },
          messages: {
              reg_email: "Please enter a valid email address!",
              password:  "Password must be between 4 and 20 characters!",
              country_id:"Please select country!"
          },
        success: function()
        {
          is_valid = 1;
        }
      });
}
else
{

      $("#register_form").validate({
          rules: {
              reg_telephone: {
                 required: true
              },
              reg_email: {
                 required: true,
                 email: true
              },
             password: {
                  required: true,
                  minlength: 4
              },
              name: {
                  required: true,
                  minlength: 2,
                  maxlength: 64
              },
              company: {
                  required: true,
                  minlength: 2,
                  maxlength: 64
              },
              gst_number: {
                  required: true,
                  GSTNumberValid: true
              },
              postcode: {
                  required: true,
                  minlength: 2,
                  maxlength: 10
              },
              address_1: {
                  required: true,
                  minlength: 3,
                  maxlength: 128
              }
          },
          messages: {
              reg_email: "Please enter a valid email address!",
              reg_telephone: "Please enter a valid mobile number!",
              password:  "Password must be between 4 and 20 characters!",
              name:      "Name must be between 2 and 64 characters!",
              company:   "Business name must be between 2 and 64 characters!",
              gst_number:"Please enter gst number!",
              postcode:  "Please enter valid postcode!",
              address_1: "Address must be between 3 and 128 characters!"
          },
        success: function()
        {
          is_valid = 1;
        }
      });
}

});
    $("#register_form").submit(function(e) {
      if(is_valid)
      {
         $("#register_form input[type=submit]").val('loading...').prop("disabled", true);
         var disabled = $("#country_id").attr('disabled');
         $("#country_id, #zone_id, #city").prop("disabled", false);
         e.preventDefault();
         var actionurl = '<?php echo $register_form_url ?>';
         var form_data = $("#register_form").serialize();
        ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {
                    $("#register_form input[type=submit]").val('continue').prop("disabled", false);
                    $("#country_id, #zone_id, #city").prop("disabled", disabled);
                    if(data['error'])
                    {
                     $('.login_register_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_register_error').show();
                     $('.login_register_success').hide();
                    }
                    else
                    {
                        window.location.assign(data['url']);
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
         e.stopImmediatePropagation();
         return false;
       }
       else
       {
        return false;
       }
    });


       $("#postcode").blur(function(e) {
        e.preventDefault();
        if($(this).val() == "") { $("#postcode").prev('label').animate({'margin-top': '20px'}); }
       
       if($(this).val().length == 6)
       {
         ajax = $.ajax({
                url: "<?php echo $this->url->link('common/login_popup/pincodeAddress', '', 'SSL'); ?>",
                type: 'post',
                dataType: 'json',
                data: { postcode: $("#postcode").val() },
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {
                  if(!data['error'])
                  {
                    if(data['zone_id'] == '')
                    {
                      $("#country_id option[value='99'").prop('selected',true);
                      get_state_list();
                      $("#city").val('');
                      $("#city").prev('label').css('margin-top', '20px');
                    }
                    else
                    {
                      $("#city").prev('label').css('margin-top', '0px');
                      $("#city").val(data['city']);
                      $("#country_id option[value=" + data['country_id'] + "]").prop("selected",true);
                       get_state_list(data['zone_id']);
                    }
                  }
                  else
                  {
                    $("#country_id, #zone_id, #city").prop("disabled", true);
                  }
                },
               complete: function(data){
                var ajax = null;
               }
         });
        }

          e.stopImmediatePropagation();
          return false;
       });


       $("#country_id").change(function(e) {
         e.preventDefault();
        get_state_list();
        e.stopImmediatePropagation();
        return false;
       });

    function  get_state_list(zone_id=0) {
         ajax = $.ajax({
                url: "<?php echo $this->url->link('common/login_popup/state_list', '', 'SSL'); ?>",
                type: 'post',
                dataType: 'json',
                data: { country_id: $("#country_id").val() },
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                  $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                success: function(data)
                {
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


     $(document).delegate('.send_otp_agin', 'click', function(e){
           $('.login_mobile_success, .login_mobile_error').hide();
           $("#verify_otp_form input[name=otp]").val('');
           $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px');
           e.preventDefault();
           $('#otp_form').submit();
      });

      $(document).delegate('#gst_uin_number', 'click', function(e){
          if($(this).prop("checked") == true){
           $("#gst_uin_number_area").removeClass("hide");
           }
          else { 
           $("#gst_uin_number_area").addClass("hide");
           $('#gst_number').val('');
           $("#gst_number").prev('label').animate({'margin-top': '20px'});
           $("#gst_number-error").hide();
          }
      });  

     $(document).delegate('#login_link_header, #footer-login, #dropshipper_login_link, #button-cart, #wishlist-total, .addtocart_bottem, .addtocart_new, .button-cart-list, .addtocart', 'click', function(e)
     {
           stop_postcode = 1;

           if($(this).attr('id') == 'dropshipper_login_link') { $('#input-is_dropshipper').val(2); } 
           else { $('#input-is_dropshipper').val(0); } 
           
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
           $('#register_form').find('input, textarea').placeholderLabel();
           $("label.error").remove();
           if(getCookie("customer_mobile") != '')
           {
              $(".login_mobile_nmr").html(getCookie("country_code")+' '+getCookie("customer_mobile"));
              $("#reg_telephone").val(getCookie("customer_mobile"));
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


        $('#reg_telephone_signup').keyup(function(e)
         {
            if (this.value.charAt(0) == 0 )
            {
              this.value = this.value.slice(1);;
            }
            if (/\D/g.test(this.value))
            {
             var node = $(this);
             node.val(node.val().replace(/[^0-9]/g,'') );
            }
          });


       $('#reg_telephone').keyup(function(e)
         {
            check_reg_telephone();
        });

       $('#reg_telephone').change(function(e)
         {
            check_reg_telephone();
        });

         $('input[name="name"], input[name="city"]').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z ]/g,'') );
          });

           $('input[name=otp]').keyup(function(e)
         {
            if (/\D/g.test(this.value))
            {
             var node = $(this);
             node.val(node.val().replace(/[^0-9]/g,'') );
            }
          });



          function check_reg_telephone()
          {
               var mobile       = $('#reg_telephone').val();
               var country_code = $('#country_code').val();
               var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
               $(".flagstrap").hide();

           if(INTERNATIONAL_STORE == 1)
           {   
               if (mobile != '' && /\D/g.test(mobile))
                 {
                   //$('#reg_telephone').prev("label").html("Email");
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

                if (mobile != '' && /\D/g.test(mobile))
                 {
                   //$('#reg_telephone').prev("label").html("Email");
                   $(".flagstrap").hide();
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

  function edit_login_no()
    {   
        $("#edit_reg_telephone").hide();
        $("#save_reg_telephone, #send_otp_title").show();
        $('.login_mobile_success, .login_mobile_error').hide(500);
        $("#verify_otp_form :input[name=otp]").val('');
        $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
        $("#collapseverify").hide();
        check_reg_telephone();
        $('#login_popup').modal('hide');
        $('.login_mobile_success, .login_mobile_error').hide();
        $("#collapseverify").css('visibility','hidden').hide();
        $('#login_verify_popup').modal('show');
    }


 var ajax = null;
 function send_otp()
    {
          $('.login_passworderror, .login_password_success').hide();
          $("#collapseverify_1").css('visibility','hidden').hide();
          $("#collapseverify_2").css('visibility','visible').show();
          $("#login_form :input[name=password]").val('');
          $("#login_form :input[name=password]").prev('label').css('margin-top', '15px');
          $("#verify_otp_forgot_form input[name=otp]").val('');
          $("#verify_otp_forgot_form input[name=otp]").prev('label').css('margin-top', '15px');

        ajax = $.ajax({
                url: "<?php echo $this->url->link('common/login_popup/send_password_otp', '', 'SSL'); ?>",
                type: 'post',
                dataType: 'json',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {
                   $('.login_password_success').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                   $('.login_password_success').show();
                   $('.login_password_error').hide();
                },
               complete: function(data){
                var ajax = null;
               }
            });
    }

function login_with_password()
   {
    $('.login_passworderror, .login_password_success').hide();
    $('#collapseverify_2').css('visibility','hidden').hide();
    $('#collapseverify_1').css('visibility','visible').show();
    $("#verify_otp_forgot_form :input[name=otp]").val('');
    $("#verify_otp_forgot_form :input[name=otp]").prev('label').css('margin-top', '15px');
  }


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



$(document).ready(function(){
<?php if(isset($otp_verify_success) && $otp_verify_success==0){ ?>
  $('.login_mobile_error').html("Invalid verify link");
  $('.login_mobile_error').show();
  $('#login_verify_popup').modal('show');
  check_reg_telephone();
<?php } else if(isset($otp_verify_success) && $otp_verify_success==1){ ?>
  $('#signup_popup').modal('show');
<?php } else if(isset($otp_verify_success) && $otp_verify_success==2){ ?>
  $('.login_mobile_error').html("Warning: No match for Mobile Number and/or Password");
  $('.login_mobile_error').show();
  $('#login_verify_popup').modal('show');
  check_reg_telephone();
<?php } else if(isset($otp_verify_success) && $otp_verify_success==3){ ?>
  $('#login_verify_popup').modal('show');
  check_reg_telephone();
<?php } ?>
});

  </script>
