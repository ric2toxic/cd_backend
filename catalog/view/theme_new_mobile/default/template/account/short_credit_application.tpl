<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8" />
    <meta name="google-play-app" content="app-id=in.wholesalebox">

 <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="catalog/view/theme_new_mobile/default/stylesheet/materialize.min.css"  media="screen,projection"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?php echo $credit_language['heading_title']; ?></title>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    

<link href="catalog/view/theme_new_mobile/default/stylesheet/all-library.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/jquery/jquery-3.2.0.min.js" type="text/javascript"></script>
<!---- add for datepicker ---->       
<link href="catalog/view/theme_new_mobile/default/javascript/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css" />
 <script src="catalog/view/theme_new_mobile/default/javascript//bootstrap-datepicker/bootstrap-datepicker.js"></script>
<!---- add for datepicker ---->
<script src="catalog/view/theme_new_mobile/default/javascript/bootstrap.min.js"></script>

<script src="catalog/view/theme_new_mobile/default/javascript/materialize.min.js"></script>

<!-- wizard js --->
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
<!-- End wizard js --->



<link href="catalog/view/javascript/jquery-ui.min.css" rel="stylesheet" type="text/css" />
 <script src="catalog/view/javascript/jquery-ui.min.js"></script>


  

 <style type="text/css">
 .marginTop20{margin-top: 20px;}
 .errorClass{color: #f90606 !important;}
         .fl{float: left;}
    .fr{float: right;}
    .clear{clear: both;}
    .mrbottom15{margin-bottom: 15px; }
    .add_attachment,.add_bank_attachment{font-size: 25px; color: #253d98; cursor: pointer;}
    .del_pic{font-size: 25px; color: #e81515; cursor: pointer;}

    .input-field > label{font-size: 15px;}
    label {color: #233c98 !important; font-weight: 100 !important;}
    
 
label.error{color: #f90606 !important; margin-top: 19px; font-size: 12px!important;}

/*.ui-datepicker .ui-datepicker-header{padding: 1.2em 0;}*/
.ui-datepicker-month{display: block; float: left; color: #337ab7;}
.ui-datepicker-year{display: block; float: right; color: #337ab7;}
.input-field label.error{ margin-top: 19px;}

.input-field  label:nth-child(3){color: #f90606 !important;}
  .document_image {
        padding: 10px 0;
        
    }
    .document_image img{float: left;margin-bottom: 15px;
        
    }
    .doc_image{padding-top: 10px;}
   .doc_img_cross{float: left;margin-left: 5px;}
 .date_field{ margin-bottom: 35px;}
 .container {width: 70%;}
 .form-control{border-bottom: 1px solid #337ab7 !important; box-shadow: 0 0 0 0 !important;}
 .btn-continue:hover{background-color: #133596 !important;}
 .nav-pills > li.active > a, .nav-pills > li.active > a:focus, .nav-pills > li.active > a:hover {
    color: #233c98;
background-color: #fff;
font-weight: bold;
font-size: 18px;
}
.ui-dialog-titlebar-close {
  display: none;
}
.ui-dialog{
    top:50%;
    left: 35%;
}
.success_div{    margin-top: 18px;
    text-align: center;
    height: auto;
    font-size: 20px;
    padding-top: 34px;}
    .edit_form_button{margin-left: 43%;
    margin-bottom: 5%;
    margin-top: 3%;}
    .submit_btn, .submit_btn:active,.submit_btn:hover, .submit_btn:focus
    {
        background-color: #133596!important;
        color:#fff!important;
    }
    .sucess_title{color: #233c98;
    background-color: #fff;
    font-weight: bold;
    font-size: 18px;}
        .sure_delete_outer{
    border: 1px solid red;
    border-radius: 5px;
    padding: 10px;
    text-align: center;
    margin: 10px 0px;
    display: block;
    float: left;
    width: 30%;
    }
    .sure_delete_outer p{float: left; width: auto;}
    .back_buttom_class {
        float: right;
    font-size: 18px;
    padding: 4px 16px;
border-radius: 5px;font-weight: bold;}
.application_form_a{float: left;}

.input-field {
    position: relative;
    margin-top: 3rem !important;
    margin-bottom: 3rem  !important;
}
.input-field.col label {
     left: 0!important;
}

.select-dropdown{
    border-bottom: 1px solid #9e9e9e !important;
    -webkit-box-shadow: 0 1px 0 0 #9e9e9e!important;
    box-shadow: none !important;
}
.select-wrapper li span{color: #9e9e9e !important;}

    @media only screen and (max-width: 600px)
    {
        .application_form_a{width: 0px;}
        .nav-pills li.active{height: 0px;}
        .ui-dialog{
            top:50%;
            left: 5%;
            }
        .second_step_title{font-size: 18px !important;}
        .input-field {
    position: relative;
    margin-top: 1rem;
    margin-bottom: 1rem;
}
        .container {width: 90%;}
        .card.wizard-card
        {
            box-shadow: none;
        }
        .edit_form_button{margin-left: 30%;
    margin-bottom: 5%;
    margin-top: 3%;}

    .sure_delete_outer{
        clear: both;
    border: 1px solid red;
    border-radius: 5px;
    padding: 10px;
    text-align: center;
    margin: 10px 0px;
    display: block;
    width: 100%;
    }
    .document_image img{
        margin-bottom: 20px;
        
    }
.required_mssg{font-size: 12px;}
    .form-group-outer{
    padding: 0px;
}
.wizard-card
{
    padding: 0px!important;
}
.submit_btn {
    width: 100%;
}

.cancel_btn{    border-radius: 4px;}
    }
    .input-field > input
    {
        border-bottom: 1px solid #636363!important;
    }
    .input-field > label
    {
        color: #636363;
    }
    .input-field > label >span, .form-group > label > span, .additional_form > label > span
    {
        color: red!important;
    }
    .additional_form{display: none;}
    .credit_input_field button
    {
        width: 100%;
        background-color: #233B97;
        color:#fff;
        border: none;
    }
    .different_address_text span p
    {
        margin: 0px;
    }
    .browse_btn
    {
        margin-top: 10px;
    }
    [type="checkbox"]:checked + span:not(.lever)::before
    {
        border-right: 2px solid #fff!important;
        border-bottom: 2px solid #fff!important;
    }
    [type="checkbox"].filled-in:checked + span:not(.lever)::after {
    border: 2px solid #233B97;
    background-color: #233B97;
}
.pdf_file{float: left; padding: 3px; margin: 0px 5px; font-weight: bold;}

.wizard-card
{
    padding: 15px;
}
.diffrent_add_label{
        color: #565656!important;
    font-family: SourceSansPro-Regular;
    font-size: 14px!important;
}
.cancel_btn{border: none;
    display: inline-block;
    height: 36px;
    line-height: 36px;
    padding: 0 16px;
    text-transform: uppercase;
    vertical-align: middle;
    -webkit-tap-highlight-color: transparent;
    background-color: #e2e2e2!important;
    color: #565656!important;}
    .nav-pills li a{padding: 0!important;}
    .nav-pills {margin-bottom: 10px;}
    .cancel_parent{width: auto!important;
    float: left!important;}
    .second_step_title{
            margin: 0 0 20px !important; font-size: 16px
    }
    .headerStyle{margin-bottom: 20px;    height: 40px;}

    .progress-div {
    border: #0FA015 1px solid;
    border-radius: 4px;
    text-align: center;
    width: 80%;
    float: left;
}
.progress-bar {
    background-color: #12CC1A;
    height: 20px;
    color: #FFFFFF;
    width: 0%;
    -webkit-transition: width .3s;
    -moz-transition: width .3s;
    transition: width .3s;
}
.loding_img{
    height: 200px;
    width: 200px;
    border: 1px solid #808080;  
    border-radius: 5px;
    border-style: dotted;
}
.parrent_progress_div{width: auto;}
.loadin_img_loader{top: 38%; left: 38%; position: relative;}
.loading_font{font-size:48px;color:#253d98;}
.ui-widget-header .ui-state-default,.ui-widget-header{
   background:#253d98; 
}
.font_width_bold{font-weight: bold;}
.font_width_bold span{font-weight: normal;}
.error.active{display: none !important;}
.dropdown-content li>span {padding: 4px 16px; }
.dropdown-content li { min-height: 30px; }
.dropdown-content.select-dropdown{max-height: 250px;}
</style>
</head>
<body >
    
<?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id) ) { ?>
    <div id="myProgress">
        <div id="myBar"></div>
    </div>

<?php } ?> 

    <div id="content" class="<?php echo $class??''; ?>">
      <div class="<?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0) echo 'create_app_wrapper_desktop'; else echo  'create_app_wrapper'; if(!empty($khufiya_user_id)) echo ' create_app_wrapper_popup'; ?> container">
        <div class="create_app row">
            <div class="app_img col-xs-12">
               <!--   <img src="https://cdnimages.net/img/dw=487,dh=116,q=90/wholesalebox-credit-logo.jpg" alt="wholesalebox-logo" class="img-responsive"> -->
            </div>     
            <div class="clear"></div>
            
        </div>
 
        <div class="wizard-container">
            <div class="card wizard-card" data-color="orange" id="wizardProfile">

        <?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id)) {?>
        <div class="headerStyle">
                <div class="col-sm-3 logo_width">
                    <a class="home_logo" href="/">
                        <img src="<?php echo STATIC_CONTENT_URL_SSL;?>img/catalog/rsz_wsb_tmp_logo_286.png" alt="wholesalebox-logo" class="img-responsive">
                    </a>
                </div>
            <div>
                <?php if(!empty($back_button) && isset($form_type) && $form_type == '2'){ ?>
                       <div class="back_buttom_class"><a href="<?php echo $form_cancel;?>"><?php echo $credit_language['heading_account']; ?></a></div>
                       <?php }else if(!empty($back_button)){ ?> 
                       <div class="back_buttom_class"><a href="<?php echo $back_button;?>"><?php echo $credit_language['heading_account']; ?></a></div>
                       <?php } ?>
            </div>
        </div>
         <?php } else { ?>

         <div class="headerStyle">
                <div class="col-sm-3 logo_width">
                    <a class="home_logo" href="/">
                        <img src="<?php echo STATIC_CONTENT_URL_SSL;?>img/catalog/rsz_wsb_tmp_logo_286.png" alt="wholesalebox-logo" class="img-responsive">
                    </a>
                </div>
        </div>
         <?PHP } ?>


                <?php if(!empty($update_success)  && $update_success == 'success' ){ ?>
                    <div class="sucess_title">
                    <?php echo $credit_language['heading_credit_form']; ?>
                    </div>
                    <div class="alert alert-success fade in alert-dismissible success-message success_div">

                  <?php echo $credit_language['text_thanks_msg']; ?>
                      </div>
                    <p class="btn btn-info edit-form edit_form_button"><a href="<?php echo $edit; ?>"><?php echo $credit_language['text_edit_form']; ?></a></p>
                    </div>

                    <?php if(empty($khufiya_user_id)) { ?>
                      <?php if(isset($home)) { ?>
                      <p class="btn btn-info edit-form edit_form_button"><a href="<?php echo $home; ?>">Go To Wholesalebox</a></p>
                      <?php } else { ?>
                      <!-- <p class="btn btn-info edit-form edit_form_button"><a href="<?php echo $edit; ?>">Edit Form</a></p> -->
                      <?php } ?>
                    <?php } ?>

                <?php }else{ ?>
                        <div class="wizard-navigation">
                            <ul class="nav nav-pills">

                                 <li>
                                    <?php 
                                    if(!empty($show_header)){ ?>
                                        <a href="#application_form" class="application_form_a" data-toggle="tab"><?php echo $credit_language['heading_credit_form']; ?></a>
                                    <?php }else{ ?>
                                        <a href="#application_form" class="application_form_a" data-toggle="tab">&nbsp</a>
                                    <?php } ?>

                                
                                    <!-- <?php if(!empty($back_button)){ ?>
                                        <a class="back_buttom_class" href="<?php echo $back_button;?>">Back</a>
                                    <?php } ?> -->
                                </li> 
                                 
                            </ul>

                        </div>
                <?php } ?>
                 <?php if(isset($form_type) && $form_type == '1' && empty($update_success)){?>

                            <p>
                            <?php echo $credit_language['text_credit_1']; ?>
                            </p>
                            <p><?php echo $credit_language['text_credit_2']; ?></p>
                            <p><?php echo $credit_language['text_credit_3']; ?></p>
                            <p><?php echo $credit_language['text_credit_call_detail']; ?></p>
                            <p><?php echo $credit_language['text_credit_fill_detail']; ?></p>

                            <?php }else if(empty($update_success)){?>
                            <p class="second_step_title">
                             <?php echo $credit_language['text_credit_4']; ?> 
                            </p>
                            <?php } ?>
                    <div class="tab-content<?php if(CONFIG_IS_MOBILE == 0  && $request_by_app == 0 && !empty($ctoken)) echo ' tab-content-desktop'; if(!empty($khufiya_user_id)) echo ' tab-content-popup'; ?>">
                        <div id="error_msg"></div>
                        <div id="application_form" class="tab-pane application_form form_section">
                            <div class="form-group-outer">

                            <?php if(isset($form_type) && $form_type == '1'){?>
                            <form id="short_credit_application_form" name="short_credit_application_form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                    
<div class="credit_input_field">

<div style="clear: both;"></div>

    <form class="col s12">



      <div class="">
        <div class="input-field col s12">
          <input id="first_name" class="form-control" name="first_name" type="text" value="<?php echo $first_name??''; ?>">
          <label for="first_name"><?php echo $credit_language['entry_firstname']; ?><span class="required">*</span></label>
        </div>
      </div>

      <div class="">
        <div class="input-field col s12">
          <input id="middle_name" class="form-control" name="middle_name" type="text" value="<?php echo $middle_name??''; ?>">
          <label for="middle_name"><?php echo $credit_language['entry_middle']; ?></label>
        </div>
      </div>

      <div class="">
        <div class="input-field col s12">
        <input id="last_name" class="form-control required_input" name="last_name" type="text" value="<?php echo $last_name??''; ?>">
          <label for="last_name"><?php echo $credit_language['entry_lastname']; ?><span class="required">*</span></label>
        </div>
      </div>

        <div class="">
        <div class="input-field col s12">
                                    <input id="father_name" class="form-control" name="father_name" type="text" value="<?php echo $father_name??''; ?>">
          <label for="father_name"><?php echo $credit_language['entry_fathername']; ?><span class="required">*</span></label>
        </div>
      </div>

      <div class="">
        <div class="input-field col s12">
                                    <input id="email" class="form-control" name="email" type="text" value="<?php echo $email??''; ?>">
          <label for="email"><?php echo $credit_language['entry_email']; ?><span class="required">*</span></label>
        </div>
      </div>
      <div class="">
        <div class="input-field col s12">
       <input id="phone_no" class="form-control required_input" name="phone_no" type="tel" minlength="10" maxlength="10" value="<?php echo $phone_no??''; ?>">
          <label for="phone_no"><?php echo $credit_language['entry_telephone']; ?><span class="required">*</span></label>
        </div>
      </div>

<div class="form-group date_field clear">
                                    <lable><?php echo $credit_language['entry_dob']; ?> <span class="required">*</span></lable>
                                    <input id="dob" class="form-control date required_input" name="dob" type="text" value="<?php if(isset($dob) && !empty($dob)) echo date_format(date_create($dob),"d-m-Y") ?>">
                                </div>
<div class="">
        <div class="input-field col s12">
                                    <input id="company_name" class="form-control required_input" name="company_name" type="text" value="<?php echo $company_name??''; ?>">
          <label for="firm_name"><?php echo $credit_language['entry_firmname']; ?><span class="required">*</span></label>
        </div>
      </div>




      <div class="">
        <div class="input-field col s12">
                                     <input id="current_pincode" name="current_pincode" class="form-control required_input" type="tel"  value="<?php echo $current_pincode??''; ?>" minlength="6" maxlength="6">
          <label for="current_pincode"><?php echo $credit_language['entry_pincode']; ?> <span class="required">*</span></label>
        </div>
      </div>

      <div class="">
       <div class="col s12">
          <label style="font-size: 14px; margin-bottom: 0; margin-top: 5px;">
            <?php echo $credit_language['entry_is_gst_number']; ?>
           <span class="required" style="color:#ff0000;">*</span></label>
        
      </div>
      </div>

       <div class="">
       <div class="col s12" style="width:10%; display: inline-block;">
       <input id="gst_number_check" name="gst_number_yes" class="form-control" type="radio" value="1" style="float: left; width: 20px; height: 20px; position: inherit; opacity: 1; pointer-events: auto;" <?php if(!empty($gst_number)) { echo "checked"; } ?>
           <?php if(!empty($gst_number) && empty($khufiya_user_id)) { echo "disabled"; } ?>
        onclick="check_gst_number()" />
       <label style="font-size: 14px; margin-bottom: 0; margin-left: 5px; margin-top: 5px;">
           <?php echo $credit_language['button_yes']; ?>
       </label>
        </div>
        <div class="col s12" style="width:10%; display: inline-block;">
       <input id="gst_number_check" name="gst_number_yes" class="form-control" type="radio"  value="1" style="float: left; width: 20px; height: 20px; position: inherit; opacity: 1; pointer-events: auto;" <?php if(empty($gst_number)) { echo "checked"; } ?> 

       <?php if(!empty($gst_number) && empty($khufiya_user_id)) { echo "disabled"; } ?>

       onclick="check_gst_number()" />
       <label style="font-size: 14px; margin-bottom: 0; margin-left: 5px; margin-top: 5px;">
           <?php echo $credit_language['button_no']; ?>
       </label>
       </div>
      </div>


      <div class="gst_number_div" <?php if(empty($gst_number)) { ?> style="display: none;" <?php } ?>>
        <div class="input-field col s12">
                                     <input id="gst_number" name="gst_number" class="form-control" type="text"  value="<?php echo $gst_number??''; ?>" <?php if(!empty($gst_number) && empty($khufiya_user_id)) { echo "disabled"; } ?> />
          <label for="gst_number"><?php echo $credit_language['entry_gst_number']; ?></label>
        </div>
      </div>


      <div class="form-group" >
                                        <div class="input-field col s12">
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
    <label><?php echo $credit_language['entry_business_year']; ?><span class="required">*</span></label> 
                                        </div>
                                    </div>

                                    <div class="form-group" >
                                        <div class="input-field col s12">
                                            <input id="months_in_current_location" class="form-control" name="months_in_current_location" type="text" value="<?php echo $months_in_current_location??''; ?>">
                                             <?php echo $credit_language['entry_business_months']; ?>
                                        </div>

                                        
                                    </div>
                                   



                                <input name="document_type" type="hidden" value="application_document"> 
                                <input name="customer_id" type="hidden" value="<?php echo $customer_id??'0'; ?>"> 
                                <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id??''; ?>">
                                <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id??''; ?>">
                                <input name="delete_document" type="hidden" value=""> 
                                <input name="draft" type="hidden" value="1">
                                <input type="hidden" name="credit_application_id"  value="<?php echo $id??''; ?>">
<div class="buttons clearfix">
        <div class="col-sm-2"></div>
        <div class="col-sm-9 form_buttons_width">
          <div class="pull-right">
            <button type="submit" value="Submit" class="btn btn-continue submit_btn"><?php echo $credit_language['button_Next']; ?></button>
          </div>
        </div>

        </div>
        </form>


       <?php } else{?> 

    <form id="short_credit_application_form2" name="short_credit_application_form2" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group" >
            <div class="input-field col s12 pan_vali">
                                        <input id="pan_no" class="form-control" name="pan_no" type="text" value="<?php echo $pan_no??''; ?>">
              <label for="pan_no"><?php echo $credit_language['entry_pan']; ?><span class="required">*</span> </label>
            </div>
        </div>

<div class="form-group">
                                    <lable  class="font_width_bold"><?php echo $credit_language['entry_pan']; ?> <span style="font-size: 12px; color: #808080;"><?php echo $credit_language['entry_pan_photo']; ?></span> </lable>
                                    <div class="browse_btn">
                                    <input class="form-control-file only_image" id="pancard" rel="1" name="pancard[]" type="file">
<div class="clear"></div> 
                                    <div class="document_image pancard_upload_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'pancard'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                                                
                                        ?>              
                                                                <div class="doc_image clear">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p><?php echo $credit_language['text_delete_confirm']; ?></p>
                                                                        <button type="button" class="sure_delete" id="<?php echo $doc['id']?>" value="yes"><?php echo $credit_language['button_yes']; ?></button>
                                                                        <button type="button"  class="sure_delete" value="no"><?php echo $credit_language['button_no']; ?></button> 
                                                                    </div>
                                                                     <?php if(!empty($khufiya_user_id)) { ?>
                                                                    <div class="clear">
                                                                         <a class="clear" href="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" target="_blank">
                                                                         <?php echo $credit_language['text_file']; ?></a>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>    
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                           
                                    </div>
                                    <div class="clear"></div> 
                                </div>
                            </div>







<div class="form-group" >
            <div class="input-field col s12">
                                        <input id="aadhaar_no" class="form-control" name="aadhaar_no" type="text" value="<?php echo $aadhaar_no??''; ?>">

              <label for="aadhaar_no"><?php echo $credit_language['entry_aadhar']; ?> <span class="required">*</span></label> 

            </div>
        </div>
   
<div class="form-group" >
    <div id="adhar_parrent_div">
        
            <lable  class="font_width_bold"><?php echo $credit_language['entry_aadhar_detail']; ?></span></lable>

                <div class="browse_btn">
                   
                    <input class="form-control-file fl mrbottom15 only_image" name="address_proof_document[]" rel="2" type="file">
                        <span class="fr" id="add_addhar">
                            <i class="fa fa-plus-circle add_attachment" aria-hidden="true"></i>
                        </span>
                </div>
    </div>
</div>
    <div style="clear: both;"></div>
                                   

                                    <div class="document_image adhar_upload_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'aadhaar_card' || $key == 'address_proof_document'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image clear">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p><?php echo $credit_language['text_delete_confirm']; ?></p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes"><?php echo $credit_language['button_yes']; ?></button>
                                                                        <button type="button"  class="sure_delete" value="no"><?php echo $credit_language['button_no']; ?></button> 
                                                                    </div>
                                                                     <?php if(!empty($khufiya_user_id)) { ?>
                                                                    <div class="clear">
                                                                         <a class="clear" href="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" target="_blank"><?php echo $credit_language['text_file']; ?></a>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        
                                    </div>
<div class="clear"></div> 




<div class="form-group" >

   <div id="bank_document_parrent_div" >
        
            <lable class="font_width_bold">
            <?php echo $credit_language['entry_bank_statement']; ?>
           </lable>

                <div class="browse_btn">
                   
                    <input class="form-control-file fl mrbottom15 only_image allow_pdf" rel="3" id="six_months_bank_statement" name="six_months_bank_statement[]" type="file">

                     <span class="fr" id="add_bank">
                            <i class="fa fa-plus-circle add_bank_attachment" aria-hidden="true"></i>
                        </span>
                </div>
    </div>
</div>
    <div style="clear: both;"></div>
                                    <div class="document_image bank_statement_upload_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'six_months_bank_statement'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                                            $ext = pathinfo($doc['cdn_path']);
                                        ?>  
                                                                <div class="doc_image clear">
                                                                    <?php if(!empty($ext['extension']) && strtolower($ext['extension'])=='pdf'){?>
                                                                    <div class="pdf_file"><a href="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" target="_blank">Bank_document.pdf</a>
                                                                    </div>
                                                                    <?php }else{?> 

                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" />
                                                                    </div>
                                                                    <?php } ?>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image modal-trigger" id="<?php echo $doc['id']?>" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p><?php echo $credit_language['text_delete_confirm']; ?></p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes"><?php echo $credit_language['button_yes']; ?></button>
                                                                        <button type="button"  class="sure_delete" value="no"><?php echo $credit_language['button_no']; ?></button> 
                                                                    </div>
                                                                    <?php if(!empty($khufiya_user_id)) { ?>
                                                                    <div class="clear">
                                                                         <a class="clear" href="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" target="_blank"><?php echo $credit_language['text_file']; ?></a>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        
                                    </div>




<div class="clear"></div> 

<div class="form-group" >
        
            <lable class="font_width_bold"><?php echo $credit_language['entry_photo']; ?></lable>

                <div class="browse_btn">
                   
                    <input class="form-control-file fl mrbottom15 only_image" rel="4" id="shop_photo" name="shop_photo[]" type="file">
                </div>
</div>
    <div style="clear: both;"></div>
                                   

                                    <div class="document_image shop_upload_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'shop_photo'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image clear">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p><?php echo $credit_language['text_delete_confirm']; ?></p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes"><?php echo $credit_language['button_yes']; ?></button>
                                                                        <button type="button"  class="sure_delete" value="no"><?php echo $credit_language['button_no']; ?></button> 
                                                                    </div>
                                                                     <?php if(!empty($khufiya_user_id)) { ?>
                                                                    <div class="clear">
                                                                         <a class="clear" href="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" target="_blank"><?php echo $credit_language['text_file']; ?></a>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        
                                    </div>

                                    <div class="clear"></div> 
<div class="form-group" >
        
            <lable class="font_width_bold"><?php echo $credit_language['entry_selfie']; ?></lable>
                <div class="browse_btn">
                   
                    <input class="form-control-file fl mrbottom15 only_image" rel="5" id="selfie_with_shop" name="selfie_with_shop[]" type="file">
                </div>
</div>
    <div style="clear: both;"></div>
                                   

                                    <div class="document_image selfie_shop_upload_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'selfie_with_shop'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image clear">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p><?php echo $credit_language['text_delete_confirm']; ?></p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes"><?php echo $credit_language['button_yes']; ?></button>
                                                                        <button type="button"  class="sure_delete" value="no"><?php echo $credit_language['button_no']; ?></button> 
                                                                    </div>
                                                                     <?php if(!empty($khufiya_user_id)) { ?>
                                                                    <div class="clear">
                                                                         <a class="clear" href="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" target="_blank"><?php echo $credit_language['text_file']; ?></a>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        
                                    </div>

                                    <div class="clear" style="margin-top: 10px;"></div> 

                                    
      <div class="form-group" >
        
            <lable class="font_width_bold"><?php echo $credit_language['entry_visting_card_photo']; ?></lable>
                <div class="browse_btn">
                   
                    <input class="form-control-file fl mrbottom15 only_image" rel="5" id="visting_card_photo" name="visting_card_photo[]" type="file">
                </div>
</div>
    <div style="clear: both;"></div>
                                   

                                    <div class="document_image visting_card_photo_upload_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'visting_card_photo'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image clear">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['cdn_path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p><?php echo $credit_language['text_delete_confirm']; ?></p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes"><?php echo $credit_language['button_yes']; ?></button>
                                                                        <button type="button"  class="sure_delete" value="no"><?php echo $credit_language['button_no']; ?></button> 
                                                                    </div>
                                                                     <?php if(!empty($khufiya_user_id)) { ?>
                                                                    <div class="clear">
                                                                         <a class="clear" href="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" target="_blank"><?php echo $credit_language['text_file']; ?></a>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        
                                    </div>

                                    <div class="clear" style="margin-top: 10px;"></div>                              


                                    
                                     <label class="different_address_text">
                                                    <input type="checkbox" class="filled-in" id="adhar_address_not_same" name="diffrent_address" <?php if(!empty($diffrent_address)) echo 'checked="checked"'; ?>   />
                                                    <span><p class="diffrent_add_label">
                                                    <?php echo $credit_language['entry_check_current_address']; ?></p></span>
                                                  </label>
                               <!--  -->

                                <div class="form-group additional_form" style="<?php if(!empty($diffrent_address)) echo 'display: block;'; ?>">
                                               
                                              <div class="">
                                            <div class="input-field">
                                           <input id="permanent_address" class="form-control required_input" name="permanent_address"  type="text" value="<?php echo $permanent_address??'';?>">
                                              <label for="permanent_address"><?php echo $credit_language['entry_address']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="">
                                            <div class="input-field">
                                           <input id="permanent_pincode" class="form-control required_input" name="permanent_pincode" type="text" value="<?php echo $permanent_pincode??'';?>">
                                              <label for="permanent_pincode"><?php echo $credit_language['entry_pincode']; ?><span class="required">*</span></label>
                                            </div>
                                          </div>
                                          <div class="">
                                            <div class="input-field">
                                           <input id="permanent_city" class="form-control required_input" name="permanent_city" type="text" value="<?php echo $permanent_city??'';?>">
                                              <label for="permanent_city"><?php echo $credit_language['entry_city']; ?></label>
                                            </div>
                                          </div>
                                          <div class="">
                                            <div class="input-field">
                                           <input id="permanent_state" class="form-control required_input" name="permanent_state" type="text" value="<?php echo $permanent_state??'';?>">
                                              <label for="permanent_state"><?php echo $credit_language['entry_state']; ?></label>
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
</div>


<div class="buttons clearfix marginTop20">
        <div class="col-sm-2 cancel_parent">
<?php if(!empty($form_cancel)){ 

if(isset($show_header) && $show_header=='0' && empty($khufiya_user_id)){?>
                                    <?php if(!empty($back_button)){ ?>
                                        <a href="<?php echo $back_button;?>" class="cancel_btn"><?php echo $credit_language['button_back']; ?></a>
                                    <?php } ?>
<?php     
}else{ ?> 
<?php if(!empty($back_button)){ ?>
 <a class="cancel_btn" href="<?php echo $back_button;?>"><?php echo $credit_language['button_back']; ?></a>
 <?php } ?>
<?php }
} ?></div>
        <div class="col-sm-9 form_buttons_width">
          <div class="pull-right">
            <button type="submit" id="document_upload_form" value="Submit" class="btn btn-continue submit_btn"><?php echo $credit_language['button_submit']; ?></button>
          </div>
        </div>

        </div>
          <div id="dialog-confirm" title="Alert" style="display: none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><?php echo $credit_language['text_delete_confirm']; ?></p>
</div>
                            </form>  
                             </div>
                            <?php } ?> 
                           
                        </div>

                       
                    </div>
            </div>
        </div> <!-- wizard container -->
        <!-- end add wizard ---->
          
          
          
    </div>
</div>
<div class="padding_bottom"></div>
<?php if(CONFIG_IS_MOBILE == 0  && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id) ) { ?>
    <section id="footer_box"></section>
    <section id="login_box"></section>
    <section id="opt_box"></section>
    <section id="register_box"></section>
    <section id="success_box"></section>
    <div class="global-bg-layer"></div>
    <div class="global-ajax-loader"></div>
    
<!--    <script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>-->
    <script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_REACT_JS; ?>"></script>

    <script>

      var header_language     = <?php echo $header_language; ?>;
      var footer_language     = <?php echo $footer_language; ?>;
      var login_language      = <?php echo $login_language ; ?>;
      var international_store = <?php echo $international_store; ?>;

    <?php if (isset($this->session->data['custom_store'])) {
            if ($this->session->data['custom_store'] == 'single') { ?>
    var custom_store        = header_language.text_wholesale_store;
    <?php  } else { ?>
    var custom_store        = header_language.text_singles_store;
    <?php  } }else { ?>
    var custom_store        = header_language.text_singles_store;
    <?php } ?>


    ReactDOM.render(React.createElement(Header, {custom_store:custom_store, language:header_language, international_store:international_store},null ), document.getElementById('header_box'));
    ReactDOM.render(React.createElement(Footer, {language:footer_language, international_store:international_store},null ), document.getElementById('footer_box'));
    ReactDOM.render(React.createElement(Otpform, {language:login_language, international_store:international_store},null ), document.getElementById('opt_box'));
    ReactDOM.render(React.createElement(Login, {language:login_language, international_store:international_store},null ), document.getElementById('login_box'));
    ReactDOM.render(React.createElement(Register, {language:login_language, international_store:international_store},null ), document.getElementById('register_box'));
    ReactDOM.render(React.createElement(Success, {language:login_language, international_store:international_store},null ), document.getElementById('success_box'));

    </script>
<?php } ?>


</body></html>
<script>
    $( document ).ready(function() {
        $('select').formSelect();
        var upload_process='0';
        //datepicker
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

        
        

  $('#close_window').click(function(){
    window.close();
  });
        
var ajax_progress=[];
        
        
        //onchange mandatory_doc
        $(document).on("change", "input[type=file]", function(event) {
            $(this).parents('.upload_image_outer').find('input.required_doc').val('1'); 
            $(this).parents('.upload_optional_image_outer').find('input[name=required_optional_document]').val('1');    
        });
        
        
        $(document).on("click", ".btn-previous", function(event) {
            $('html, body').animate({
                      'scrollTop' : $(".wizard-navigation").position().top
            });
        });
        

       $(document).on("click", "#add_addhar", function(event) {
        var rel = $.now();
                $("#adhar_parrent_div").append('<div class="clear"><input class="form-control-file fl mrbottom15 only_image" name="address_proof_document[]" rel="'+rel+'" type="file"><span class="fr remove_addhar"><i class="fa fa-minus-circle add_attachment" aria-hidden="true"></i></span></div>');
        });

       $(document).on("click", ".remove_addhar", function(event) {
                $(this).parent().remove();
        });

        $(document).on("click", "#add_bank", function(event) {
        var rel = $.now();
                $("#bank_document_parrent_div").append('<div class="clear"><input class="form-control-file fl mrbottom15 only_image allow_pdf" name="six_months_bank_statement[]" rel="'+rel+'" type="file"><span class="fr remove_bank_attach"><i class="fa fa-minus-circle add_attachment" aria-hidden="true"></i></span></div>');
        });
        $(document).on("click", ".remove_bank_attach", function(event) {
                $(this).parent().remove();
        });



        $(document).on("change", ".only_image", function(event) {
            var image_error='0';
                var ext = $(this).val().split('.').pop().toLowerCase();
                if($(this).hasClass('allow_pdf') && $.inArray(ext, ['png','jpg','jpeg','pdf']) == -1){
                        $(this).val('');
                        image_error++;
                        alert('File type not supported.');
                }else if(!$(this).hasClass('allow_pdf') && $.inArray(ext, ['png','jpg','jpeg']) == -1) {
                        $(this).val('');
                        image_error++;
                        alert('File type not supported.');
                    }
                    var file_size = this.files[0].size;
                    var max_file_size = '8388608'; //1048576 = 1MB
                    if(file_size > max_file_size){
                        $(this).val('');
                        image_error++;
                        alert('File must be less than 8MB');
                    }
                    if(image_error=='0'){
                        var image_main_refrence=$(this).attr('rel');
                        //ajax_progress[image_main_refrence]='qqqq';

                       // var loading_html='<div class="doc_image clear"><div class="parrent_progress_div" id="loading_'+image_main_refrence+'"><div class="doc_img doc_img_cross"><a class="cross delete_process_image" id="" rel="'+image_main_refrence+'" ><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div><div class="doc_img loding_img"><div class="loadin_img_loader"><i class="fa fa-circle-o-notch fa-spin loading_font" ></i></div></div></div></div>';
                        var loading_html='<div class="doc_image clear"><div class="parrent_progress_div" id="loading_'+image_main_refrence+'"><div class="doc_img loding_img"><div class="loadin_img_loader"><i class="fa fa-circle-o-notch fa-spin loading_font" ></i></div></div></div></div>';
                        let self_file=$(this);
                        
                        formdata = new FormData();
                        file =$(this).prop('files')[0];
                        formdata.append($(this).prop('name'), file);
                        $(this).val('');
                        $(this).prop('disabled', true);
                        upload_process++;
                        $('#document_upload_form').prop('disabled', true);
                        jQuery.ajax({
                              xhr: function () {
                                if(self_file.prop('name')=='pancard[]'){
                                  self_file.parent().find('.document_image').append(loading_html);   
                                }else if(self_file.prop('name')=='address_proof_document[]'){
                                    $('.adhar_upload_image').append(loading_html);
                                 }
                                 else if(self_file.prop('name')=='shop_photo[]'){
                                    $('.shop_upload_image').append(loading_html);
                                } else if(self_file.prop('name')=='selfie_with_shop[]'){
                                    $('.selfie_shop_upload_image').append(loading_html);
                                }else if(self_file.prop('name')=='visting_card_photo[]'){
                                    $('.visting_card_photo_upload_image').append(loading_html);
                                }
                                 else{
                                    $('.bank_statement_upload_image').append(loading_html);
                                 }
                               
                                        var xhr = new window.XMLHttpRequest();
                                        //Download progress
                                           xhr.addEventListener("progress", function (evt) {
                                        //alert('dddd'); // false
                                        $('#loading_'+image_main_refrence).parent().remove();
                                        self_file.prop('disabled', false);
                                        if (evt.lengthComputable) {
                                            var percentComplete = evt.loaded / evt.total;
                                            console.log(Math.round(percentComplete * 100) + "%");
                                        }
                                    }, false);
                                        return xhr;
                                    },
                                url: ajax_upload,
                                type: "POST",
                                data: formdata,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function (result) {
                                    upload_process--;
                                    if(upload_process=='0'){
                                        $('#document_upload_form').prop('disabled', false);
                                    }
                                    var STATIC_CONTENT_URL_SSL='<?php echo STATIC_CONTENT_URL_SSL;?>';
                                    if(typeof(result.last_image_id) !=='undefined' && typeof(result.file_path) !=='undefined'){
                                                var image_path=STATIC_CONTENT_URL_SSL+'img/dw=213,q=90/'+result.file_path;
                                                if(ext=='pdf'){
                                                    var pdf_path=STATIC_CONTENT_URL_SSL+result.file_path;
                                                    var loading_image_html='<div class="doc_image clear"><div class="doc_img"><div class="pdf_file"><a href="'+pdf_path+'" target="_blank">Bank_document.pdf</a></div></div><div class="doc_img doc_img_cross"><a class="cross delete_image" id="'+result.last_image_id+'"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div><div style="display:none" class="sure_delete_outer"><p>Are you sure to delete ?</p><button type="button" class="sure_delete" id="'+result.last_image_id+'" value="yes">yes</button><button type="button" class="sure_delete" value="no">No</button></div></div>';

                                                }else{
                                                     var loading_image_html='<div class="doc_image clear"><div class="doc_img"><img src="'+image_path+'"></div><div class="doc_img doc_img_cross"><a class="cross delete_image" id="'+result.last_image_id+'"><i class="fa fa-times-circle del_pic" aria-hidden="true"></i></a></div><div style="display:none" class="sure_delete_outer"><p>Are you sure to delete ?</p><button type="button" class="sure_delete" id="'+result.last_image_id+'" value="yes">yes</button><button type="button" class="sure_delete" value="no">No</button></div></div>';
                                                }
                                               

                                                if(self_file.prop('name')=='pancard[]'){
                                                  self_file.parent().find('.document_image').append(loading_image_html);   
                                                }else if(self_file.prop('name')=='address_proof_document[]'){
                                                    $('.adhar_upload_image').append(loading_image_html);
                                                 }
                                                 else if(self_file.prop('name')=='shop_photo[]'){
                                                    $('.shop_upload_image').append(loading_image_html);
                                                 }
                                                 else if(self_file.prop('name')=='selfie_with_shop[]'){
                                                    $('.selfie_shop_upload_image').append(loading_image_html);
                                                 }else  if(self_file.prop('name')=='visting_card_photo[]'){
                                                    $('.visting_card_photo_upload_image').append(loading_image_html);
                                                 }else{
                                                    $('.bank_statement_upload_image').append(loading_image_html);
                                                 }
                                                 self_file.val('');
                                     }else{
                                        alert('Document not upload please try again');
                                     }
                                    
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                      //  alert(xhr.responseText); alert(thrownError);
                                       alert('Document not upload please try again');
                                    },
                            });
                    }
                    
        });


       
        $('input[name=declaration]').change(function (event) {
            if($(this).prop('checked') == 1){
                $(this).val('1');
            }else{
                $(this).val('0');
            }
        });

        $(document).on('change','#business_start_year',function(event){
            $('#business_start_year').parent().parent().find('label').removeClass('errorClass');
            });
        

        $(document).on('focus','input[type=text],input[type=tel]',function(event){
            $(this).parent().find('.error.active').remove();
            $(this).parent().find('label').removeClass('errorClass');
        });
        $(document).on('click','input[type=text],input[type=tel]',function(event){
            $(this).parent().find('.error.active').remove();
            $(this).parent().find('label').removeClass('errorClass');
        });
        $(document).on('blur','input[type=text]',function(event){
            if($.trim($(this).val())!=''){
            $(this).parent().find('.error.active').remove();
            $(this).parent().find('label').removeClass('errorClass');
            }
        });

        $(document).on('submit','#short_credit_application_form',function(event){
            var error ='0';

             if($.trim($('#business_start_year').val())==''){
                $('#business_start_year').parent().parent().find('label').addClass('errorClass');
                error++;
             }
             if($.trim($('#months_in_current_location').val())!='' && $.trim($('#months_in_current_location').val())=='0'){
                $('#months_in_current_location').parent().find('label').addClass('errorClass');
                error++;
             }

             if(error>0){
                return false;  
             }
        });


        $(document).on('submit','#short_credit_application_form2',function(event){
            var error ='0';
            var pan_error = '0';
            var adhar_error = '0';
            var bank_doc_error = '0';
            var adhar_file_insert ='0';
                     $("input[name='aadhaar_card[]']").each(function() {
                        if($.trim($(this).val())!=''){
                           adhar_file_insert++;
                        }   
                    });
             if(adhar_file_insert=='0' && $.trim($('.adhar_upload_image').html())==''){
                //alert('Please Upload Adhar card Photo : Back Side & Front Side image');
                adhar_error++;
             }
              
             if($.trim($('#pancard').val())=='' && $.trim($('.pancard_upload_image').html())==''){
                //alert('Please Upload Pan card front side photo');
                pan_error++;
             }
             if($.trim($('#six_months_bank_statement').val())=='' && $.trim($('.bank_statement_upload_image').html())==''){
                alert('Please Upload 6 month bank statement');
               // bank_doc_error++;
                error++;
             }
             if(bank_doc_error == 1 && adhar_error == 1 && pan_error == 1 ){
                // error++;
                // alert('Please Upload atleast one document.');
             }

              
             // if($.trim($('#pan_no').val())=='' && $.trim($('#aadhaar_no').val())==''){
             //    $('#pan_no').parent().find('label').addClass('errorClass');
             //    $('#aadhaar_no').parent().find('label').addClass('errorClass');
             //    error++;

             // }
             if($.trim($('#business_start_year').val())!='' && $.trim($('#business_start_year').val())=='0'){
                $('#business_start_year').parent().find('label').addClass('errorClass');
                error++;
             }
             if($.trim($('#months_in_current_location').val())!='' && $.trim($('#months_in_current_location').val())=='0'){
                $('#months_in_current_location').parent().find('label').addClass('errorClass');
                error++;
             }

             if($('#adhar_address_not_same').prop('checked')==true){
                if($.trim($('#permanent_address').val())==''){
                error++;
                $('#permanent_address').parent().find('label').addClass('errorClass');
                }
                if($.trim($('#permanent_pincode').val())==''){
                error++;
                $('#permanent_pincode').parent().find('label').addClass('errorClass');
                } 
             }
             if(error>0){
                return false;  
             }
        });

        $(document).on('click','.delete_image_old',function(){
            //alert('asda');
            //$(this).closest('.doc_image').children(".sure_delete_outer").show();
        });
        $(document).on('click','.delete_image',function(event){ 
            try{$('#dialog-confirm').dialog('destroy');}catch(e){}
           $("html, body").animate({ scrollTop: 0 }, "slow");
            var curent_this=$(this);
             var doc_id = $(this).attr('id');
                
                var form_name = $(this).closest("form").attr('name');

                var delete_doc_ids = '';
  $( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
      modal: true,
      dragStop:true,
      buttons: {
        "Yes": function() {
          $( "#dialog-confirm" ).dialog( "close" );
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

                //set value agian
                //alert(delete_doc_ids);
                $('#'+form_name+ ' input[name=delete_document]').val(delete_doc_ids);

                //manage requried if delete all images
                var img_count = curent_this.closest('.document_image').children('.doc_image').length;
                if(img_count == 1){
                    var v = curent_this.closest('.upload_image_section').find('input.required_doc').val('1');
                }

                //remove image
                curent_this.closest('.doc_image').remove();

        },
        Cancel: function() {
          $( "#dialog-confirm" ).dialog( "close" );
        }
      }
    });
});

        
        $(document).on('change','#adhar_address_not_same',function(event){
            if($(this).is(":checked")) {
                $('.additional_form').slideDown();
            }else{
               $('.additional_form').slideUp();
            }
        });

    });
   
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

