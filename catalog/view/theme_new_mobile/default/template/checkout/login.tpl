<?php echo $header; ?>
<div class="container">
  <div class="row">
    <div id="content" class="">
      <div class="checkout-pages-block">
        <div id="accordion" class="panel-group">
          <div class="panel panel-default">
            <div class="checkout-login-page">
              <ul class="nav nav-tabs">
                <li class="active signin"><a data-value="log_in" class="common-form login_show" href="#sign_in">Log in</a></li>
                <li class="signup"><a data-value="sign_in" class="common-form" href="#sign_up">Sign up</a></li>
                <li class="forgot_hidden"><a href="#forgot_password_show" class="common-form forgot_password_show" data-value="forgot_password">Forgot Password</a></li>
              </ul>
              <div class="tab-content">
                <?php if ($success) { ?>
                <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
                <?php } ?>
                <?php if ($error_warning) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
                <?php } ?>
                <div class="tab-pane fade in active" id="sign_in">
                  <div id="collapse-checkout-option">
                    <!--<h2><?php echo $text_returning_customer; ?></h2>
                  <p><?php echo $text_i_am_returning_customer; ?></p>  -->
                    <div class="form-group">
                      <label class="control-label" for="input-email"><?php echo $entry_email; ?><?php //echo $entry_mobile_or; ?></label>
                      <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?><?php //echo $entry_mobile_or; ?>" id="input-email" class="form-control log_email" />
                      <div class="log-email-error error"></div>
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                      <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control log_password" />
                      <div class="log-password-error error"></div>
                      <!--<a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>-->
                      <a href="javascript:void(0);<?php //echo $forgotten; ?>" id="forgot_password" class="forgot_password_link forgot-redirect"><?php echo $text_forgotten; ?></a>
                    </div>
                    <div class="buttons">
                      <input type="button" value="<?php echo $button_login; ?>" id="button-login" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary checkout_login_button" />
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="sign_up">
                  <form class="checkout_reg" method="">
                  <div id="collapse-checkout-option">
                    <fieldset id="account">
                      <legend><?php echo $text_your_details; ?></legend>
                      <div class="form-group required">
                        <div class="col-sm-6">
                          <label class="control-label" for="input-payment-firstname"><?php echo $entry_firstname; ?></label>
                          <input type="text" name="name" value="" placeholder="<?php echo $entry_firstname; ?>" id="input-payment-firstname" class="form-control reg_name" />
                          <div class="reg-name-error error"></div>
                        </div>
                        <div class="col-sm-6">
                          <label class="control-label" for="input-payment-telephone"><?php echo $entry_telephone; ?></label>
                          <input type="text" name="reg_telephone" value="" placeholder="<?php echo $entry_telephone; ?>" id="input-payment-telephone" class="form-control" />
                          <div class="reg-mobile-error error"></div>
                        </div>
                      </div>

                      <!--<div class="form-group required">
                        <label class="control-label" for="input-payment-telephone"><?php echo $entry_telephone; ?></label>
                        <input type="text" name="telephone" value="" placeholder="<?php echo $entry_telephone; ?>" id="input-payment-telephone" class="form-control" />
                      </div>  -->

                      <div class="form-group">
                        <div class="col-sm-6">
                          <label class="control-label" for="input-telephone"></label>
                          <input type="checkbox" id="showWhatsAppNumber" value="WhatsApp Number" /><?php echo $text_mob_pop;?>
                        </div>

                        <div class="col-sm-6" id="showWhatsAppNumberBox" style="display:none;">
                          <label class="control-label" for="input-telephone"><?php echo $entry_whatsapp_telephone; ?></label>
                          <input type="tel" id="input-whatsapp-telephone" name="whatsapp_telephone" placeholder="ENTER YOUR WHATSAPP NUMBER" class="form-control" />
                        </div>
                      </div>

                      <!--
                      <div class="form-group" id="showWhatsAppNumberBox" style="display:none;">
                        <label class="control-label" for="input-telephone"><?php echo $entry_whatsapp_telephone; ?></label>
                        <input type="tel" id="input-whatsapp-telephone" name="whatsapp_telephone" placeholder="ENTER YOUR WHATSAPP NUMBER" class="form-control" />
                      </div>  -->

                      <div class="form-group">
                        <div class="col-sm-6">
                          <label class="control-label" for="input-payment-email"><?php echo $entry_email_address; ?></label>
                          <input type="text" name="reg_email" value="" placeholder="<?php echo $entry_email_address; ?>" id="input-payment-email" class="form-control" />
                        </div>
                      </div>
                      
                      <input type="hidden" name="is_dropshipper" value="<?php echo $is_dropshipper; ?>" id="input-is_dropshipper" class="form-control" />

                    </fieldset>
                    <fieldset>
                      <!--<legend><?php echo $text_your_password; ?></legend> -->
                      <div class="form-group required">
                        <div class="col-sm-6">
                          <label class="control-label" for="input-payment-password"><?php echo $entry_password; ?></label>
                          <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="input-payment-password" class="form-control" />
                        </div>
                        <div class="col-sm-6">
                          <label class="control-label" for="input-payment-confirm"><?php echo $entry_confirm; ?></label>
                          <input type="text" name="confirm" value="" placeholder="<?php echo $entry_confirm; ?>" id="input-payment-confirm" class="form-control" />
                        </div>


                      </div>

                      <!-- <div class="form-group required">
                        <label class="control-label" for="input-payment-confirm"><?php echo $entry_confirm; ?></label>
                        <input type="text" name="confirm" value="" placeholder="<?php echo $entry_confirm; ?>" id="input-payment-confirm" class="form-control" />
                      </div>  -->
                    </fieldset>
                    <fieldset>
                      <label class="control-label"><?php echo $show_pass; ?></label>
                      <input type="checkbox" name="show_pass" id="show_pass" />
                    </fieldset>
                    </div>
                  <?php if ($text_agree) { ?>
                  <div class="buttons clearfix">
                    <?php echo $text_agree; ?> &nbsp;
                    <input type="checkbox" name="agree" value="1" />
                    <input type="submit" value="<?php echo $button_continue; ?>" id="button-register" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary checkout_reg_button" />
                  </div>
                  <?php } else { ?>
                  <div class="buttons clearfix">
                    <input type="submit" value="<?php echo $button_continue; ?>" id="button-register" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary checkout_reg_button" />
                  </div>
                  <?php } ?>
                  </form>
                </div>
                <div id="forgot_password_show" class="tab-pane fade forgot_password ">
                  <div class="buttons">
                    <div class="pull-left btn btn-default forgot_back_button"><?php echo $button_back; ?></a></div>
                  </div>
                  <h3><?php echo $heading_text_forget_password; ?></h3>
                  <!--<p><?php echo $text_email; ?></p> -->
                  <form action="<?php echo $forgot_action; ?>" method="post" enctype="multipart/form-data" class="">
                    <fieldset id="forgotten">
                      <legend><?php //echo $text_your_email; ?></legend>
                      <div class="form-group required">
                        <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                        <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control forget-email" />
                        <div class="forgot-password-error error"></div>
                      </div>
                    </fieldset>
                    <div class="form-group required">
                      <div class="buttons">
                        <!--<div class="pull-left"><a href="<?php //echo $back; ?>" class="btn btn-default"><?php //echo $button_back; ?></a></div> -->
                        <input type="hidden" name="form_button" value="forget_button"/>
                        <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary forgot_submit_button" />
                      </div>
                    </div>
                  </form>
                </div>
                <!--
                <div class="col-sm-6">
                  <h2><?php echo $text_new_customer; ?></h2>
                  <p><?php echo $text_checkout; ?></p>
                  <div class="radio">
                    <label>
                      <?php if ($account == 'register') { ?>
                      <input type="radio" name="account" value="register" checked="checked" />
                      <?php } else { ?>
                      <input type="radio" name="account" value="register" />
                      <?php } ?>
                      <?php echo $text_register; ?></label>
                  </div>
                  <?php if ($checkout_guest) { ?>
                  <div class="radio">
                    <label>
                      <?php if ($account == 'guest') { ?>
                      <input type="radio" name="account" value="guest" checked="checked" />
                      <?php } else { ?>
                      <input type="radio" name="account" value="guest" />
                      <?php } ?>
                      <?php echo $text_guest; ?></label>
                  </div>
                  <?php } ?>
                  <p><?php echo $text_register_account; ?></p>
                  <input type="button" value="<?php echo $button_continue; ?>" id="button-account" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
                </div>
                <div class="col-sm-6">
                  <h2><?php echo $text_returning_customer; ?></h2>
                  <p><?php echo $text_i_am_returning_customer; ?></p>
                  <div class="form-group">
                    <label class="control-label" for="input-email"><?php echo $entry_email; ?><?php echo $entry_mobile_or; ?></label>
                    <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?><?php echo $entry_mobile_or; ?>" id="input-email" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                    <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
                    <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></div>
                  <input type="button" value="<?php echo $button_login; ?>" id="button-login" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
                </div>  -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="order-summary-block">
        <div class="order-summary-show">
          <div class="order-summary">
            <h3 class="order-summary-title"><?php echo $price_details; ?></h3>
          </div>
          <div class="order-list"><?php echo $order_summary_list; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>


<script>
  $(document).ready(function(){
    $(".nav-tabs a").click(function(e){
      e.preventDefault();
      $(this).tab('show');
    });



    $('.nav-tabs li').click(function(){
      $(this).removeClass('active').addClass('active');
    });



    var url = document.location.toString();
    if (url.match('#')) {
      $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
    }
  });


</script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#showWhatsAppNumber").click(function(){
      $("#showWhatsAppNumberBox").toggle();
    });
  });

  $(document).delegate ("#show_pass","click",function(){
    if($(this).prop("checked") == true){
      $("#input-payment-password").attr("type","text");
    }else{
      $("#input-payment-password").attr("type","password");
    }
  });

  $(document).ready(function(){
    $(document).delegate('.forgot-redirect', 'click', function(){
      $('a.forgot_password_show').trigger('click');
      $('.alert-success').hide();
      $('.alert-danger').hide();
      $('.signin a').css({'background':'#FFF','color':'#12339C'}) ;
    });

    $('.signin').click(function(){
      $('.alert-success').hide();
      $('.alert-danger').hide();
      $('.signin a').css({'background':'#FFF','color':'#12339C'}) ;
    });

    $('.signup').click(function(){
      $('.alert-success').hide();
      $('.alert-danger').hide();
      $('.signin a').css({'background':'#F03140','color':'#fff'}) ;
    });
  });

  $(document).ready(function(){
    //validation for login
    $('.checkout_login_button').click(function(e){
      flag = 0;
      //e.preventDefault();
      if($('.log_email').val()==''){
        $('.log-email-error').text('Please enter your email-id or mobile no.');
        flag = 1;
      }
      if($('.log_password').val()==''){
        $('.log-password-error').text('Please enter your password.');
        flag = 1;
      }
      if(flag){
        return false;
      }
    });

    //validation for registration
    $('.reg_button_submit').click(function(e){
      //e.preventDefault();
      flag = 0;
      if($('.reg_name').val()==''){
        $('.reg-name-error').text('Please enter your name.');
        flag = 1;
      }
      if($('.reg_mobile').val()==''){
        $('.reg-mobile-error').text('Please enter your mobile no.');
        flag = 1;
      }
      /*
       if($('.reg_address_1').val()==''){
       $('.reg-address-1-error').text('Please enter your address.');
       flag = 1;
       }
       if($('.reg_city').val()==''){
       $('.reg-city-error').text('Please enter your city.');
       flag = 1;
       }
       if($('.reg_region').val()==''){
       $('.reg-region-error').text('Please enter your region/state.');
       flag = 1;
       }
       */
      if($('.reg_password').val()==''){
        $('.reg-password-error').text('Please enter your password.');
        flag = 1;
      }
      if($('.reg_cm_password').val()==''){
        $('.reg-cm-password-error').text('Please enter your confirm password.');
        flag = 1;
      }
      if(flag){
        return false;
      }

    });

    //validation for forgot password
    $('.forgot_submit_button').click(function(e){
      //e.preventDefault();
      flag = 0;
      if($('.forget-email').val()==''){
        $('.forgot-password-error').text('Please enter your password.');
        flag = 1;
      }

      if(flag){
        return false;
      }

    });


    $(document).delegate('.forgot_back_button', 'click', function(){
      $('a.login_show').trigger('click');
    });


  });
</script>
<script>
  // Login
  $(document).delegate('#button-login', 'click', function() {
    $.ajax({
      url: 'index.php?route=checkout/login/save',
      type: 'post',
      data: $('#collapse-checkout-option :input'),
      dataType: 'json',
      success: function(json) {
        $('.alert, .text-danger').remove();
        $('.form-group').removeClass('has-error');

        if (json['redirect']) {
          location = json['redirect'];
        } else if (json['error']) {
          $('#sign_in #collapse-checkout-option').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

          // Highlight any found errors
          $('#sign_in input[name=\'email\']').parent().addClass('has-error');
          $('#sign_in input[name=\'password\']').parent().addClass('has-error');
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  });
</script>
<script type="text/javascript">
  // Register
  // validate signup form on keyup and submit
  $(".checkout_reg").validate({
    rules: {
      name: "required",
      reg_telephone: "required",
      password: {
        required: true,
        minlength: 4
      },
      confirm: {
        required: true,
        minlength: 4,
        equalTo: "#input-payment-password"
      },
      reg_email: {
        required: true,
        email: true
      },
    },
    messages: {
      name: "Please enter your name !",
      reg_telephone: "Please enter your mobile number !",
      password: {
        required: "Please enter your password !",
        minlength: "Password must be between 4 and 20 characters !"
      },
      confirm: {
        required: "Please enter your confirm password !",
        minlength: "Password must be between 4 and 20 characters !",
        equalTo: "Please enter the same password as above !"
      },
      reg_email: "Please enter a valid email address !",
    },
    submitHandler: function(){
      $.ajax({
        url: 'index.php?route=checkout/register/save',
        type: 'post',
        data: $('#sign_up #collapse-checkout-option input[type=\'text\'], #sign_up #collapse-checkout-option input[type=\'password\'], #sign_up #collapse-checkout-option input[type=\'hidden\'], #sign_up #collapse-checkout-option input[type=\'checkbox\']:checked'),
        dataType: 'json',
        beforeSend: function() {
          $('#button-register').button('loading');
        },
        complete: function() {
          $('#button-register').button('reset');
        },
        success: function(json) {
          $('.alert, .text-danger').remove();
          $('.form-group').removeClass('has-error');

          if (json['redirect']) {
            location = json['redirect'];
          } else if (json['error']) {
            if (json['error']['warning']) {
              $('#sign_up #collapse-checkout-option').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }

            for (i in json['error']) {
              var element = $('#input-payment-' + i.replace('_', '-'));

              if ($(element).parent().hasClass('input-group')) {
                $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
              } else {
                $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
              }
            }

            // Highlight any found errors
            $('.text-danger').parent().addClass('has-error');
          }

        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    },
  });


  // Register
   /*$(document).delegate('#button-register', 'click', function() {
    $.ajax({
   url: 'index.php?route=checkout/register/save',
   type: 'post',
   data: $('#sign_up #collapse-checkout-option input[type=\'text\'], #sign_up #collapse-checkout-option input[type=\'password\'], #sign_up #collapse-checkout-option input[type=\'hidden\'], #sign_up #collapse-checkout-option input[type=\'checkbox\']:checked'),
   dataType: 'json',
   beforeSend: function() {
   $('#button-register').button('loading');
   },
   complete: function() {
   $('#button-register').button('reset');
   },
   success: function(json) {
   $('.alert, .text-danger').remove();
   $('.form-group').removeClass('has-error');

   if (json['redirect']) {
   location = json['redirect'];
   } else if (json['error']) {
   if (json['error']['warning']) {
   $('#sign_up #collapse-checkout-option').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
   }

   for (i in json['error']) {
   var element = $('#input-payment-' + i.replace('_', '-'));

   if ($(element).parent().hasClass('input-group')) {
   $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
   } else {
   $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
   }
   }

   // Highlight any found errors
   $('.text-danger').parent().addClass('has-error');
   }

   },
   error: function(xhr, ajaxOptions, thrownError) {
   alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
   }
   });
   });
   */
</script>
<!--<script>
  // Register
  $(document).delegate('#button-register', 'click', function() {
    $.ajax({
      url: 'index.php?route=checkout/register/save',
      type: 'post',
      data: $('#sign_up #collapse-checkout-option input[type=\'text\'], #sign_up #collapse-checkout-option input[type=\'password\'], #sign_up #collapse-checkout-option input[type=\'hidden\'], #sign_up #collapse-checkout-option input[type=\'checkbox\']:checked'),
      dataType: 'json',
      success: function(json) {
        $('.alert, .text-danger').remove();
        $('.form-group').removeClass('has-error');

        if (json['redirect']) {
          location = json['redirect'];
        } else if (json['error']) {
          if (json['error']['warning']) {
            $('#sign_up #collapse-checkout-option').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
          }

          for (i in json['error']) {
            var element = $('#input-payment-' + i.replace('_', '-'));

            if ($(element).parent().hasClass('input-group')) {
              $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
            } else {
              $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
            }
          }

          // Highlight any found errors
          $('.text-danger').parent().addClass('has-error');
        } else {
        <?php if ($shipping_required) { ?>
            var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').prop('value');

            if (shipping_address) {
              $.ajax({
                url: 'index.php?route=checkout/shipping_method',
                dataType: 'html',
                success: function(html) {
                  // Add the shipping address
                  $.ajax({
                    url: 'index.php?route=checkout/shipping_address',
                    dataType: 'html',
                    success: function(html) {
                      $('#collapse-shipping-address .panel-body').html(html);

                      //$('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> <i class="fa fa-caret-down"></i></a>');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                  });

                  $('#collapse-shipping-method .panel-body').html(html);

                  //$('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_method; ?> <i class="fa fa-caret-down"></i></a>');

                  $('a[href=\'#collapse-shipping-method\']').trigger('click');

                  //$('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_shipping_method; ?>');
                  //$('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                  //$('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
              });
            } else {
              $.ajax({
                url: 'index.php?route=checkout/shipping_address',
                dataType: 'html',
                success: function(html) {
                  $('#collapse-shipping-address .panel-body').html(html);

                  //$('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> <i class="fa fa-caret-down"></i></a>');

                  $('a[href=\'#collapse-shipping-address\']').trigger('click');

                  //$('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_shipping_method; ?>');
                  //$('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                  //$('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
              });
            }
          <?php } else { ?>
            $.ajax({
              url: 'index.php?route=checkout/payment_method',
              dataType: 'html',
              success: function(html) {
                $('#collapse-payment-method .panel-body').html(html);

                //$('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_method; ?> <i class="fa fa-caret-down"></i></a>');

                $('a[href=\'#collapse-payment-method\']').trigger('click');

                //$('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
            });
          <?php } ?>

          /*$.ajax({
            url: 'index.php?route=checkout/payment_address',
            dataType: 'html',
            success: function(html) {
              $('#collapse-payment-address .panel-body').html(html);

              $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_address; ?> <i class="fa fa-caret-down"></i></a>');
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });*/
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  });
</script> -->