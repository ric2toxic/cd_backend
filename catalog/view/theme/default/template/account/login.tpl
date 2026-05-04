<?php echo $header; ?>
<div class="container">
  <div class="breadcrumb"></div>
  <?php /* ?>
  <!--
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  -->
  <?php */ ?>
  <div class="row account-page"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php //echo $class; ?>"><?php echo $content_top; ?>
      <div class="well">
        <ul class="nav nav-tabs">
          <li class="active signin" style="width:100%;"><a href="#sign_in" class="common-form login_show" data-value="log_in">Log in</a></li>
          <!-- <li class="signup"><a href="#sign_up" class="common-form" data-value="sign_in">Sign up</a></li> -->
          <li class="forgot_hidden"><a href="#forgot_password_show" class="common-form forgot_password_show" data-value="forgot_password">Forgot Password</a></li>
        </ul>
          <?php if ($success) { ?>
          <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
          <?php } ?>
          <?php if ($error_warning) { ?>
          <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
          <?php } ?>
          <div class="tab-content">
          <div id="sign_in" class="tab-pane fade in active">
            <!--<h2><?php echo $text_returning_customer; ?></h2>
            <p><strong><?php echo $text_i_am_returning_customer; ?></strong></p> -->
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?><?php //echo $entry_mobile_or; ?></label>
                <input type="text" name="email" value="<?php echo $login_email; ?>" placeholder="<?php echo $entry_email; ?><?php //echo $entry_mobile_or; ?> " id="input-email" class="form-control log_email" />
                <div class="log-email-error error"></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control log_password" />
                <div class="log-password-error error"></div>
                <a href="javascript:void(0);<?php //echo $forgotten; ?>" id="forgot_password" class="forgot_password_link forgot-redirect"><?php echo $text_forgotten; ?></a>
              </div>
              <div class="buttons">
                <input type="hidden" name="form_button" value="log-button"/>
                <input type="hidden" name="referrers" value="<?php echo $referrers; ?>"/>
                <input type="submit" value="<?php echo $button_login; ?>" class="btn btn-primary log_submit_button" data-name="#sign_in"/>
                <?php if ($redirect) { ?>
                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <?php } ?>
              </div>
            </form>
          </div>


          <div id="sign_up" class="tab-pane fade">
            <form id="signupForm" action="<?php echo $reg_action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
              <fieldset id="account">
                <div class="form-group required">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                    <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control reg_name" />
                    <?php if ($error_name) { ?>
                    <div class="text-danger "><?php echo $error_name; ?></div>
                    <?php } ?>
                    <div class="reg-name-error error"></div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                    <input type="tel" name="reg_telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control reg_mobile" />
                    <?php if ($error_telephone) { ?>
                    <div class="text-danger"><?php echo $error_telephone; ?></div>
                    <?php } ?>
                    <div class="reg-mobile-error error"></div>
                  </div>
                </div>

                <!--<div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                  <div class="col-sm-10">
                    <input type="tel" name="reg_telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control reg_mobile" />
                    <?php if ($error_telephone) { ?>
                    <div class="text-danger"><?php echo $error_telephone; ?></div>
                    <?php } ?>
                    <div class="reg-mobile-error error"></div>
                  </div>
                </div>  -->
                <div class="form-group">
                  <!-- <label class="col-sm-2 control-label" for="input-telephone"></label>  -->
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
                  <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_whatsapp_telephone; ?></label>
                  <div class="col-sm-10">
                    <input type="tel" id="input-whatsapp-telephone" name="whatsapp_telephone" placeholder="ENTER YOUR WHATSAPP NUMBER" class="form-control" />
                  </div>
                </div>
                -->


                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-email"><?php echo $entry_email_address; ?></label>
                    <input type="email" name="reg_email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email_address; ?>" id="input-email" class="form-control" />
                    <?php if ($error_email) { ?>
                    <div class="text-danger"><?php echo $error_email; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <!-- dropshipper hidden field -->
                <input type="hidden" name="is_dropshipper" value="<?php echo $is_dropshipper; ?>" id="input-is_dropshipper" class="form-control" />

              </fieldset>
           <!--   <fieldset id="address">
                <legend><?php echo $text_your_address; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-company"><?php echo $entry_company; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="company" value="<?php echo $company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                  <div class="col-sm-10">
                    <input type="tel" name="address_telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-address-telephone" class="form-control" />
                  </div>
                </div>

                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-address-1"><?php echo $entry_address_1; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control reg_address_1" />
                    <?php if ($error_address_1) { ?>
                    <div class="text-danger"><?php echo $error_address_1; ?></div>
                    <?php } ?>
                    <div class="reg-address-1-error error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-address-2"><?php echo $entry_address_2; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2" class="form-control" />
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control reg_city" />
                    <?php if ($error_city) { ?>
                    <div class="text-danger"><?php echo $error_city; ?></div>
                    <?php } ?>
                    <div class="reg-city-error error"></div>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" />
                    <?php if ($error_postcode) { ?>
                    <div class="text-danger"><?php echo $error_postcode; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <?php /* ?>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
                  <div class="col-sm-10">
                    <select name="country_id" id="input-country" class="form-control">
                      <option value=""><?php echo $text_select; ?></option>
                      <?php foreach ($countries as $country) { ?>
                      <?php if ($country['country_id'] == $country_id) { ?>
                      <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                    <?php if ($error_country) { ?>
                    <div class="text-danger"><?php echo $error_country; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <?php */ ?>

                <input type="hidden" value="99" name="country_id" id="input-country" />

                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-zone"><?php echo $entry_zone; ?></label>
                  <div class="col-sm-10">
                    <select name="zone_id" id="input-zone" class="form-control reg_region">
                    </select>
                    <?php if ($error_zone) { ?>
                    <div class="text-danger"><?php echo $error_zone; ?></div>
                    <?php } ?>
                    <div class="reg-region-error error"></div>
                  </div>
                </div>
                
              </fieldset>  -->
              <fieldset id="account-password">
                <!--<legend><?php echo $text_your_password; ?></legend>  -->
                <div class="form-group required">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                    <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control reg_password" />
                    <?php if ($error_password) { ?>
                    <div class="text-danger"><?php echo $error_password; ?></div>
                    <?php } ?>
                    <div class="reg-password-error error"></div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-confirm"><?php echo $entry_confirm; ?></label>
                    <input type="password" name="confirm" value="<?php echo $confirm; ?>" placeholder="<?php echo $entry_confirm; ?>" id="input-confirm" class="form-control reg_cm_password" />
                    <?php if ($error_confirm) { ?>
                    <div class="text-danger"><?php echo $error_confirm; ?></div>
                    <?php } ?>
                    <div class="reg-cm-password-error error"></div>
                  </div>
                </div>
                <!--
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_confirm; ?></label>
                  <div class="col-sm-10">
                    <input type="password" name="confirm" value="<?php echo $confirm; ?>" placeholder="<?php echo $entry_confirm; ?>" id="input-confirm" class="form-control reg_cm_password" />
                    <?php if ($error_confirm) { ?>
                    <div class="text-danger"><?php echo $error_confirm; ?></div>
                    <?php } ?>
                    <div class="reg-cm-password-error error"></div>
                  </div>
                </div>
                -->
              </fieldset>

              <?php if ($text_agree) { ?>
              <div class="buttons">
                <?php echo $text_agree; ?>
                  <?php if ($agree) { ?>
                  <input type="checkbox" name="agree" value="1" checked="checked" />
                  <?php } else { ?>
                  <input type="checkbox" name="agree" value="1" checked="checked" />
                  <?php } ?>
                  &nbsp;
                  <input type="hidden" name="form_button" value="reg_button"/>
                  <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary reg_button_submit" />
              </div>
              <?php } else { ?>
              <div class="buttons">
                  <input type="hidden" name="referrers" value="<?php echo $referrers; ?>"/>
                  <input type="hidden" name="form_button" value="reg_button"/>
                  <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary reg_button_submit" />
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
            <form action="<?php //echo $forgot_action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
              <fieldset id="forgotten" class="col-sm-12">
                <legend><?php //echo $text_your_email; ?></legend>
                <div class="form-group required">
                  <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                  <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control forget-email" />
                  <div class="forgot-password-error error"></div>
                </div>
              </fieldset>
              <div class="buttons">
                <!--<div class="pull-left"><a href="<?php //echo $back; ?>" class="btn btn-default"><?php //echo $button_back; ?></a></div> -->
                <input type="hidden" name="form_button" value="forget_button"/>
                <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary forgot_submit_button" />
                <!--<a href="javascript:void(0);" class="btn btn-default" id="forgot_back_button">
                  <?php echo $button_back; ?>
                </a> -->
              </div>
            </form>
          </div>
        </div>
      </div>
      <!--<div id="forgot_password_show" class="col-sm-12">
        <h1><?php echo $heading_text_forget_password; ?></h1>
        <p><?php echo $text_email; ?></p>
        <form action="<?php echo $forgot_action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
          <fieldset id="forgotten">
            <legend><?php echo $text_your_email; ?></legend>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
              <div class="col-sm-10">
                <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control forget-email" />
                <div class="forgot-password-error error"></div>
              </div>
            </div>
          </fieldset>
          <div class="buttons clearfix">
            <!--<div class="pull-left"><a href="<?php //echo $back; ?>" class="btn btn-default"><?php //echo $button_back; ?></a></div>
            <div class="pull-left"><a href="javascript:void(0);" class="btn btn-default" id="forgot_back_button"><?php echo $button_back; ?></a></div>
            <div class="pull-right">
              <input type="hidden" name="form_button" value="forget_button"/>
              <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary forgot_submit_button" />
            </div>
          </div>
        </form>
      </div>  -->
      <?php /* ?>
      <!--
      <div class="col-sm-6">
        <div class="well">
          <h2><?php echo $text_new_customer; ?></h2>
          <p><strong><?php echo $text_register; ?></strong></p>
          <p><?php echo $text_register_account; ?></p>
          <a href="<?php echo $register; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="well">
          <h2><?php echo $text_returning_customer; ?></h2>
          <p><strong><?php echo $text_i_am_returning_customer; ?></strong></p>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label class="control-label" for="input-email"><?php echo $entry_email; ?><?php echo $entry_mobile_or; ?></label>
              <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?><?php echo $entry_mobile_or; ?> " id="input-email" class="form-control" />
            </div>
            <div class="form-group">
              <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
              <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
              <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></div>
            <input type="submit" value="<?php echo $button_login; ?>" class="btn btn-primary" />
            <?php if ($redirect) { ?>
            <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
            <?php } ?>
          </form>
        </div>
      </div> -->
      <?php */ ?>
      <?php echo $content_bottom; ?>
    </div>
    <!--<div class="col-sm-3 column-right">
      <div class="account-right-content">
        <p>"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit..."</p>
      </div>
    </div>   -->
    <?php //echo $column_right; ?>
  </div>
</div>
<script>
  $(document).ready(function(){

      $(".nav-tabs a").click(function(e){
        e.preventDefault();
        $(this).tab('show');
      });

      $("#showWhatsAppNumber").click(function(){
          $("#showWhatsAppNumberBox").toggle();
      });

      $('.nav-tabs li').click(function(){
          $(this).removeClass('active').addClass('active');
      });

      $(document).delegate('.forgot-redirect', 'click', function(){
          $('a.forgot_password_show').trigger('click');
          $('.alert-success').hide();
          $('.alert-danger').hide();
          $('.signin a').css({'background':'#e4e4e4','color':'#000'}) ;
      });

      $('.signin').click(function(){
          $('.alert-success').hide();
          $('.alert-danger').hide();
          $('.signin a').css('background','#e4e4e4') ;
      });

      $('.signup').click(function(){
          $('.alert-success').hide();
          $('.alert-danger').hide();
          $('.signin a').css('background','#888888') ;
      });

      $(document).delegate('.forgot_back_button', 'click', function(){
          $('a.login_show').trigger('click');
      });


      // validate signup form on keyup and submit
      $("#signupForm").validate({
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
                  equalTo: ".reg_password"
              },
              reg_email: {
                /* required: true,*/
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
          }
      });

      var url = document.location.toString();
      if (url.match('#')) {
          $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
      }

      // Sort the custom fields
      $('#account .form-group[data-sort]').detach().each(function() {
          if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#account .form-group').length) {
              $('#account .form-group').eq($(this).attr('data-sort')).before(this);
          }

          if ($(this).attr('data-sort') > $('#account .form-group').length) {
              $('#account .form-group:last').after(this);
          }

          if ($(this).attr('data-sort') < -$('#account .form-group').length) {
              $('#account .form-group:first').before(this);
          }
      });

      $('#address .form-group[data-sort]').detach().each(function() {
          if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#address .form-group').length) {
              $('#address .form-group').eq($(this).attr('data-sort')).before(this);
          }

          if ($(this).attr('data-sort') > $('#address .form-group').length) {
              $('#address .form-group:last').after(this);
          }

          if ($(this).attr('data-sort') < -$('#address .form-group').length) {
              $('#address .form-group:first').before(this);
          }
      });

  });


  function isEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-]))+$/;
      return regex.test(email);
  }
 </script>

<?php echo $footer; ?>
