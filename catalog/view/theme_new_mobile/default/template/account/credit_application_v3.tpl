<!DOCTYPE html>
<style type="text/css">
    label.ellipsis{
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap; 
        overflow: hidden;
    }
</style>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="google-play-app" content="app-id=in.wholesalebox">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <!--Import Google Icon Font-->
     <link type="text/css" rel="stylesheet" href="catalog/view/theme_new_mobile/default/css/materialize.min.css"  media="screen,projection"/>
      <link href="catalog/view/javascript/jquery-ui.min.css" rel="stylesheet" type="text/css" />
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <link href="catalog/view/theme_new_mobile/default/css/credit_form.css" rel="stylesheet">
      <link href="catalog/view/theme_new_mobile/default/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <!--Import materialize.css-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
     <title><?php echo $credit_language['heading_title']; ?></title>
     <script src="catalog/view/javascript/jquery/jquery-3.2.0.min.js" type="text/javascript"></script>
     <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
     <script src="catalog/view/theme_new_mobile/default/javascript/bootstrap.min.js"></script>
    <script src="catalog/view/theme_new_mobile/default/javascript/materialize.min.js"></script>
    <script src="catalog/view/theme_new_mobile/default/javascript/bootstrap.min.js"></script>

<script type="text/javascript">
    var ctoken = '<?php echo $ctoken; ?>';
    var draft = '<?php echo $draft; ?>'; 
    var validation_error = '<?php echo $validation_error; ?>'; 
    var ajax_upload ='<?php echo $ajax_upload;?>';
</script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/jquery.bootstrap.wizard.js" type="text/javascript"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/gsdk-bootstrap-wizard.js?v=14"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/jquery.validate.min.js"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/additional-methods.js"></script>
 <script src="catalog/view/javascript/jquery-ui.min.js"></script>

</head>
<body >
<div id="content">       
    <div class="container">
        <div class="wizard-container">
            <div class="card wizard-card" data-color="orange" id="wizardProfile">
                <div class="tab-content-popup">
                 <?php if(isset($form_type) && $form_type == '1' && empty($update_success)){?>   
                  <div id="application_form" class="tab-pane application_form form_section">
                    <div class="breadcrumps" style="height: 65px;"><h5>

                      <div style="padding-top: 15px;"><?php echo $credit_language['text_step']; ?> 1<span>/2</span></div>

                      <?php if(empty($khufiya_user_id) && !empty($back_button)) { ?>
                      <a class="home_logo" href="<?php echo $back_button;?>">
                        <img src="<?php echo STATIC_CONTENT_URL_SSL;?>img/catalog/rsz_wsb_tmp_logo_286.png" alt="wholesalebox-logo" class="img-responsive" style="height: 50px; float: left;margin: -40px 0px 0px 30px;">
                    </a>
                    <?php } ?></h5>
                      
                    </div>
                     <div class="form-group-outer">
                     <p><?php echo $credit_language['text_credit_1']; ?></p>
                     <p> <?php echo $credit_language['text_credit_2']; ?></p>
                     <p><?php echo $credit_language['text_credit_3']; ?></p>
                     <p><?php echo $credit_language['text_credit_call_detail']; ?>
                     </p>
                     <h6><?php echo $credit_language['text_credit_fill_detail']; ?></h6>
                   
                                <div class="credit_input_field">
                                    <div class="row">
                                        <form id="short_credit_application_form" class="<?php echo !empty($khufiya_user_id)?'khufiya':'nokhufiya'; ?>" name="short_credit_application_form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                          <div class="row">
                                            <div class="input-field">
                                             <input id="first_name" class="form-control" name="first_name" type="text" value="" />
                                              <label for="first_name"><?php echo $credit_language['entry_firstname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                              <input id="middle_name" class="form-control" name="middle_name" type="text" value="" />
                                              <label for="middle_name"><?php echo $credit_language['entry_middle']; ?></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                            <input id="last_name" class="form-control required_input" name="last_name" type="text" value="" />
                                              <label for="last_name"><?php echo $credit_language['entry_lastname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <!-- <div class="row">
                                            <div class="input-field">
                                            <input id="father_name" class="form-control" name="father_name" type="text" value="" />
                                              <label for="father_name"><?php echo $credit_language['entry_fathername']; ?><span class="required">*</span></label>
                                            </div>
                                          </div> -->
                                          <!-- <div class="row">
                                            <div class="input-field">
                                            <input id="email" class="form-control" name="email" type="text" value="" />
                                              <label for="email"><?php echo $credit_language['entry_email']; ?><span class="required">*</span></label>
                                            </div>
                                          </div> -->
                                          <div class="row">                                     
                                              <label class="gst_lable"> <?php echo $credit_language['gender_label']; ?><span class="required">*</span><label id="gender_error" class="gender_error">This field is required.</label></label>
                                                <div class="gst_radio_btn">
                                                  <label>

                                                    <input class="with-gap" id="gender_male" name="gender" value="male" type="radio" />
                                                   
                                                    <span><?php echo $credit_language['gender_male']; ?></span>
                                                  </label>

                                                  <label>

                                                    <input class="with-gap" id="gender_female" name="gender" type="radio" value="female" />

                                                    <span><?php echo $credit_language['gender_female']; ?></span>
                                                  </label>
                                                </div>                                            
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="phone_no" class="form-control required_input" name="phone_no" type="tel" minlength="10" maxlength="10" value="" />
                                              <label for="phone_no"><?php echo $credit_language['entry_telephone']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                             <input id="pan_no" class="form-control" name="pan_no" type="text" value="<?php echo $pan_no??''; ?>">
                                              <label for="firm_name"><?php echo $credit_language['entry_pan']; ?><span class="required">*</span></label>
                                            </div>
                                        </div>
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="dob" class="form-control date required_input" name="dob" type="text" value="" />

                                              <label for="dob"><?php echo $credit_language['entry_dob']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <!-- <div class="row">
                                            <div class="input-field">
                                            <input id="company_name" class="form-control required_input" name="company_name" type="text" value="" />
                                              <label for="firm_name"><?php echo $credit_language['entry_firmname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div> -->
                                           <div class="row">
                                                <div class="input-field">
                                                    <input id="permanent_address" class="form-control required_input" name="permanent_address"  type="text" value="">
                                                    <label for="address"><?php echo $credit_language['entry_address']; ?><span class="required">*</span></label>
                                                </div>
                                            </div>
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="current_pincode" name="current_pincode" class="form-control required_input" type="text"  value="" minlength="6" maxlength="6">
                                              <label for="pin"><?php echo $credit_language['entry_pincode']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>

                                          <div class="row">
                                            <div class="input-field">
                                           <input id="permanent_pincode" name="permanent_pincode" class="form-control required_input" type="text"  value="" minlength="6" maxlength="6">
                                              <label for="pin"><?php echo $credit_language['entry_pincode_office']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">                                       
                                              <label class="gst_lable"> <?php echo $credit_language['entry_is_gst_number']; ?><span class="required">*</span></label>
                                                <div class="gst_radio_btn">
                                                  <label>

                                                    <input class="with-gap" id="gst_number_check_yes" name="gst_number_yes" value="1" type="radio" 
                                                    onclick="check_gst_number()"  />
                                                   
                                                    <span><?php echo $credit_language['button_yes']; ?></span>
                                                  </label>

                                                  <label>

                                                    <input class="with-gap" id="gst_number_check" name="gst_number_yes" type="radio" value="0" onclick="check_gst_number()"  />

                                                    <span><?php echo $credit_language['button_no']; ?></span>
                                                  </label>
                                                </div>                                            
                                          </div>
                                          
                                          <div class="row gst_number_div">
                                            <div class="input-field">
                                          
                                           <input id="gst_number" name="gst_number" class="form-control required_input" type="text"  value=""/>

                                              <label for="gst_number"><?php echo $credit_language['entry_gst_number']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          
                                          
                                          <?php if(empty($khufiya_user_id)){?>
                                          <div class="row term_and_condition_class">                                       
                                            
                                                  <label>

                                                    <input class="with-gap" id="term_and_condition" name="term_and_condition" value="1" type="checkbox"  />
                                                   
                                                    <span><?php echo $credit_language['i_agree_to_all']; ?> <a href="<?php echo 'i/terms';?>"><?php echo $credit_language['term_condition_link']; ?></a></span>
                                                  </label>
                                          
                                          </div>
                                          <?php } ?>
                                          <!-- 
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="months_in_current_location" value="" class="form-control required_input" name="months_in_current_location" type="text">
                                              <label for="months_in_current_location">
                                               <?php echo $credit_language['entry_business_months']; ?></label>
                                            </div>
                                            <label class="location_text">
                                            <?php echo $credit_language['entry_business_months_2']; ?></label>
                                          </div> -->

                                          <div class="buttons clearfix">
                                          <?php if(!empty($back_button)){ ?> 
                                          <div class="col s3 nopadding">
                                            <div class="form_buttons_width">

                       <a class="back_btn" href="<?php echo $back_button;?>"><?php echo $credit_language['button_back']; ?></a>
                       </div>
                                          </div>
                                                <?php } ?>
                                            
                                          <div class="col s3 nopadding" style="float: right;">
                                            <div class="form_buttons_width">
                                                <button type="submit" value="Submit"><?php echo $credit_language['button_submit']; ?></button>
                                            </div>
                                          </div>
                                        </div>


                                          <input type="hidden" name="web_access" value="1">
                                         <input name="document_type" type="hidden" value="application_document"> 
                                         <input name="customer_id" type="hidden" value="<?php echo $customer_id??'0'; ?>"> 
                                         <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id??''; ?>">
                                         <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id??''; ?>">
                                         <input name="delete_document" type="hidden" value=""> 
                                         <input name="draft" type="hidden" value="1">
                                         <input type="hidden" name="credit_application_id"  value="<?php echo $id??''; ?>">
                                         <input type="hidden" name="credit_application_form_status"  value="<?php echo $draft??''; ?>">
                                         <input name="user_id" type="hidden" value="<?php echo $customer_id??'0'; ?>">
                                        </form>
                                        </div>
                                         </div>
                                          </div>
              
                       </div>
       
        <!-- SECOND PAGE -->
        <?php  
        } else if(isset($form_type) && $form_type == '2' && empty($update_success)){
        ?>
            <div id="application_form" class="tab-pane application_form  form_section">
             <div class="breadcrumps"><h5><?php echo $credit_language['text_step']; ?> 2<span>/2</span></h5></div>
             <div class="form-group-outer">
              <p><?php echo $credit_language['text_credit_4']; ?> </p>
                   
              <div class="credit_input_field">
                                    <div class="row">
                                     <form id="short_credit_application_form2" class="<?php echo !empty($khufiya_user_id)?'khufiya':'nokhufiya'; ?>" name="short_credit_application_form2" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                    <div class="verification">
                                       
                                       <!-- <div class="form-group">
                                            <div class="input-field">
                                             <input id="pan_no" class="form-control" name="pan_no" type="text" value="<?php echo $pan_no??''; ?>">
                                              <label for="firm_name"><?php echo $credit_language['entry_pan']; ?><span class="required">*</span></label>
                                            </div>
                                        </div> -->
                                        <div class="row">
                                            <div class="input-field">
                                            <input id="email" class="form-control" name="customer_email" type="text" value="" />
                                              <label for="email"><?php echo $credit_language['entry_email']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                            <input id="company_name" class="form-control required_input" name="company_name" type="text" value="" />
                                              <label for="firm_name"><?php echo $credit_language['entry_firmname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>

                                          <div class="row">
                                                <div class="input-field">
                                                    <input id="current_address" class="form-control required_input" name="current_address"  type="text" value="">
                                                    <label for="current_address"><?php echo $credit_language['shop_address']; ?><span class="required">*</span></label>
                                                </div>
                                            </div>

                                            

                                          <div class="row select_box">
                                            <div class="input-field">

                                           <select id="business_start_year" name="business_start_year">
                                            <option value="" disabled <?php echo empty($business_start_year)?'selected':'';?>> <?php echo $credit_language['entry_year']; ?></option>
                                              <?php 
                                              $current_year=date('Y');
                                              for($i='1950'; $i<=$current_year;$i++){
                                              if($business_start_year==$i){
                                              $select='selected';
                                                }else{
                                                $select='';
                                                }
                                                    echo '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
                                                }
                                               ?>
                                            </select>
                                          
                                            <label for="business_start_year"><?php echo $credit_language['entry_business_year']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                        <div class="form-group">
                                            <lable class="text_design">
                                            <?php echo $credit_language['entry_pan']; ?>
                                            </lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_pan_photo']; ?></p>
                                            <div class="browse_btn">
                                                <input class="form-control-file only_image" id="pancard" rel="1" name="pancard[]" type="file" style="display:none;">
                                            </div> 
                                            <div class="col s12 nopadding">
                                                <div class="Browse_image" data-id="pancard" id="pancard_upload_image">
                                                
                                                  <div class="parrent_progress_div">
                                                     <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                   </div>  
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div id="adhar_parrent_div">
                                            <lable class="text_design"><?php echo $credit_language['entry_aadhar_detail']; ?><span class="required">*</span></lable>
                                            <p  class="necessary_text"><?php echo $credit_language['entry_aadhar_detail_1']; ?></p>
                                            <div id="address_proof_document_parent_div">
                                            <div id="address_proof_document_div" class="col s6 nopadding padding_right">
                                                  <input class="form-control-file fl mrbottom15 only_image" name="aadhaar_card[]" id="aadhaar_card" rel="2" type="file" style="display:none;">                                
                                                  <div class="Browse_image" data-id="aadhaar_card" id="aadhaar_card_upload_image">
                                                  <div class="parrent_progress_div">
                                                     <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                   </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col s6 nopadding padding_left">

                                                <input class="form-control-file fl mrbottom15 only_image" name="aadhaar_card[]" id="aadhaar_card_2" rel="2" type="file" style="display:none;">                                
                                                <div class="Browse_image" data-id="aadhaar_card" id="aadhaar_card_upload_image">
                                                <div class="Browse_image" data-id="aadhaar_card_2" id="aadhaar_card_2_upload_image">
                                                  <div class="parrent_progress_div">
                                                     <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                   </div>
                                                </div>
                                            </div> 
                                            </div>
                                            </div>                                    
                                        </div>

                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_bank_statement']; ?><span class="required">*</span></lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_bank_statement_2']; ?></p>
                                            <input class="form-control-file fl mrbottom15 only_image allow_pdf" rel="3" id="six_months_bank_statement" name="six_months_bank_statement[]" type="file" style="visibility: hidden; height: 0px; width: 0px;">
                                                         <div class="" id="six_months_bank_statement_parent_div">
                                                       </div>
                                            <div class="col s12 nopadding">
                                                      <div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image">
                                                        <div class="parrent_progress_div">
                                                           <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                         </div>

                                                      </div>
                                                  </div>

                                                  <div class="buttons clearfix">
                                            <div class="form_buttons_width">
                                                <button id="upload_bank_statement" type="button" class="btn-continue submit_btn"><?php echo $credit_language['upload_bank_statement']; ?></button>
                                            </div>
                                        </div>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_photo']; ?></lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_photo_2']; ?></p>

                                            <input class="form-control-file fl mrbottom15 only_image" rel="4" id="shop_photo" name="shop_photo[]" type="file" style="visibility: hidden; height: 0px; width: 0px;">
                                             <div class="" id="shop_photo_parent_div">
                                                       </div>
                                            <div class="col s12 nopadding">
                                                <div class="Browse_image" data-id="shop_photo" id="shop_photo_upload_image">
                                                  <div class="parrent_progress_div">
                                                     <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                   </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_selfie']; ?></lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_selfie_2']; ?></p>

                                            <input class="form-control-file fl mrbottom15 only_image" rel="5" id="selfie_with_shop" name="selfie_with_shop[]" type="file" style="visibility: hidden; height: 0px; width: 0px;">
                                            <div class="" id="selfie_with_shop_parent_div">
                                                       </div>
                                            <div class="col s12 nopadding">
                                                <div class="Browse_image" data-id="selfie_with_shop" id="selfie_with_shop_upload_image">
                                                  
                                                 
                                                  <div class="parrent_progress_div">
                                                     <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                   </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div style="clear: both;"></div>
                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_visting_card_photo']; ?></lable>
                                             <p class="necessary_text"><?php echo $credit_language['entry_visting_card_photo_2']; ?></p>

                                            <input class="form-control-file fl mrbottom15 only_image" rel="10" id="visting_card_photo" name="visting_card_photo[]" type="file" style="display: none;">

                                            <div class="col s12 nopadding">
                                                <div class="Browse_image" data-id="visting_card_photo" id="visting_card_photo_upload_image">
                                                
                                                  <div class="parrent_progress_div">
                                                     <img src="catalog/view/theme_new_mobile/default/image/add_image.png">
                                                   </div>

                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="buttons clearfix">
                                          <?php if(!empty($back_button)){ ?> 
                                          <div class="col s3 nopadding">
                                            <div class="form_buttons_width">

                       <a class="back_btn" href="<?php echo $back_button;?>"><?php echo $credit_language['button_back']; ?></a>
                                                <?php } ?>
                                            </div>
                                          </div>
                                          <div class="col s3 nopadding" style="float: right;">
                                            <div class="form_buttons_width">
                                                <button id="document_upload_form" type="submit" value="Submit" class="btn-continue submit_btn"><?php echo $credit_language['button_submit']; ?></button>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <input type="hidden" name="web_access" value="1">
                                      <input name="document_type" type="hidden" value="application_document"> 
                                      <input name="customer_id" type="hidden" value="<?php echo $customer_id??''; ?>">
                                      <input name="user_id" type="hidden" value="<?php echo $customer_id??''; ?>"> 
                                      <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id??''; ?>">
                                      <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id??''; ?>">
                                      <input name="delete_document" type="hidden" value=""> 
                                      <input name="draft" type="hidden" value="2">
                                      <input type="hidden" name="credit_application_form_status"  value="<?php echo $draft??''; ?>">
                                      <input type="hidden" name="document_count" id="document_count"  value="">
                                      <input type="hidden" name="credit_application_id"  value="<?php echo $id??''; ?>">
                                     </form>
                                    </div>
                                    </div>
                                    </div>
                                     </div>
             
   <!-- success page -->            
  <?php } else if(!empty($update_success)  && $update_success == 'success' ){ ?>
              <div id="application_form" class="tab-pane application_form  form_section">
             <div class="breadcrumps"><h5><?php echo $credit_language['text_success']; ?></h5></div>
             <div class="form-group-outer" style="text-align: center;">
              <h6><?php echo $credit_language['text_thanks_msg']; ?> </h6>

               <?php if(empty($khufiya_user_id)) { ?>
                    <p style="margin-top: 30px;">
                      <?php if(isset($home)) { ?>
                        <div class="credit_input_field"> 
                        <div class="form_buttons_width">
                            <button onclick="window.location.href='<?php echo $home; ?>'" type="button" value="Submit" class="btn-continue submit_btn"><?php echo $credit_language['text_wholesalebox']; ?></button>
                        </div>
                     </div>   
                      <?php } else { ?>
                        <div class="credit_input_field"> 
                        <div class="form_buttons_width">
                            <button onclick="window.location.href='<?php echo $edit; ?>'" type="button" value="Submit" class="btn-continue submit_btn"><?php echo $credit_language['text_edit_form']; ?></button>
                             <br /> <br />

                            <?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id)) {?>
                            <button onclick="window.location.href='<?php echo $form_cancel; ?>'" type="button" value="Submit" class="btn-continue submit_btn"><?php echo $credit_language['text_my_account']; ?></button>
                            <?php } ?>


                        </div>
                     </div> 
                      <?php } ?>
                    
                     </p>               
                    <?php } else{ ?>
                    <?php if(!empty($edit)){ ?>
                      <p style="margin-top: 30px;">
                        <div class="credit_input_field"> 
                        <div class="form_buttons_width">
                            <button onclick="window.location.href='<?php echo $edit; ?>'" type="button" value="Submit" class="btn-continue submit_btn"><?php echo $credit_language['text_edit_form']; ?></button>
                           
                        </div>
                     </div> 
                    
                     </p>  

                  <?php } ?>
                    <?php } ?>
              </div>
              </div>
  <?php } ?> 
   </div>
  </div>
</div>
</div>
</div>  


<script>
$( document ).ready(function() 
{

  // /six_months_bank_statement upload_bank_statement

  $(document).on('click','#upload_bank_statement',function(){
    $('#six_months_bank_statement').trigger('click');
  });

 var upload_process='0';
 var customer_id='<?php echo $customer_id??'';?>';
 var customer_application_id='<?php echo $id??'';?>';

 var khufiya_user_id='<?php echo $khufiya_user_id??'';?>';




 if(typeof(customer_application_id) !=='undefined' && $.trim(customer_application_id)!=''){
    $.ajax({
          url: 'api/credit_application/initialCreditAppUsingFormId',
          type: 'post',
          dataType: 'json',
          crossOrigin: true,
          data: JSON.stringify({'customer_application_id':customer_application_id}),
          // cache: false,
           contentType: 'application/json',
          // processData: false,
          beforeSend: function() {
            //$(node).button('loading');
          },
          complete: function() {
            //$(node).button('reset');
          },
          success: function(json) {
            //console.log(json);
            // /var json_obj = JSON.parse(json);
            //console.log(json.data);
            if(typeof(json.status) !=='undefined' && json.status=='1'){
              fillFormField(json);
            }else{
              alert('error');
            }
            // $(node).parent().find('.text-danger').remove();

            // if (json['error']) {
            //   $(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
            // }

            // if (json['success']) {
            //   alert(json['success']);

            //   $(node).parent().find('input').attr('value', json['code']);
            // }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
 } else if(typeof(customer_id) !=='undefined' && $.trim(customer_id)!='' && customer_id!='0'){
  $.ajax({
          url: 'api/credit_application/initialCreditApp',
          type: 'post',
          dataType: 'json',
          crossOrigin: true,
          data: JSON.stringify({'user_id':customer_id,'web_access':'1'}),
          // cache: false,
           contentType: 'application/json',
          // processData: false,
          beforeSend: function() {
            //$(node).button('loading');
          },
          complete: function() {
            //$(node).button('reset');
          },
          success: function(json) {
            //console.log(json);
            // /var json_obj = JSON.parse(json);
            //console.log(json.data);
            if(typeof(json.status) !=='undefined' && json.status=='1'){
              fillFormField(json);
            }else{
              alert('error');
            }
            // $(node).parent().find('.text-danger').remove();

            // if (json['error']) {
            //   $(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
            // }

            // if (json['success']) {
            //   alert(json['success']);

            //   $(node).parent().find('input').attr('value', json['code']);
            // }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
 }


 function fillFormField(json){
  $.each(json.data, function( index, value ) {
                  //console.log( index + ": " + value.value );
                  if(typeof(value.value) !=='undefined'){
                    if(index=='dob'){
                      if(value.value !==null){
                      var date_format_value = formatDate(value.value);
                      $('#'+index).val(date_format_value);
                      }
                    }else if(index=='gst_number'){
                      if($.trim(value.value) !==''){
                        $(".gst_number_div").fadeIn();
                        $("#gst_number").val(value.value);
                        $("#gst_number_check_yes").attr('checked', 'checked');
                        //gst_number_check_yes
                      }else{
                        $(".gst_number_div").fadeOut();
                        $("#gst_number").val('');
                        $("#gst_number_check").attr('checked', 'checked');
                      }
                    }else if(index=='gender'){
                      if($.trim(value.value) !=='' && value.value=='male'){
                        $("#gender_male").attr('checked', 'checked');
                      }if($.trim(value.value) !=='' && value.value=='female'){
                        $("#gender_female").attr('checked', 'checked');
                      }

                      
                    }else{ 
                      $('#'+index).val(value.value);
                    }
                      $('#'+index).parent().find('label').addClass('active');
                  }else {
                    if(index=='address_proof_document'){
                      if(value.images.length >= '2'){
                        var image_html='';
                        $.each(value.images, function( index, value ) {
                          var padding_class='';
                          if(index%2=='0'){
                            padding_class = 'padding_right';
                          }
                          image_html +='<div class="col s6 nopadding '+padding_class+'"><input class="form-control-file fl mrbottom15 only_image" name="address_proof_document[]" id="address_proof_document'+index+'" type="file" style="display:none;"><div class="Browse_image" data-id="address_proof_document" id="address_proof_document_upload_image"><div class="doc_image clear"><div class="doc_img"><img src="'+value+'"></div>';
                          if($.trim(khufiya_user_id)!=''){
                            image_html +='<div class="doc_img doc_img_cross"><a class="cross delete_image" id="70"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                          }
                          image_html +='</div></div></div>';
                        });
                        $('#address_proof_document_parent_div').html(image_html);
                      }
                      else if(value.images.length == '1'){ 
                        var image_html='';
                        $.each(value.images, function( index, value ) {
                          var padding_class='';
                          if(index%2=='0'){
                            padding_class = 'padding_right';
                          }
                          image_html +='<input class="form-control-file fl mrbottom15 only_image" name="address_proof_document[]" id="address_proof_document'+index+'" type="file" style="display:none;"><div class="Browse_image" data-id="address_proof_document" id="address_proof_document_upload_image"><div class="doc_image clear"><div class="doc_img"><img src="'+value+'"></div>';
                          if($.trim(khufiya_user_id)!=''){
                            image_html +='<div class="doc_img doc_img_cross"><a class="cross delete_image" id="70"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                          }
                          image_html +='</div></div>';
                        });
                        if($.trim(image_html)!=''){
                        $('#address_proof_document_div').html(image_html);
                        }
                      }
                    }else if(index=='shop_photo'){

                      //shop_photo_parent_div
                      var image_html='';
                        $.each(value.images, function( index, value ) {
                          image_html +='<div class="col s12 nopadding"><div class="Browse_image" data-id="shop_photo" id="shop_photo_upload_image_div"><div class="doc_image clear"><div class="doc_img"><img src="'+value+'" /></div>';
                          
                          if($.trim(khufiya_user_id)!=''){
                          image_html +='<div class="doc_img doc_img_cross"><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                        }

                          image_html +='</div></div>';
                          });
                        if($.trim(image_html)!=''){
                        $('#shop_photo_parent_div').html(image_html);
                        }
                      }
                      else if(index=='visting_card_photo'){
                        var image_html='';
                          $.each(value.images, function( index, value ) {
                            image_html ='<div class="doc_image clear"><div class="doc_img"><img src="'+value+'" /></div>';
                            if($.trim(khufiya_user_id)!=''){

                            image_html +='<div class="doc_img doc_img_cross"><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                          }
                            image_html +='</div>';
                            });
                          if($.trim(image_html)!=''){
                          $('#visting_card_photo_upload_image').html(image_html);
                          }
                        }
                        else if(index=='selfie_with_shop'){
                        var image_html='';
                          $.each(value.images, function( index, value ) {
                            image_html +='<div class="col s12 nopadding"><div class="Browse_image" data-id="selfie_with_shop"  id="selfie_with_shop_upload_image_div"><div class="doc_image clear"><div class="doc_img"><img src="'+value+'" /></div>';
                                if($.trim(khufiya_user_id)!=''){
                                  image_html +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                                }
                            image_html +='</div></div></div>';
                            });
                          if($.trim(image_html)!=''){
                          $('#selfie_with_shop_parent_div').html(image_html);
                          }
                        }else if(index=='pancard'){
                          var image_html='';
                          $.each(value.images, function( index, value ) {
                              image_html ='<div class="doc_image clear"><div class="doc_img"><img src="'+value+'" /></div>';
                             if($.trim(khufiya_user_id)!=''){
                              image_html +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                            }
                              image_html +='</div>';
                            });
                          if($.trim(image_html)!=''){
                           $('#pancard_upload_image').html(image_html); 
                          }
                          
                        }
                        else if(index=='six_months_bank_statement'){
                            var image_html='';
                            $.each(value.images, function( index, value ) {
                              var padding_class='';
                              if(index%2=='0'){
                                padding_class = 'padding_right';
                              }
                              image_html +='<div class="col s12 nopadding"><div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image"><div class="doc_image clear">';
                                                       var fileNameExt = value.substr(value.lastIndexOf('.') + 1);
                                                       if(fileNameExt=='pdf'){
                                                        image_html +='<div class="doc_img"><a href="'+value+'" target="_blank">Bank Document</a></div>';
                                                       }else{
                                                        image_html +='<div class="doc_img"><img src="'+value+'" /></div>';
                                                       }
                                                       if($.trim(khufiya_user_id)!=''){
                                                         image_html +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                                                       }

                                                         image_html +='</div></div></div>';
                            });
                            if($.trim(image_html)!=''){
                            $('#six_months_bank_statement_parent_div').html(image_html);
                            }
                         
                    }

                          
                    }
              });
 }

function formatDate(date) {
     var d = new Date(date),
         month = '' + (d.getMonth() + 1),
         day = '' + d.getDate(),
         year = d.getFullYear();

     if (month.length < 2) month = '0' + month;
     if (day.length < 2) day = '0' + day;

     return [day, month, year].join('-');
 }

$('#close_window').click(function(){
    window.close();
  });

//dob calendar
 $( function() {
   var dt = new Date();
   dt.setFullYear(new Date().getFullYear()-18);
   $('#dob').datepicker({ 
                autoclose: true, 
                todayHighlight: true,
                dateFormat: 'dd-mm-yy',
                yearRange: "-100:+0",
                maxDate: new Date(),
                changeMonth: true,
                changeYear: true,
        }).attr('readonly','readonly');
 });


 
$(document).delegate('.Browse_image .parrent_progress_div', 'click', function(event)
{ 
 var id = $(this).parent('.Browse_image').data('id');
 $("#"+id).click();
});  



$(document).delegate('#adhar_address_not_same, #adhar_address_same', 'change', function(event)
{ 
  if($("#adhar_address_not_same").is(":checked")) {
     $('.different_adhar_address').slideDown();
  }else{
    $('.different_adhar_address').slideUp();
  }
});


  $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

$('input[name="gender"]').click(function(){
  $('#gender_error').fadeOut(); 
});
      $(document).delegate('#short_credit_application_form', 'submit', function(event){       
              event.preventDefault();
              var error=0;
              if ($('input[name="gender"]:checked').length == 0) {  
              $('#gender_error').fadeIn(); 
                error++;
              }

             if ($('input[name="term_and_condition"]:checked').length == 0 && $.trim(khufiya_user_id)=='') {  
              alert('Please checked term and condition');
                error++;
              } 

              if(error == '0'){
                var data = $(this).serializeFormJSON();
              if(typeof(customer_application_id) !=='undefined' && $.trim(customer_application_id)!=''){
                var url='api/credit_application/saveBasicInfo';
              }else{
                var url='api/credit_application/insertBasicInfo';
              }
              $.ajax({
              url: url,
              type: 'post',
              dataType: 'json',
              data: JSON.stringify(data, null, "  "),
              //cache: false,
              contentType: false,
              //processData: false,
              beforeSend: function() {
                //$(node).button('loading');
              },
              complete: function() {
               //$(node).button('reset');
              },
              success: function(json) { 
                  if(typeof(json.status) !=='undefined' && json.status=='1'){
                    window.location.href = json.redirect_action;
                  }else{
                     if(typeof(json.message) !=='undefined' && json.message !==''){
                      alert(json.message);
                     }else{
                      alert('error');
                     }
                      
                  }

                return false;
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
            });
              }
              
              return false;
      });


$(document).delegate('#short_credit_application_form2', 'submit', function(event){       
              event.preventDefault();



                var error ='0';
  if($.trim($('#short_credit_application_form2 input[name="khufiya_user_id"]').val())==''  && $.trim($('#six_months_bank_statement_upload_image .doc_img').html())==''){
     alert("<?php echo $credit_language['error_bank_statement']; ?>");
     error++;
  }
if($.trim($('#short_credit_application_form2 input[name="khufiya_user_id"]').val())==''  && $.trim($('#address_proof_document_parent_div .doc_img').html())==''){
     alert("<?php echo $credit_language['error_address_proof']; ?>");
     error++;
  }

  if(error>0)
  {
    return false;  
  }else{ 

                 var data = $(this).serializeFormJSON();
              $.ajax({
              url: 'api/credit_application/saveKycDocument',
              type: 'post',
              dataType: 'json',
              data: JSON.stringify(data, null, "  "),
              //cache: false,
              contentType: false,
              //processData: false,
              beforeSend: function() {
                //$(node).button('loading');
              },
              complete: function() {
               //$(node).button('reset');
              },
              success: function(json) { 
                  if(typeof(json.status) !=='undefined' && json.status=='1'){
                    window.location.href = json.redirect_action;
                  }else{
                    if(typeof(json.message) !=='undefined' && json.message !==''){
                      alert(json.message);
                     }else{
                      alert('error');
                     }
                  }

                
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
            }); 
  }

              return false;
      });

// $(document).delegate('#short_credit_application_form2', 'submit', function(event)
// {  
//   var error ='0';
//   if($.trim($('#six_months_bank_statement').val())=='' && $.trim($('#six_months_bank_statement_upload_image .doc_img').html())==''){
//      alert("<?php echo $credit_language['error_bank_statement']; ?>");
//      error++;
//   }
//   if(error>0)
//   {
//     return false;  
//   }
// });

    //upload files
    $(document).delegate('.only_image', 'change', function(event)
     {   
      var ext = $(this).val().split('.').pop().toLowerCase();
      var file_size = this.files[0].size;
      var max_file_size = '8388608'; //1048576 = 1MB
        if($(this).hasClass('allow_pdf') && $.inArray(ext, ['png','jpg','jpeg','pdf']) == -1)
        {
              $(this).val('');
              alert('File type not supported.');
        }
        else if(!$(this).hasClass('allow_pdf') && $.inArray(ext, ['png','jpg','jpeg']) == -1)
        {
              $(this).val('');
              alert('File type not supported.');
        }
        else if(file_size > max_file_size)
        {
            $(this).val('');
            alert('File must be less than 8MB');
        }
        else
        {
           var loading_html='<i class="fa fa-circle-o-notch fa-spin loading_font" ></i>';
            let self_file=$(this);
            formdata = new FormData();
            file =$(this).prop('files')[0];
            formdata.append($(this).prop('name'), file);
            $(this).val('');
            $(this).prop('disabled', true);
            upload_process++;
            $('#document_upload_form').prop('disabled', true);

             jQuery.ajax({
              xhr: function () 
               {
                if(self_file.attr('id')=='six_months_bank_statement' ){
                  var loading_div='<div class="col s12 nopadding clear" id="six_months_bank_statement_loading"><div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image"><div class="doc_image clear"><div class="doc_img"><i class="fa fa-circle-o-notch fa-spin loading_font" ></i></div>';
                  if($.trim(khufiya_user_id)!=''){
                  loading_div +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                  }

                  loading_div +='</div></div></div>';
                  $('#six_months_bank_statement_parent_div').append(loading_div);
                }
                else if(self_file.attr('id')=='shop_photo' || self_file.attr('id')=='selfie_with_shop'){
                    var loading_div='<div class="col s12 nopadding clear" id="'+self_file.attr('id')+'_loading"><div class="Browse_image"><div class="doc_image clear"><div class="doc_img"><i class="fa fa-circle-o-notch fa-spin loading_font" ></i></div>';
                    
                    if($.trim(khufiya_user_id)!=''){
                    loading_div +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                    }
                    loading_div +='</div></div></div>';
                    $('#'+self_file.prop('id')+'_parent_div').append(loading_div);
                }
                  else{
                  var image_div = $('#'+self_file.prop('id')+"_upload_image");
                  image_div.children('.parrent_progress_div').html(loading_html);
                }
                  
                   var xhr = new window.XMLHttpRequest();
                   //Download progress
                    xhr.addEventListener("progress", function (evt) {
                      if(self_file.attr('id')!='six_months_bank_statement' && self_file.attr('id')!='shop_photo' && self_file.attr('id')!='selfie_with_shop'){
                          image_div.children('.parrent_progress_div').remove();
                      }
                    self_file.prop('disabled', false);
                    if (evt.lengthComputable) 
                     {
                       var percentComplete = evt.loaded / evt.total;
                       console.log(Math.round(percentComplete * 100) + "%");
                     }}, false);
                     return xhr;
              },
              url: ajax_upload,
              type: "POST",
              data: formdata,
              processData: false,
              contentType: false,
              dataType: 'json',
              success: function (result)
                {
                   upload_process--;
                   if(upload_process=='0')
                   {
                     $('#document_upload_form').prop('disabled', false);
                   }
                   var STATIC_CONTENT_URL_SSL='<?php echo STATIC_CONTENT_URL_SSL;?>';

                   if(typeof(result.last_image_id) !=='undefined' && typeof(result.file_path) !=='undefined')
                   {
                    var image_path=STATIC_CONTENT_URL_SSL+'img/dw=213,q=90/'+result.file_path;
                     if(self_file.attr('id')=='six_months_bank_statement'){

                    if(ext=='pdf')
                     {
                       var pdf_path=STATIC_CONTENT_URL_SSL+result.file_path;


                        var loading_image_html ='<div class="col s12 nopadding clear"><div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image"><div class="doc_image clear"><div class="doc_img"><a href="'+pdf_path+'" target="_blank">Bank Document</a></div>';

                        if($.trim(khufiya_user_id)!=''){
                        loading_image_html +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                        }
                        loading_image_html +='</div></div></div>';

                     }
                     else
                     {

                       var loading_image_html ='<div class="col s12 nopadding clear"><div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image"><div class="doc_image clear"><div class="doc_img"><img src="'+image_path+'" /></div>';

                       if($.trim(khufiya_user_id)!=''){
                        loading_image_html +='<div class="doc_img doc_img_cross" id=""><a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                      }
                       loading_image_html +='</div></div></div>';

                     }

                        //image_div.append(loading_image_html);
                        $('#six_months_bank_statement_parent_div').append(loading_image_html);
                        $('#six_months_bank_statement_loading').remove();
                        self_file.val('');
                     }else if(self_file.attr('id')=='shop_photo' || self_file.attr('id')=='selfie_with_shop'){
                      var loading_image_html='<div class="col s12 nopadding"><div class="Browse_image" data-id="'+self_file.attr('id')+'" id="'+self_file.attr('id')+'_upload_image_div"><div class="doc_image clear"><div class="doc_img"><img src="'+image_path+'"></div>';
                      if($.trim(khufiya_user_id)!=''){
                      loading_image_html +='<div class="doc_img doc_img_cross"><a class="cross delete_image" id="'+result.last_image_id+'"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                      }
                      loading_image_html +='</div></div></div>';
                        $('#'+self_file.attr('id')+'_loading').remove();
                        var image_div = $('#'+self_file.attr('id')+'_parent_div');
                        image_div.append(loading_image_html);
                        self_file.val('');

                     }


                     else{
                       var loading_image_html='<div class="doc_image clear"><div class="doc_img"><img src="'+image_path+'"></div>';

                       if($.trim(khufiya_user_id)!=''){
                      loading_image_html +='<div class="doc_img doc_img_cross"><a class="cross delete_image" id="'+result.last_image_id+'"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div>';
                    }
                       loading_image_html +='</div>';
                       var image_div = $('#'+self_file.prop('id')+"_upload_image");
                        image_div.append(loading_image_html);
                        self_file.val('');

                    }
                     
                     
                      
                    

                   }
                   else
                   {
                     alert('Document not upload please try again');
                   }
                },
              error: function (xhr, ajaxOptions, thrownError) 
               {
                  alert('Document not upload please try again');
               },
            });
          }
        });

      

      //delete image
    $(document).delegate('.doc_img_cross', 'click', function(e)
     {  
     //var customer_application_id='<?php echo @$id;?>'; 
 //alert($(this).parent().find('img').attr('src'));
 //alert(customer_application_id);
 var parent_div_identify=$(this).parent().parent().parent().find('.Browse_image').attr('id');
 var image_src=$(this).parent().find('img').attr('src');
 if(typeof(image_src) ==='undefined'){
var image_src=$(this).parent().find('a').attr('href');
 }
 var parent_div=$(this).parent().parent().parent();
 var self_div = $(this);
        if(confirm("Are you sure ?"))
        {
          $.ajax({
          url: 'api/credit_application/removeDocument',
          type: 'post',
          dataType: 'json',
          crossOrigin: true,
          data: JSON.stringify({'credit_application_id':customer_application_id,'image_url':image_src,'web_access':'1'}),
          // cache: false,
           contentType: 'application/json',
          // processData: false,
          beforeSend: function() {
            //$(node).button('loading');
          },
          complete: function() {
            //$(node).button('reset');
          },
          success: function(json) {
            if(typeof(json.status) !=='undefined' && json.status=='1'){
              if(typeof(parent_div_identify) !=='undefined' && ($.trim(parent_div_identify)=='shop_photo_upload_image_div' || $.trim(parent_div_identify)=='six_months_bank_statement_upload_image' || $.trim(parent_div_identify)=='selfie_with_shop_upload_image_div')){
                parent_div.remove();
          
          }else{
            var main_div = self_div.parent('div').parent('div');
            self_div.parent('div').remove();
            main_div.html('<div class="parrent_progress_div"><img src="catalog/view/theme_new_mobile/default/image/add_image.png"></div>');

         }
            }else{
              alert('error');
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
          }
        });

         

         
       } 

      }); 
$('#dob').change(function(){
  if($(this).val()!=''){
  $(this).parent().find('label').addClass('active');
  }

});

});


//for gst hide/show
function check_gst_number()
{
  if($('#gst_number_check_yes').prop('checked'))
  {
    $(".gst_number_div").show();
    <?php if(!empty($gst_number)) { ?>
      $("#gst_number").val('<?php echo $gst_number; ?>'); 
    <?php } else { ?>
      $("#gst_number").val('');
    <?PHP } ?>
  }
  else
  {
    $(".gst_number_div").hide();
    $("#gst_number").val('');
   
  }
} 

</script>
</body>
</html>
