<!DOCTYPE html>

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
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/gsdk-bootstrap-wizard.js?v=13"></script>
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
                    <div class="breadcrumps"><h5><?php echo $credit_language['text_step']; ?> 1<span>/2</span></h5></div>
                     <div class="form-group-outer">
                     <p><?php echo $credit_language['text_credit_1']; ?></p>
                     <p> <?php echo $credit_language['text_credit_2']; ?></p>
                     <p><?php echo $credit_language['text_credit_3']; ?></p>
                     <p><?php echo $credit_language['text_credit_call_detail']; ?>
                     </p>
                     <h6><?php echo $credit_language['text_credit_fill_detail']; ?></h6>
                   
                                <div class="credit_input_field">
                                    <div class="row">
                                        <form id="short_credit_application_form" name="short_credit_application_form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                          <div class="row">
                                            <div class="input-field">
                                             <input id="first_name" class="form-control" name="first_name" type="text" value="<?php echo $first_name??''; ?>" />
                                              <label for="first_name"><?php echo $credit_language['entry_firstname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                              <input id="middle_name" class="form-control" name="middle_name" type="text" value="<?php echo $middle_name??''; ?>" />
                                              <label for="middle_name"><?php echo $credit_language['entry_middle']; ?></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                            <input id="last_name" class="form-control required_input" name="last_name" type="text" value="<?php echo $last_name??''; ?>" />
                                              <label for="last_name"><?php echo $credit_language['entry_lastname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                            <input id="father_name" class="form-control" name="father_name" type="text" value="<?php echo $father_name??''; ?>" />
                                              <label for="father_name"><?php echo $credit_language['entry_fathername']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                            <input id="email" class="form-control" name="email" type="text" value="<?php echo $email??''; ?>" />
                                              <label for="email"><?php echo $credit_language['entry_email']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="phone_no" class="form-control required_input" name="phone_no" type="tel" minlength="10" maxlength="10" value="<?php echo $phone_no??''; ?>" />
                                              <label for="phone_no"><?php echo $credit_language['entry_telephone']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="dob" class="form-control date required_input" name="dob" type="text" value="<?php if(isset($dob) && !empty($dob)) echo date_format(date_create($dob),"d-m-Y") ?>" />

                                              <label for="dob"><?php echo $credit_language['entry_dob']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                            <input id="company_name" class="form-control required_input" name="company_name" type="text" value="<?php echo $company_name??''; ?>" />
                                              <label for="firm_name"><?php echo $credit_language['entry_firmname']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="current_pincode" name="current_pincode" class="form-control required_input" type="text"  value="<?php echo $current_pincode??''; ?>" minlength="6" maxlength="6">
                                              <label for="pin"><?php echo $credit_language['entry_pincode']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="row">                                       
                                              <label class="gst_lable"> <?php echo $credit_language['entry_is_gst_number']; ?><span class="required">*</span></label>
                                                <div class="gst_radio_btn">
                                                  <label>

                                                    <input class="with-gap" id="gst_number_check" name="gst_number_yes" value="1" type="radio" 
                                                    <?php if(!empty($gst_number)) { echo "checked"; } ?>
                                                    <?php if(!empty($gst_number) && empty($khufiya_user_id)) { echo "disabled"; } ?>
                                                    onclick="check_gst_number()"  />
                                                   
                                                    <span><?php echo $credit_language['button_yes']; ?></span>
                                                  </label>

                                                  <label>

                                                    <input class="with-gap" id="gst_number_check" name="gst_number_yes" type="radio" value="1"  <?php if(empty($gst_number)) { echo "checked"; } ?> <?php if(!empty($gst_number) && empty($khufiya_user_id)) { echo "disabled"; } ?> onclick="check_gst_number()"  />

                                                    <span><?php echo $credit_language['button_no']; ?></span>
                                                  </label>
                                                </div>                                            
                                          </div>

                                          <div class="row gst_number_div" <?php if(empty($gst_number)) { ?> style="display: none;" <?php } ?>>
                                            <div class="input-field">
                                          
                                           <input id="gst_number" name="gst_number" class="form-control required_input" type="text"  value="<?php echo $gst_number??''; ?>" <?php if(!empty($gst_number) && empty($khufiya_user_id)) { echo "disabled"; } ?> />

                                              <label for="gst_number"><?php echo $credit_language['entry_gst_number']; ?><span class="required">*</span></label>
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
                                          <div class="row">
                                            <div class="input-field">
                                           <input id="months_in_current_location" value="<?php echo $months_in_current_location??'';?>" class="form-control required_input" name="months_in_current_location" type="text">
                                              <label for="months_in_current_location">
                                               <?php echo $credit_language['entry_business_months']; ?></label>
                                            </div>
                                            <label class="location_text">
                                            <?php echo $credit_language['entry_business_months_2']; ?></label>
                                          </div>
                                          <div class="text_center">
                                            <button type="submit" value="Submit"><?php echo $credit_language['button_Next']; ?></button>
                                          </div>

                                         <input name="document_type" type="hidden" value="application_document"> 
                                         <input name="customer_id" type="hidden" value="<?php echo $customer_id??'0'; ?>"> 
                                         <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id??''; ?>">
                                         <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id??''; ?>">
                                         <input name="delete_document" type="hidden" value=""> 
                                         <input name="draft" type="hidden" value="1">
                                         <input type="hidden" name="credit_application_id"  value="<?php echo $id??''; ?>">
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
                                     <form id="short_credit_application_form2" name="short_credit_application_form2" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                    <div class="verification">
                                       
                                       <div class="form-group">
                                            <div class="input-field">
                                             <input id="pan_no" class="form-control" name="pan_no" type="text" value="<?php echo $pan_no??''; ?>">
                                              <label for="firm_name"><?php echo $credit_language['entry_pan']; ?><span class="required">*</span></label>
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
                                                <?php
                                                 if(!empty($data['application_document']['pancard'])) {
                                                   $pancard_doc = array_values($data['application_document']['pancard']);
                                                  ?>
                                                <div class="doc_image clear">
                                                  <div class="doc_img">
                                                     <img src="<?php echo STATIC_CONTENT_URL_SSL.$pancard_doc[0]['cdn_path']; ?>" />
                                                  </div>
                                                   <div class="doc_img doc_img_cross" id="<?php echo $pancard_doc[0]['id']?>">
                                                  <a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true">
                                                  </i></a>
                                                 </div>
                                                </div> 
                                                <?php } else { ?>
                                                  <div class="parrent_progress_div">
                                                     <img src="add_image.png">
                                                   </div>
                                                <?php } ?>   
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-field">
                                            <input id="aadhaar_no" class="form-control" name="aadhaar_no" type="text" value="<?php echo $aadhaar_no??''; ?>">
                                              <label for="firm_name"><?php echo $credit_language['entry_aadhar']; ?><span class="required">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div id="adhar_parrent_div">
                                            <lable class="text_design"><?php echo $credit_language['entry_aadhar_detail']; ?><span class="required">*</span></lable>
                                            <p  class="necessary_text"><?php echo $credit_language['entry_aadhar_detail_1']; ?></p>
                                            <div class="col s6 nopadding padding_right">

                                                  <input class="form-control-file fl mrbottom15 only_image" name="aadhaar_card[]" id="aadhaar_card" rel="2" type="file" style="display:none;">                                <div class="Browse_image" data-id="aadhaar_card" id="aadhaar_card_upload_image">
                                                  <?php if(!empty($data['application_document']['aadhaar_card'])) {
                                                        $aadhaar_card = array_values($data['application_document']['aadhaar_card']);
                                                   ?>
                                                <div class="doc_image clear">
                                                  <div class="doc_img">
                                                     <img src="<?php echo STATIC_CONTENT_URL_SSL.$aadhaar_card[0]['cdn_path']; ?>" />
                                                  </div>
                                                   <div class="doc_img doc_img_cross" id="<?php echo $aadhaar_card[0]['id']?>">
                                                  <a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true">
                                                  </i></a>
                                                 </div>
                                                </div> 
                                                <?php } else { ?>
                                                  <div class="parrent_progress_div">
                                                     <img src="add_image.png">
                                                   </div>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col s6 nopadding padding_left">

                                                <input class="form-control-file fl mrbottom15 only_image" name="aadhaar_card[]" id="aadhaar_card_2" rel="2" type="file" style="display:none;">                                <div class="Browse_image" data-id="aadhaar_card" id="aadhaar_card_upload_image">
                                                <div class="Browse_image" data-id="aadhaar_card_2" id="aadhaar_card_2_upload_image">
                                                  <?php if(!empty($data['application_document']['aadhaar_card']) && count($data['application_document']['aadhaar_card']) > 1) 
                                                  {
                                                        $aadhaar_card = array_values($data['application_document']['aadhaar_card']);
                                                   ?>
                                                <div class="doc_image clear">
                                                  <div class="doc_img">
                                                     <img src="<?php echo STATIC_CONTENT_URL_SSL.$aadhaar_card[1]['cdn_path']; ?>" />
                                                  </div>
                                                   <div class="doc_img doc_img_cross" id="<?php echo $aadhaar_card[1]['id']?>">
                                                  <a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true">
                                                  </i></a>
                                                 </div>
                                                </div> 
                                                <?php } else { ?>
                                                  <div class="parrent_progress_div">
                                                     <img src="add_image.png">
                                                   </div>
                                                <?php } ?>
                                                </div>
                                            </div> 
                                            </div>                                    
                                        </div>

                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_bank_statement']; ?><span class="required">*</span></lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_bank_statement_2']; ?></p>
                                            <input class="form-control-file fl mrbottom15 only_image allow_pdf" rel="3" id="six_months_bank_statement" name="six_months_bank_statement[]" type="file" style="display:none;">
                                            <?php if(!empty($data['application_document']['six_months_bank_statement'])){
                                              foreach($data['application_document']['six_months_bank_statement'] as $key=>$value){?>
                                                  <div class="col s6 nopadding">
                                                      <div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image">
                                                      <div class="doc_image clear">
                                                        <?php 
                                                        $ext = pathinfo($value['cdn_path']);
                                                            if(!empty($ext['extension']) && strtolower($ext['extension'])=='pdf'){ ?>
                                                                <div class="doc_img">
                                                                   <a href="<?php echo STATIC_CONTENT_URL_SSL.$value['cdn_path'];?>" target="_blank">Bank Document</a>
                                                                </div>
                                                            <?php }else{ ?>
                                                                <div class="doc_img">
                                                                   <img src="<?php echo STATIC_CONTENT_URL_SSL.$value['cdn_path']; ?>" />
                                                                </div>
                                                           <?php  }
                                                        ?>
                                                        
                                                         <div class="doc_img doc_img_cross" id="<?php echo $value['id']?>">
                                                        <a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true">
                                                        </i></a>
                                                       </div>
                                                      </div> 

                                                      </div>
                                                  </div>
                                              <?php }
                                            ?> 
                                            <?php } ?>
                                            <div class="col s6 nopadding">
                                                      <div class="Browse_image" data-id="six_months_bank_statement" id="six_months_bank_statement_upload_image">
                                                        <div class="parrent_progress_div">
                                                           <img src="add_image.png">
                                                         </div>

                                                      </div>
                                                  </div>
                                        </div>

                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_photo']; ?></lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_photo_2']; ?></p>
                                            <input class="form-control-file fl mrbottom15 only_image" rel="4" id="shop_photo" name="shop_photo[]" type="file" style="display: none;">

                                            <div class="col s12 nopadding">
                                                <div class="Browse_image" data-id="shop_photo" id="shop_photo_upload_image">
                                                 <?php if(!empty($data['application_document']['shop_photo'])) { 
                                                    $shop_photo = array_values($data['application_document']['shop_photo']);
                                                 ?>
                                                <div class="doc_image clear">
                                                  <div class="doc_img">
                                                     <img src="<?php echo STATIC_CONTENT_URL_SSL.$shop_photo[0]['cdn_path']; ?>" />
                                                  </div>
                                                   <div class="doc_img doc_img_cross" id="<?php echo $shop_photo[0]['id']?>">
                                                  <a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true">
                                                  </i></a>
                                                 </div>
                                                </div> 
                                                <?php } else { ?>
                                                  <div class="parrent_progress_div">
                                                     <img src="add_image.png">
                                                   </div>
                                                <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <lable class="text_design"><?php echo $credit_language['entry_selfie']; ?></lable>
                                            <p class="necessary_text"><?php echo $credit_language['entry_selfie_2']; ?></p>

                                            <input class="form-control-file fl mrbottom15 only_image" rel="5" id="selfie_with_shop" name="selfie_with_shop[]" type="file" style="display: none;">

                                            <div class="col s12 nopadding">
                                                <div class="Browse_image" data-id="selfie_with_shop" id="selfie_with_shop_upload_image">
                                                  
                                                  <?php if(!empty($data['application_document']['selfie_with_shop'])) {
                                                         $selfie_with_shop = array_values($data['application_document']['selfie_with_shop']); 
                                                   ?>
                                                <div class="doc_image clear">
                                                  <div class="doc_img">
                                                     <img src="<?php echo STATIC_CONTENT_URL_SSL.$selfie_with_shop[0]['cdn_path']; ?>" />
                                                  </div>
                                                   <div class="doc_img doc_img_cross" id="<?php echo $selfie_with_shop[0]['id']?>">
                                                  <a class="cross delete_image"><i class="fa fa-times-circle del_pic" aria-hidden="true">
                                                  </i></a>
                                                 </div>
                                                </div> 
                                                <?php } else { ?>
                                                  <div class="parrent_progress_div">
                                                     <img src="add_image.png">
                                                   </div>
                                                <?php } ?>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group additional_form">
                                            <div class="row">
                                              <label class="address_lable"><?php echo $credit_language['entry_check_current_address']; ?><span class="required">*</span></label>
                                                <div class="address_lable_btn">
                                                  <label>
                                                    <input class="with-gap" name="diffrent_address" id="adhar_address_not_same" type="radio" <?php if(!empty($diffrent_address)) echo 'checked="checked"'; ?>
                                                    
                                                      />
                                                    <span><?php echo $credit_language['button_yes']; ?></span>
                                                  </label>
                                                  <label>
                                                    <input class="with-gap" id="adhar_address_same" name="diffrent_address" type="radio" <?php if(empty($diffrent_address)) echo 'checked="checked"'; ?>  />
                                                    <span><?php echo $credit_language['button_no']; ?></span>
                                                  </label>
                                                </div>
                                            </div>
                                         
                                         <div class="form-group different_adhar_address" style="<?php if(empty($diffrent_address)) echo 'display: none;'; ?>">
                                            <div class="row">
                                                <div class="input-field">
                                                    <input id="permanent_address" class="form-control required_input" name="permanent_address"  type="text" value="<?php echo $permanent_address??'';?>">
                                                    <label for="address"><?php echo $credit_language['entry_address']; ?><span class="required">*</span></label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-field">
                                                    <input id="permanent_pincode" class="form-control required_input" name="permanent_pincode" type="text" value="<?php echo $permanent_pincode??'';?>">
                                                    <label for="pincode"><?php echo $credit_language['entry_pincode']; ?><span class="required">*</span></label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-field">
                                                    <input id="permanent_city" class="form-control required_input" name="permanent_city" type="text" value="<?php echo $permanent_city??'';?>">
                                                    <label for="city"><?php echo $credit_language['entry_city']; ?><span class="required">*</span></label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-field">
                                                    <input id="permanent_state" class="form-control required_input" name="permanent_state" type="text" value="<?php echo $permanent_state??'';?>">
                                                    <label for="state"><?php echo $credit_language['entry_state']; ?><span class="required">*</span></label>
                                                </div>
                                            </div>
                                         </div>

                                        </div>
                                        <div class="buttons clearfix">
                                            <div class="form_buttons_width">
                                                <button id="document_upload_form" type="submit" value="Submit" class="btn-continue submit_btn"><?php echo $credit_language['button_submit']; ?></button>
                                            </div>
                                        </div>
                                      </div>

                                 <input name="document_type" type="hidden" value="application_document"> 
                                <input name="customer_id" type="hidden" value="<?php echo $customer_id; ?>"> 
                                <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id; ?>">
                                <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id; ?>">
                                <input name="delete_document" type="hidden" value=""> 
                                <input name="draft" type="hidden" value="2">
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
 var upload_process='0';
  
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

$(document).delegate('#short_credit_application_form2', 'submit', function(event)
{  
  var error ='0';
  if($.trim($('#six_months_bank_statement').val())=='' && $.trim($('#six_months_bank_statement_upload_image .doc_img').html())==''){
     alert("<?php echo $credit_language['error_bank_statement']; ?>");
     error++;
  }
  if(error>0)
  {
    return false;  
  }
});

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
                  var image_div = $('#'+self_file.prop('id')+"_upload_image");
                  image_div.children('.parrent_progress_div').html(loading_html);
                   var xhr = new window.XMLHttpRequest();
                   //Download progress
                    xhr.addEventListener("progress", function (evt) {
                    image_div.children('.parrent_progress_div').remove();
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
                     if(ext=='pdf')
                     {
                       var pdf_path=STATIC_CONTENT_URL_SSL+result.file_path;
                       var loading_image_html='<div class="doc_image clear"><div class="doc_img"><div class="pdf_file"><a href="'+pdf_path+'" target="_blank">Bank_document.pdf</a></div></div><div class="doc_img doc_img_cross" id="'+result.last_image_id+'"><a class="cross delete_image" id="'+result.last_image_id+'"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div></div>';

                     }
                     else
                     {
                       var loading_image_html='<div class="doc_image clear"><div class="doc_img"><img src="'+image_path+'"></div><div class="doc_img doc_img_cross"><a class="cross delete_image" id="'+result.last_image_id+'"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div></div>';
                     }
                      
                    var image_div = $('#'+self_file.prop('id')+"_upload_image");
                     image_div.append(loading_image_html);
                     self_file.val('');

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
        if(confirm("Are you sure ?"))
        {
         var form_name = $(this).closest("form").attr('name');
         var doc_id = $(this).attr('id');
         var delete_doc_ids = $('#'+form_name+ ' input[name=delete_document]').val();
         
         var doc_ids = new Array(); 
          if(delete_doc_ids != ''){
            doc_ids = delete_doc_ids.split(",");
          }

          if(doc_ids.length > 0){
                    if(jQuery.inArray(doc_id, doc_ids) == -1) {
                      delete_doc_ids = delete_doc_ids + ',' + doc_id;
                    }
                }else{
                    delete_doc_ids = doc_id;
                }

          $('#'+form_name+ ' input[name=delete_document]').val(delete_doc_ids);      

         var main_div = $(this).parent('div').parent('div');
         $(this).parent('div').remove();
         main_div.html('<div class="parrent_progress_div"><img src="add_image.png"></div>');
       } 

      }); 


});


//for gst hide/show
function check_gst_number()
{
  if($('#gst_number_check').prop('checked'))
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
