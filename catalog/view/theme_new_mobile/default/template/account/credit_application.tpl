<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8" />
    <meta name="google-play-app" content="app-id=in.wholesalebox">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Credit Application</title>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    

<link href="catalog/view/theme_new_mobile/default/stylesheet/all-library.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/jquery/jquery-3.2.0.min.js" type="text/javascript"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/bootstrap.min.js"></script>

<!-- wizard js --->
<script type="text/javascript">
    var ctoken = '<?php echo $ctoken; ?>';
    var draft = '<?php echo $draft; ?>'; 
    var validation_error = '<?php echo $validation_error; ?>'; 
</script>


<script src="catalog/view/theme_new_mobile/default/javascript/wizard/jquery.bootstrap.wizard.js" type="text/javascript"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/gsdk-bootstrap-wizard.js?v=7"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/jquery.validate.min.js"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/wizard/additional-methods.js"></script>
<!-- End wizard js --->


<!---- add for datepicker ---->       
<link href="catalog/view/theme_new_mobile/default/javascript/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css" />
 <script src="catalog/view/theme_new_mobile/default/javascript//bootstrap-datepicker/bootstrap-datepicker.js"></script>
<!---- add for datepicker ---->

<?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id)) { ?>
    <link href="//fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
    <link href="<?php echo LOCAL_CDN_URL_SSL.COMMON_CSS; ?>" rel="stylesheet">
<?php } ?>
  
<style type="text/css">
    .account-credit_application_form{
        background: #fff; 
    }
    .clear{
        clear: both;
    }
   
    .app_img img{
       width: 100%;
       margin: auto;
    }
    
    .create_app_des p{
        color: #233c98;
        font-size: 14px;
    }
    .form_headind{
        background: #233c98;
        color: #fff;
        padding: 5px;
        display:none;
    }
    .text-white{
        color: #233c98;
    }
    .form_section p {
        font-size: 14px;
    }
    .form_section p span{
        font-size: 14px;
    }
    .form_number{
        float: left;
    }
    .form_headind p{ 
        float: left;
        margin: 15px 0px 0 0;
        font-size: 14px;
        /*padding: 0px 20px;*/
        padding: 0px 7px;
    }
    .wizard-navigation ul li a{
        font-size: 15px;
        text-align: center;
        font-weight: bold;
        color: #337ab7;
    }
    .wizard-navigation ul li{
        width: 24%!important;
    }
    .tab-content-desktop{
        width: 50%;
        margin: 0 auto;
    }
    .tab-content-popup{
        width:auto;
    }
    .form{
        padding: 15px 0;
    }
    
    lable{
        font-size: 14px; 
    }
    span.required{
        color: #FF0000;
        
    }
   
    .form-control-checkbox{
        width: 20px;
        height: 20px; 
    }
   
    .radio_input{
        font-size: 14px;
    }
    .radio_button{ 
        width: 1em;
        height: 1em;
    }
    .document_upload h6 {
        font-size: 14px;
        text-decoration: underline;
    }
    .document_upload p {
        font-size: 14px;
    }
    .document_upload p span{
        font-size: 14px;
    }
    .upload_card {
        padding-top: 31px;
    }
   
    .form-group-outer{
        padding-top: 30px;
    }
    
    .form-control-button{
        width: auto;
        background: #233c98;
        color: #fff;
    }
    .form-control-button:hover{ 
        width: auto;
        background: #233c98;
        color: #fff;
    }
    .form-control-button:focus, .form-control-button.focus {
         width: auto;
        background: #233c98;
        color: #fff;
    }
    .bussines_document select {
        margin: 0 0 5px 0;
    }
    .form-group input.error, select.error, textarea.error{
        border-color: #FF3B30;
    }
    .form-group input.error:focus, select.error:focus, textarea.error:focus{
        border-color: #FF3B30;
    }
    .form-group label.error{
        color: #FF3B30;
        font-size : 12px; 
    }
    .declaration p{
        font-size : 12px; 
    }
    .declaration input{
        width: 20px;
        height: 20px; 
    }
    .validation_error{
        margin-top: 25px;
    }
    .document_image {
        padding: 10px 0;
        
    }
    .document_image img{
        width: 212px;
        height: 112px;
        
    }
    .validation_error {
        font-size: 12px;
    }
    .validation_error .close{ 
        font-size: 12px;
    }
    .success-message{
        font-size: 14px;
    }
    .edit-form a{
        color: #fff;
        font-size: 14px;
    }
    .optional_doc{
        /*padding: 15px 0;*/
        border-bottom: #000 solid 1px;
    }
    .date{
        background-color: #fff !important;
    }
    .remove, .remove:focus, .remove:hover {
        color: red;
        text-decoration: none;
        cursor: pointer;
    }
    .add_more, .add_more:focus, .add_more:hover {
        text-decoration: none;
        cursor: pointer;
    }
    .add_more_optional, .add_more_optional:focus, .add_more_optional:hover {
        text-decoration: none;
        cursor: pointer;
    }
    .cross, .cross:focus, .cross:hover{
        font-weight: bold;
        font-size: 14px;
        color: #fff;
        padding: 4px 9px;
        background: red;
        border-radius: 15px;
        text-decoration: none;
    }
    .doc_image{
        float: left;
        margin: 0 10px;
    }
    .doc_img{
        float: left;
    }
    .doc_img_cross{
        margin: -10px;
    }
    .img_cross{
        position: absolute;
        float: left;
    }
    .document_image p{
        clear: both;
    }
    .delete_image{
        cursor: pointer;
    }
    .sure_delete_outer p{
        font-size: 23px;
        color: #FF0000;
    }
    
    @media (min-width: 768px){
        .app_img img{
            width: auto;
        }
        .create_app_wrapper {
            width: 80%;
        }
    }
   
    .create_app_wrapper_desktop {
        width: 72%;
        float: left;
        padding-bottom: 25px;
    }
    .create_app_wrapper_popup{
        float: none;
    }
   
    .padding_bottom{
        padding:15px 0px;
        }
    .h1_text{
        font-size:16px;
    }  
    .upload_style_box {
        border: 1px solid grey; 
        padding-left: 0px;
        padding-right: 0px;
        padding-top: 10px; 
        padding-bottom: 10px;
        margin-bottom: 5px;
    } 

    .month_year {
        display: inline-block;
        width: 49%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s
    }
</style>
 
</head>
<body >
    
<?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id) ) { ?>
    <div id="myProgress">
        <div id="myBar"></div>
    </div>
<section id="header_box"></section>
<?php } ?> 

    <div id="content" class="<?php echo $class; ?>"><?php echo !empty($content_top)?$content_top:''; ?>
      
        <?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0 && !empty($ctoken) && empty($khufiya_user_id)) echo $column_right; ?>
        
      <div class="<?php if(CONFIG_IS_MOBILE == 0 && $request_by_app == 0) echo 'create_app_wrapper_desktop'; else echo  'create_app_wrapper'; if(!empty($khufiya_user_id)) echo ' create_app_wrapper_popup'; ?> container">
        <div class="create_app row">
            <div class="app_img col-xs-12">
               <!--   <img src="https://cdnimages.net/img/dw=487,dh=116,q=90/wholesalebox-credit-logo.jpg" alt="wholesalebox-logo" class="img-responsive"> -->
            </div>     
            <div class="clear"></div>
            
        </div>
        <div class="create_app_des" style="margin-top:20px;">
            <p>You are just few steps away from 0% interest credit limit from &#x20b9 25,000 to &#x20b9 3,00,000 upto 40 days at Wholesatebox!
            </p>
            
            <p>In case of other queries, you can reach out to credit@wholesalebox.in or call us on 8239778680
            </p>
            
        </div> 
          
        <div class="wizard-container">
            <div class="card wizard-card" data-color="orange" id="wizardProfile">
                <?php if($draft == '4' && $validation_error == ''){ ?>
                    <div class="alert alert-success fade in alert-dismissible success-message" style="margin-top:18px; text-align: center">
                        Thanks for contacting us! We will be in touch with you shortly.
                    </div>
                    <p class="btn btn-info edit-form">
                    <?php if(isset($home)) { ?>
                    <a href="<?php echo $home; ?>">Go To Wholesalebox</a>
                    <?php } else { ?>
                    <a href="<?php echo $edit; ?>">Edit Form</a>
                    <?php } ?>
                    </p>
                <?php }else{ ?>
                        <div class="create_app_des">
                            <p>Please fill following details</p>
                        </div> 
                        <div class="wizard-navigation">
                            <ul class="nav nav-pills">
                                <li><a href="#application_form" data-toggle="tab">Application <br>Form</a></li> 
                                <li><a href="#business_form" data-toggle="tab">Business <br>Form</a></li>
                                <li><a href="#application_document" data-toggle="tab">Application <br> Document</a></li>
                                <li><a href="#bussines_document" data-toggle="tab">Business <br> Document</a></li>
                            </ul>
                            <?php if ($validation_error) { ?>
                                <div class="alert alert-danger fade in alert-dismissible validation_error">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">x</a>
                                    <strong>Error!</strong> <?php echo $validation_error; ?>.
                                </div>
                            <?php } ?>
                        </div>
                <?php } ?>
                    <div class="tab-content<?php if(CONFIG_IS_MOBILE == 0  && $request_by_app == 0 && !empty($ctoken)) echo ' tab-content-desktop'; if(!empty($khufiya_user_id)) echo ' tab-content-popup'; ?>">
                        <div id="error_msg"></div>
                        <div id="application_form" class="tab-pane application_form form_section">
                        
                            <div class="form_headind col-xs-8">
                                <div class="form_number">
                                    <span class="fa-stack fa-3x">
                                        <i class="fa fa-circle fa-stack-2x"></i>
                                        <strong class="fa-stack-1x text-white">1</strong>
                                    </span>  
                                </div> 
                                <p>Application Details</p>
                                <div class="clear"></div>
                            </div> 
                            <div class="clear"></div>

                            <div class="form-group-outer">
                            <form id="credit_application_form1" name="application_form" action="<?php echo $action; ?>" method="post"> 
                                <div class="form-group">
                                    <lable>Type of business entity <span class="required">*</span></lable>
                                    <select class="form-control" id="business_entity" name="business_entity_type">
                                        <option value=''>Select Business entity</option>
                                        <option <?php if(!empty($business_entity_type) && $business_entity_type == 'proprietorship') echo 'selected'; ?> value='proprietorship'>Proprietorship</option>
                                        <option <?php if(!empty($business_entity_type) && $business_entity_type == 'partnership') echo 'selected'; ?> value='partnership'>Partnership</option>
                                        <option <?php if(!empty($business_entity_type) && $business_entity_type == 'corporate') echo 'selected'; ?> value='corporate'>Corporate</option>
                                        <option <?php if(!empty($business_entity_type) && $business_entity_type == 'HUF') echo 'selected'; ?> value='HUF'>HUF</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <lable>First Name of Applicant <span class="required">*</span></lable>
                                    <input class="form-control" name="first_name" type="text" value="<?php echo $first_name??''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Middle Name of Applicant</lable>
                                    <input class="form-control" name="middle_name" type="text" value="<?php echo $middle_name??''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Last Name of Applicant <span class="required">*</span></lable>
                                    <input class="form-control" name="last_name" type="text" value="<?php echo $last_name??''; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Father's Name <span class="required">*</span></lable>
                                    <input class="form-control" name="father_name" type="text" value="<?php echo $father_name??''; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Mothers's Name <span class="required">*</span></lable>
                                    <input class="form-control" name="mother_name" type="text" value="<?php echo $mother_name??''; ?>">
                                </div>
                                <div class="form-group">
                                    <lable>Marital status <span class="required">*</span></lable>
                                    <select class="form-control" name="marital_status"> 
                                        <option value="" select>I am..</option>
                                        <option <?php if(!empty($marital_status) && $marital_status == 'Single') echo 'selected'; ?> value="Single">Single</option>
                                        <option <?php if(!empty($marital_status) && $marital_status == 'Married') echo 'selected'; ?> value="Married">Married</option>
                                        <option <?php if(!empty($marital_status) && $marital_status == 'Divorced') echo 'selected'; ?> value="Divorced">Divorced</option>
                                        <option <?php if(!empty($marital_status) && $marital_status == 'Widowed') echo 'selected'; ?> value="Widowed">Widowed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <lable>Applicant PAN Number <span class="required">*</span></lable>
                                    <input class="form-control" name="pan_no" type="text" value="<?php echo $pan_no??''; ?>" maxlength="10">
                                </div>

                                <div class="form-group">
                                    <lable>Applicant Aadhar Number <span class="required">*</span></lable>
                                    <input class="form-control" name="aadhaar_no" type="tel" value="<?php echo $aadhaar_no??''; ?>" minlength="12" maxlength="12">
                                </div>

                                <div class="form-group">
                                    <lable>Date of Birth <span class="required">*</span></lable>
                                    <input id="dob" class="form-control date" name="dob" type="text" value="<?php if(isset($dob)) echo date_format(date_create($dob),"d-m-Y") ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Gender <span class="required">*</span></lable>
                                    <select class="form-control" name="gender"> 
                                        <option value="" select>Please select</option>
                                        <option <?php if(!empty($gender) && $gender == 'male') echo 'selected'; ?> value="male">Male</option>
                                        <option <?php if(!empty($gender) && $gender == 'female') echo 'selected'; ?> value="female">Female</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Education level <span class="required">*</span></lable>
                                    <select class="form-control" name="education">
                                        <option value="" select>Please select</option>
                                        <option <?php if(!empty($education) && $education == 'high school passout') echo 'selected'; ?> value="high school passout">High School Passout</option>
                                        <option <?php if(!empty($education) && $education == 'graduate') echo 'selected'; ?> value="graduate">Graduate</option>
                                        <option <?php if(!empty($education) && $education == 'post-graduate') echo 'selected'; ?> value="post-graduate">Post Graduate</option>
                                        <!-- <option <?php if($education == 'diploma') echo 'selected'; ?> value="diploma">Diploma</option>  -->
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <lable>Phone No. <span class="required">*</span></lable>
                                    <input class="form-control" name="phone_no" type="tel" minlength="10" maxlength="10" value="<?php echo $phone_no??''; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Email <span class="required">*</span></lable>
                                    <input class="form-control" name="email" type="email" value="<?php echo $email??''; ?>">  
                                </div> 

                                <div class="form-group">
                                    <lable>Current Residence Address(Number and Street Name,Apt.)<br>
                                        Kindly mention the address where you are currently residing for faster processing of your application.
                                        <span class="required">*</span>
                                    </lable>
                                    <input id="current_address" name="current_address" class="form-control" type="text" value="<?php echo $current_address??''; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Pin Code <span class="required">*</span></lable>
                                    <input id="current_pincode" name="current_pincode" class="form-control" type="tel"  value="<?php echo $current_pincode??''; ?>" minlength="6" maxlength="6">
                                </div>

                                <div class="form-group">
                                    <lable>City <span class="required">*</span></lable>
                                    <input id="current_city" name="current_city" class="form-control" type="text" value="<?php echo $current_city??''; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>State <span class="required">*</span></lable>
                                    <input id="current_state" name="current_state" class="form-control" type="text" value="<?php echo $current_state??''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Landline Phone number <span class="required">*</span></lable>
                                    <input id="current_landline_phone_no" name="current_landline_phone_no" class="form-control" type="tel"  value="<?php echo $current_landline_phone_no??'' ; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Residential Premises Is <span class="required">*</span></lable>
                                    <select id="current_resident_premises" class="form-control" name="current_resident_premises">
                                        <option value="" select>select resident premises</option> 
                                        <option <?php if(!empty($current_resident_premises) && $current_resident_premises == 'Self Owned') echo 'selected'; ?> value="Self Owned">Self Owned</option>
                                        <option <?php if(!empty($current_resident_premises) && $current_resident_premises == 'Rented') echo 'selected'; ?> value="Rented">Rented</option>
                                        <option <?php if(!empty($current_resident_premises) && $current_resident_premises == 'Family Owned') echo 'selected'; ?> value="Family Owned">Family Owned</option>
                                         <option <?php if(!empty($current_resident_premises) && $current_resident_premises == 'Leased') echo 'selected'; ?> value="Leased">Leased</option>
                                        <!-- 
                                        <option <?php if($current_resident_premises == 'self_owned') echo 'selected'; ?> value="self_owned">Self Owned</option>
                                        <option <?php if($current_resident_premises == 'rented') echo 'selected'; ?> value="rented">Rented</option>
                                        <option <?php if($current_resident_premises == 'family_owned') echo 'selected'; ?> value="family_owned">Family Owned</option>
                                        <option <?php if($current_resident_premises == 'leased') echo 'selected'; ?> value="leased">Leased</option> -->
                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Residential Since<span class="required">*</span></lable>
                                    <div>
                                    <select id="residing_date_month" name="residing_date_month" class="month_year">
                                        <option value="" select>Select Month</option> 
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '01') echo 'selected'; ?> value="01">Jan</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '02') echo 'selected'; ?> value="02">Feb</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '03') echo 'selected'; ?> value="03">Mar</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '04') echo 'selected'; ?> value="04">Apr</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '05') echo 'selected'; ?> value="05">May</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '06') echo 'selected'; ?> value="06">June</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '07') echo 'selected'; ?> value="07">July</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '08') echo 'selected'; ?> value="08">Aug</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '09') echo 'selected'; ?> value="09">Sep</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '10') echo 'selected'; ?> value="10">Oct</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '11') echo 'selected'; ?> value="11">Nov</option>
                                        <option <?php if(!empty($residing_date_month) && $residing_date_month == '12') echo 'selected'; ?> value="12">Dec</option>
                                        
                                    </select>

                                    <select id="residing_date_year" name="residing_date_year" class="month_year">
                                    <option value="" select>Select Year</option> 
                                    <?php $current_year = (int)date('Y'); for($year=$current_year;$year> 1950;$year=$year-1) { ?>
                                        <option <?php if(!empty($residing_date_year) && $residing_date_year == $year ) echo 'selected'; ?> value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php }?>
                                    </select>
                                </div>
                                    <!-- <input id="residing_date" class="form-control" name="residing_date" type="text" value="<?php if(isset($residing_date)) echo $residing_date; ?>">   -->
                                </div>

                                <div class="form-group">
                                    <lable>Permanent address is same as Current Residential Address </lable>
                                    <div class="radio_input">
                                        <input type="checkbox" class="form-control-checkbox" name="same_address">
                                 </div>
                                </div>

                                <div class="permanent_address_div">
                                    <div class="form-group">
                                        <lable>Permanent Address <span class="required">*</span></lable>
                                        <input id="permanent_address" name="permanent_address" class="form-control" type="text"  value="<?php echo $permanent_address??''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <lable>Pin Code <span class="required">*</span></lable>
                                        <input id="permanent_pincode" name="permanent_pincode" class="form-control" type="tel" value="<?php echo $permanent_pincode??''; ?>" minlength="6" maxlength="6">
                                    </div>

                                    <div class="form-group">
                                        <lable>City <span class="required">*</span></lable>
                                        <input id="permanent_city" name="permanent_city" class="form-control" type="text"  value="<?php echo $permanent_city??''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <lable>State <span class="required">*</span></lable>
                                        <input id="permanent_state" name="permanent_state" class="form-control" type="text" value="<?php echo $permanent_state??''; ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <lable>Landline Phone number <span class="required">*</span></lable>
                                        <input id="permanent_landline_phone_no" name="permanent_landline_phone_no" class="form-control" type="tel"  value="<?php echo $permanent_landline_phone_no??'' ; ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <lable>Residential Premises Is <span class="required">*</span></lable>
                                        <select id="permanent_resident_premises" class="form-control" name="permanent_resident_premises">
                                            <option value="" select>select resident premises</option>
                                            <option <?php if(!empty($permanent_resident_premises) && $permanent_resident_premises == 'Self Owned') echo 'selected'; ?> value="Self Owned">Self Owned</option>
                                            <option <?php if(!empty($permanent_resident_premises) && $permanent_resident_premises == 'Rented') echo 'selected'; ?> value="Rented">Rented</option>
                                            <option <?php if(!empty($permanent_resident_premises) && $permanent_resident_premises == 'Family Owned') echo 'selected'; ?> value="Family Owned">Family Owned</option>
                                            <option <?php if(!empty($permanent_resident_premises) && $permanent_resident_premises == 'Leased') echo 'selected'; ?> value="Leased">Leased</option>
                                           <!--  <option <?php if($permanent_resident_premises == 'self_owned') echo 'selected'; ?> value="self_owned">Self Owned</option>
                                            <option <?php if($permanent_resident_premises == 'rented') echo 'selected'; ?> value="rented">Rented</option>
                                            <option <?php if($permanent_resident_premises == 'family_owned') echo 'selected'; ?> value="family_owned">Family Owned</option>
                                            <option <?php if($permanent_resident_premises == 'leased') echo 'selected'; ?> value="leased">Leased</option> -->
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                    <lable>Permanent Residential Since<span class="required">*</span></lable>
                                    <div>
                                    <select id="permanent_date_month" name="permanent_date_month" class="month_year">
                                        <option value="" select>Select Month</option> 
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '01') echo 'selected'; ?> value="01">Jan</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '02') echo 'selected'; ?> value="02">Feb</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '03') echo 'selected'; ?> value="03">Mar</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '04') echo 'selected'; ?> value="04">Apr</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '05') echo 'selected'; ?> value="06">May</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '06') echo 'selected'; ?> value="06">June</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '07') echo 'selected'; ?> value="07">July</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '08') echo 'selected'; ?> value="08">Aug</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '09') echo 'selected'; ?> value="09">Sep</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '10') echo 'selected'; ?> value="10">Oct</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '11') echo 'selected'; ?> value="11">Nov</option>
                                        <option <?php if(!empty($permanent_date_month) && $permanent_date_month == '12') echo 'selected'; ?> value="12">Dec</option>
                                        
                                    </select>

                                    <select id="permanent_date_year" name="permanent_date_year" class="month_year">
                                    <option value="" select>Select Year</option> 
                                    <?php $current_year = (int)date('Y'); for($year=$current_year;$year> 1950;$year=$year-1) { ?>
                                        <option <?php if(!empty($permanent_date_year) && $permanent_date_year == $year ) echo 'selected'; ?> value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php }?>
                                    </select>
                                </div>

                                </div>
                                </div>
                                <input name="customer_id" type="hidden" value="<?php echo $customer_id??''; ?>"> 
                                <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id??''; ?>">
                                <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id??''; ?>">
                                <input name="draft" type="hidden" value="1">
                                <input name="version" type="hidden" value="1">
                                <input type="hidden" name="credit_application_id"  value="<?php echo $id??''; ?>">
                                <div class="pull-right">
                                    <input type='button' class='form-control form-control-button btn btn-next btn-fill btn-warning btn-wd btn-sm' name='next' value='Next' />
                                </div>

                                <div class="pull-left">
                                    <input type='button' class='form-control btn btn-previous btn-fill btn-default btn-wd btn-sm' name='previous' value='Previous' />
                                </div>
                            </form>      
                            </div>
                           
                        </div>





                        <div id="business_form" class="tab-pane business_form form_section">
                        <form id="credit_application_form2" name="business_form" action="<?php echo $action; ?>" method="post">    
                            <div class="form_headind col-xs-8">
                                <div class="form_number">
                                    <span class="fa-stack fa-3x">
                                        <i class="fa fa-circle fa-stack-2x"></i>
                                        <strong class="fa-stack-1x text-white">2</strong>
                                    </span>  
                                </div> 
                                <p>Business Details</p>
                                <div class="clear"></div>
                            </div> 
                            <div class="clear"></div>

                            <div class="form-group-outer">
                                 <div class="form-group">
                                    <lable>Entity Name <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="company_name" value="<?php echo $company_name; ?>">
                                </div>
                                
                                <!-- <div class="form-group">
                                    <lable>Name of the Entity <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="entity_name" value="<?php echo $entity_name; ?>">
                                    <p><span><i class="fa fa-exclamation-circle"></i> As per certificate or registration</span></p>
                                </div> -->
                                
                                <div class="form-group">
                                    <lable>NO of Directors or Partners <span class="required">*</span></lable>
                                    <input class="form-control" type="tel" name="partners" value="<?php echo $partners; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Shop Establishment Number <span class="required">(required if Business PAN is blank)</span></lable>
                                    <input class="form-control" type="text" name="shop_establishment_number" value="<?php echo $shop_establishment_number; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Business PAN Number <span class="required">(required if Shop Establishment No is blank)</span></lable>
                                    <input class="form-control" type="text" name="business_pan_no" value="<?php echo $business_pan_no; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>GST</lable>
                                    <input class="form-control" type="text" name="gst" value="<?php echo $gst; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Trading Name <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="trading_name" value="<?php echo $trading_name; ?>">
                                    <p><span><i class="fa fa-exclamation-circle"></i> if defferent from entity.</span></p>
                                </div>
                                
                                <div class="form-group">
                                    <lable>Nature of Business <span class="required">*</span></lable>
                                    <select class="form-control" name="nature_of_business">
                                        <option value="" select>Select Business Nature</option>
                                        <option <?php if($nature_of_business == 'Retail') echo 'selected'; ?> value="Retail">Retail</option>
                                       <!--  <option <?php if($nature_of_business == 'jewellery') echo 'selected'; ?> value="jewellery">Jewellery</option>
                                        <option <?php if($nature_of_business == 'garment') echo 'selected'; ?> value="garment">Garment</option>
                                        <option <?php if($nature_of_business == 'footwear') echo 'selected'; ?> value="footwear">Footwear</option>
                                        <option <?php if($nature_of_business == 'gift_store') echo 'selected'; ?> value="gift_store">Gift Store</option> -->
                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Business Ownership Status <span class="required">*</span></lable>
                                    <select class="form-control" name="business_ownership">
                                        <option value="" select>Select Business Ownership</option>
                                        <option <?php if($business_ownership == 'Rented') echo 'selected'; ?> value="Rented">Rented</option>
                                        <option <?php if($business_ownership == 'Owned') echo 'selected'; ?> value="Owned">Owned</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Business Segment <span class="required">*</span></lable>
                                    <select class="form-control" name="business_segment">
                                        <option value="" select>Select Business Segment</option>
                                        <option <?php if($business_segment == 'Bill Payments / Digital recharge') echo 'selected'; ?> value="Bill Payments / Digital recharge">Bill Payments / Digital recharge</option>
                                        <option <?php if($business_segment == 'FMCG') echo 'selected'; ?> value="FMCG">FMCG</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Business Vintage in months <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="business_vintage" value="<?php echo $business_vintage; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Months in Current Business <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="months_in_current_business" value="<?php echo $months_in_current_business; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Business Premises is <span class="required">*</span></lable>
                                    <select class="form-control" name="business_premises">
                                        <option value="" select>Select Business premises</option>
                                        <option <?php if($business_premises == 'Self Owned') echo 'selected'; ?> value="Self Owned">Self Owned</option>
                                        <option <?php if($business_premises == 'Rented') echo 'selected'; ?> value="Rented">Rented</option>
                                        <option <?php if($business_premises == 'Family Owned') echo 'selected'; ?> value="Family Owned">Family Owned</option>
                                         <option <?php if($business_premises == 'Leased') echo 'selected'; ?> value="Leased">Leased</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Occupied Since<span class="required">*</span></lable>

                                    <div>
                                    <select id="occupied_since_month" class="month_year" name="occupied_since_month">
                                        <option value="" select>Select Month</option> 
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '01') echo 'selected'; ?> value="01">Jan</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '02') echo 'selected'; ?> value="02">Feb</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '03') echo 'selected'; ?> value="03">Mar</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '04') echo 'selected'; ?> value="04">Apr</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '05') echo 'selected'; ?> value="05">May</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '06') echo 'selected'; ?> value="06">June</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '07') echo 'selected'; ?> value="07">July</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '08') echo 'selected'; ?> value="08">Aug</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '09') echo 'selected'; ?> value="09">Sep</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '10') echo 'selected'; ?> value="10">Oct</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '11') echo 'selected'; ?> value="11">Nov</option>
                                        <option <?php if(!empty($occupied_since_month) && $occupied_since_month == '12') echo 'selected'; ?> value="12">Dec</option>
                                        
                                    </select>

                                    <select id="occupied_since_year" class="month_year" name="occupied_since_year">
                                    <option value="" select>Select Year</option>  
                                    <?php $current_year = (int)date('Y'); for($year=$current_year;$year> 1950;$year=$year-1) { ?>
                                        <option <?php if(!empty($occupied_since_year) && $occupied_since_year == $year ) echo 'selected'; ?> value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php }?>
                                    </select>
                                </div>

                                   <!--  <input id="occupied_since" class="form-control" type="text" name="occupied_since" value="<?php if(isset($occupied_since)) echo $occupied_since; ?>"> -->
                                </div>

                                <div class="form-group">
                                    <lable>Business Address <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="business_address" id="business_address" value="<?php echo $business_address; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Pin Code <span class="required">*</span></lable>
                                    <input class="form-control" type="tel" name="business_pincode" id="business_pincode" value="<?php echo $business_pincode; ?>" minlength="6" maxlength="6">
                                </div>

                                <div class="form-group">
                                    <lable>City <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="business_city" id="business_city" value="<?php echo $business_city; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>State <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="business_state" id="business_state" value="<?php echo $business_state; ?>">
                                </div>

                                <div class="form-group">
                                    
                                    <p><span>Registered Office address is same as Business Address ?</span></p>
                                    <div class="radio_input">
                                        <input type="checkbox" class="form-control-checkbox" name="registered_office_address_same">
                                    </div>
                                </div>
                                <div class="registered_office_address_div">
                                <div class="form-group">
                                    <lable>Address</lable>
                                    <input class="form-control" type="text" name="reg_office_address" id="reg_office_address" value="<?php echo $reg_office_address; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Pin Code <span class="required">*</span></lable>
                                    <input class="form-control" type="tel" name="reg_office_pincode" id="reg_office_pincode" value="<?php echo $reg_office_pincode; ?>" minlength="6" maxlength="6">
                                </div>

                                <div class="form-group">
                                    <lable>City <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="reg_office_city" id="reg_office_city" value="<?php echo $reg_office_city; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>State <span class="required">*</span></lable>
                                    <input class="form-control" type="text" name="reg_office_state" id="reg_office_state" value="<?php echo $reg_office_state; ?>">
                                </div>

                                </div>
                                <div class="form-group">
                                    <lable>Any other associate Entity? </lable>
                                    <div class="radio_input">
                                        <input class="radio_button" type="radio" name="has_other_entity" value="no"  checked="checked">No
                                        <input class="radio_button" type="radio" name="has_other_entity" value="yes">Yes
                                    </div>
                                </div>

                                <div id="other_business_entity" class="form-group" style="display:none">
                                    <lable>Any other associate Entity? <span class="required">*</span></lable>
                                    <textarea rows="2" class="form-control" name="other_business_entity_detail"><?php echo $other_business_entity_detail; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <lable>Doing Business Since <span class="required">*</span></lable>

                                    <div>
                                    <select id="business_since_month" class="month_year" name="business_since_month">
                                        <option value="" select>Select Month</option> 
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '01') echo 'selected'; ?> value="01">Jan</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '02') echo 'selected'; ?> value="02">Feb</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '03') echo 'selected'; ?> value="03">Mar</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '04') echo 'selected'; ?> value="04">Apr</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '05') echo 'selected'; ?> value="05">May</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '06') echo 'selected'; ?> value="06">June</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '07') echo 'selected'; ?> value="07">July</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '08') echo 'selected'; ?> value="08">Aug</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '09') echo 'selected'; ?> value="09">Sep</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '10') echo 'selected'; ?> value="10">Oct</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '11') echo 'selected'; ?> value="11">Nov</option>
                                        <option <?php if(!empty($business_since_month) && $business_since_month == '12') echo 'selected'; ?> value="12">Dec</option>
                                        
                                    </select>

                                    <select id="business_since_year" class="month_year" name="business_since_year">
                                    <option value="" select>Select Year</option>  
                                    <?php $current_year = (int)date('Y'); for($year=$current_year;$year> 1950;$year=$year-1) { ?>
                                        <option <?php if(!empty($business_since_year) && $business_since_year == $year ) echo 'selected'; ?> value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php }?>
                                    </select>
                                </div>

                                    <!-- <input id="business_start_date" class="form-control date" name="business_since" type="text" value="<?php if(isset($business_since)) echo date_format(date_create($business_since),"d-m-Y"); ?>"> -->
                                </div>

                                <div class="form-group">
                                    <lable>Annual Trunover( in Rs. lakhs) <span class="required">*</span></lable>
                                    <select class="form-control" name="annual_turnover">
                                        <option value=''>Select Business premises</option>
                                         <option <?php if($annual_turnover == '5 lakhs') echo 'selected'; ?> value="5 lakhs"> < Rs 5 lakhs</option>
                                        <option <?php if($annual_turnover == '5-10 lakhs') echo 'selected'; ?> value="5-10 lakhs"> Rs 5-10 lakhs</option>
                                        <option <?php if($annual_turnover == '10-20 lakhs') echo 'selected'; ?> value="10-20 lakhs"> Rs 10-20 lakhs</option>
                                        <option <?php if($annual_turnover == '20-50 lakhs') echo 'selected'; ?> value="20-50 lakhs"> Rs 20-50 lakhs</option>
                                        <option <?php if($annual_turnover == '50 lakhs') echo 'selected'; ?> value="50 lakhs"> Rs 50 lakhs</option>
                                        <option <?php if($annual_turnover == '50 lakhs-1.5cr') echo 'selected'; ?>  value="50 lakhs-1.5cr">Rs 50 lakhs-1.5cr</option>
                                        <option <?php if($annual_turnover == '1.5-3cr') echo 'selected'; ?>  value="1.5-3cr">Rs 1.5-3 cr</option>
                                        <option <?php if($annual_turnover == '3-5cr') echo 'selected'; ?>  value="3-5cr">Rs 3-5 cr</option>
                                        <option <?php if($annual_turnover == '5cr') echo 'selected'; ?>  value="5cr"> Rs 5 cr</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <lable>Any litigation,by you or against you</lable>
                                    <div class="radio_input">
                                        <input class="radio_button" type="radio" name="has_litigation" value="no"  checked="checked">No
                                        <input class="radio_button" type="radio" name="has_litigation" value="yes">Yes
                                    </div>
                                </div>

                                <div id="litigation" class="form-group" style="display:none"> 
                                    <lable>Please provide details <span class="required">*</span></lable>
                                    <textarea rows="2" class="form-control" name="litigation"><?php echo $litigation; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <lable>CONTACT PERSON DETAIL</lable>
                                    <p><span>CONTACT PERSON DETAIL are same as Applicant1?</span></p>
                                    <div class="radio_input">
                                        <input class="radio_button" type="radio" name="is_contact_person_same" value="0"  checked="checked">No
                                        <input class="radio_button" type="radio" name="is_contact_person_same" value="1">Yes  
                                    </div>
                                </div>

                                <div class="form-group">
                                    <lable>First Name of contact person <span class="required">*</span></lable>
                                    <input class="form-control" name="contact_person_first_name" type="text" value="<?php echo $contact_person_first_name; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Middle Name of contact person </lable>
                                    <input class="form-control" name="contact_person_middle_name" type="text" value="<?php echo $contact_person_middle_name; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <lable>Last Name of contact person <span class="required">*</span></lable>
                                    <input class="form-control" name="contact_person_last_name" type="text" value="<?php echo $contact_person_last_name; ?>">
                                </div>

                                <div class="form-group contact_person_designation">
                                    <lable>Designation <span class="required">*</span></lable>
                                    <input class="form-control" name="contact_person_designation" type="text" value="<?php echo $contact_person_designation; ?>">
                                </div>

                                <div class="form-group contact_person_relation_with_borrower">
                                    <lable>Relation with the borrower <span class="required">*</span></lable>
                                    <input class="form-control" name="contact_person_relation_with_borrower" type="text" value="<?php echo $contact_person_relation_with_borrower ; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>E-mail address <span class="required">*</span></lable>
                                    <input class="form-control" type="email" name="contact_person_email" value="<?php echo $contact_person_email ; ?>">
                                </div>

                                <div class="form-group">
                                    <lable>Phone number <span class="required">*</span></lable>
                                    <input class="form-control" name="contact_person_phone_no" type="tel"  value="<?php echo $contact_person_phone_no ; ?>" maxlength="10" minlength="10">
                                </div>
                                <?php if($declaration) { ?>
                                <div class="form-group declaration" style="display:none">    
                                <?php } else {?>
                                <div class="form-group declaration">
                                <?php } ?>
                                <lable>Declaration: </lable> 
                                <input type="checkbox" name="declaration" value="<?php echo $declaration; ?>" <?php if($declaration) echo 'checked' ; ?> />   
                                <span class="required">*</span>
                                <p>I/We authorize Wholesalebox Internet Pvt. Ltd. And all its group companies and their agents to exchange, share or part with all the information and details relating to my/our existing loans and/or repayment history to other Group companies, Banks, Financial Institutions, Credit Bureaus, Agencies, Statutory Bodies etc. as may be required or as they deem fit and shall not hold Wholesalebox Internet Pvt. Ltd. or any of its group companies or its/their agents/representatives liable for use/sharing this information. </p>
                                <p>I/We authorize Wholesalebox Internet Pvt. Ltd. to use my PAN number to verify KYC details submitted by me in the Application form. Any such verification will be from an authorized forum only.</p>
                                <p>I/We agree to the terms and conditions to get credit limit to buy from Wholesalebox and hereby authorize Wholesalebox and its affiliate NBFC partners to check my CIBIL score.</p>
                                <p>I/We hereby, authorize Wholesalebox Internet Pvt. Ltd. & its representatives to call or SMS me in relation to this application and other products/services. This consent will override any registration by me for DNC/NDNC.</p>
                                </div>
                                <input name="customer_id" type="hidden" value="<?php echo $customer_id; ?>"> 
                                <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id; ?>">
                                <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id; ?>">
                                <input name="draft" type="hidden" value="2">
                                <div class="pull-right">
                                    <input  type='button' class='form-control form-control-button btn btn-next btn-fill btn-warning btn-wd btn-sm' name='next' value='Next' />
                                </div>

                                <div class="pull-left">
                                    <input type='button' class='form-control btn btn-previous btn-fill btn-default btn-wd btn-sm' name='previous' value='Previous' />
                                </div>
                            </div>
                        </form>    
                        </div>


                        <div id="application_document" class="tab-pane application_document form_section">
                        <form id="credit_application_form3" name="application_document" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">    
                            <div class="form_headind col-xs-8">
                                <div class="form_number">
                                    <span class="fa-stack fa-3x">
                                        <i class="fa fa-circle fa-stack-2x"></i>
                                        <strong class="fa-stack-1x text-white">3</strong>
                                    </span>  
                                </div> 
                                <p>Applicant documents</p> 
                                <div class="clear"></div>
                            </div> 
                            <div class="clear"></div>

                            <div class="form-group-outer">
                                <p>Name : <?php echo $name; ?></p>
                                <p>Father's Name : <?php echo $father_name; ?></p>
                                <p>PAN : <?php echo $pan_no; ?></p>
                                <hr>
                                <p>
                                    <span>Upload<b> required documents</b>. <br> 
                                        Please upload only JPG, JPEG, PNG or PDF file formats. Multiple files can be uploaded together.
                                    </span>
                                </p>

                                <div class="form-group upload_card upload_image_section">
                                    <lable>1.PAN Card <span class="required">*</span></lable>
                                    <input class="required_doc" name="required_pancard" type="hidden" value="<?php echo $required_pancard; ?>">
                                    <input class="form-control-file" name="pancard[]" type="file" multiple>
                                    <div class="document_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'pancard'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                                                
                                        ?>              
                                                                <div class="doc_image">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p>Are you sure delete ?</p>
                                                                        <button type="button" class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                        <button type="button"  class="sure_delete" value="no">No</button> 
                                                                    </div>
                                                                </div>    
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        <div class="clear"></div>    
                                    </div>
                                    
                                </div>

                                <div class="form-group upload_card upload_image_section">
                                    <p>2.Aadhaar Card <span class="required">*</span></p> 
                                    <input class="required_doc" name="required_aadhaar_card" type="hidden" value="<?php echo $required_aadhaar_card; ?>">
                                    <input class="form-control-file" name="aadhaar_card[]" type="file" multiple>
                                    <div class="document_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'aadhaar_card'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p>Are you sure delete ?</p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                        <button type="button"  class="sure_delete" value="no">No</button> 
                                                                    </div>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        <div class="clear"></div> 
                                    </div>
                                </div>

                                

                                <div class="form-group upload_image_section">    
                                    <lable>1.Photograph <span class="required">*</span></lable>
                                    <input class="required_doc" name="required_photo" type="hidden" value="<?php echo $required_photo; ?>">
                                    <input class="form-control-file" name="photo[]" type="file" multiple>
                                    <div class="document_image">
                                        <?php 
                                            if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                foreach($data['application_document'] as $key => $document){
                                                    if($key == 'photo'){
                                                        foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p>Are you sure delete ?</p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                        <button type="button"  class="sure_delete" value="no">No</button> 
                                                                    </div>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                        <div class="clear"></div> 
                                    </div>
                                </div>
                                <hr>
                                <p>
                                    <span>Upload<b> optional documents</b>. <br> 
                                        Please upload only JPG, JPEG, PNG or PDF file formats. Multiple files can be uploaded together.
                                    </span>
                                </p>
                                <div class="application_optional_document">
                                    <h1 class="h1_text">Optional Document</h1>
                                    <div class="form-group upload_voter_id upload_optional_image_section voter_id_images">
                                        <p>1.Voter ID</p>
                                        <div class="col-xs-12 form-group">
                                            <input id="voter_id" class="form-control-file" name="voter_id[]" type="file" multiple>
                                        </div>
                                        <div class="col-xs-12 form-group">
                                            <?php 
                                                if(!empty($data['application_document']['voter_id']) && count($data['application_document']['voter_id']) > 0){
                                                    $voter_id_detail = array_values($data['application_document']['voter_id']); 
                                                }
                                            ?>
                                            <input class="form-control-file" name="voter_id[number]" type="text" value="<?php echo $voter_id_detail[0]['document_number']??'' ?>"
                                            id="voter_id_number" placeholder="Voter Id Number" minlength="10" maxlength="10">
                                        </div>
                                        
                                        <div class="document_image">
                                            <?php 
                                                if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                    foreach($data['application_document'] as $key => $document){
                                                        if($key == 'voter_id'){
                                                            foreach($document as $k => $doc){
                                                                if(count($doc) > 0){

                                            ?>              
                                                                    <div class="doc_image">
                                                                        <div class="doc_img">
                                                                            <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                        </div>
                                                                        <div class="doc_img doc_img_cross">
                                                                            <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                        </div>
                                                                        <div style="display:none" class="sure_delete_outer">
                                                                            <p>Are you sure delete ?</p>
                                                                            <button type="button" class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                            <button type="button" class="sure_delete" value="no">No</button> 
                                                                        </div>
                                                                    </div>    
                                            <?php               } 
                                                            }
                                                        } 
                                                    }
                                                 } 
                                            ?>
                                            <div class="clear"></div>    
                                        </div>
                                    </div>
                                    <div class="form-group upload_voter_id upload_optional_image_section driving_license_images">
                                        <p>2.Driving License</p>
                                        <div class="col-xs-12 form-group">
                                            <input id="driving_license" class="form-control-file" name="driving_license[]" type="file" multiple>
                                        </div>
                                        <div class="col-xs-12 form-group">
                                            <?php 
                                                if(!empty($data['application_document']['driving_license']) && count($data['application_document']['driving_license']) > 0){
                                                    $driving_license_detail = array_values($data['application_document']['driving_license']);
                                                }
                                            ?>
                                            <input class="form-control-file" name="driving_license[number]" type="text" value="<?php echo $driving_license_detail[0]['document_number']??'' ?>"
                                            id="dl_number" placeholder="DL Number" minlength="15" maxlength="15">
                                        </div>
                                        <div class="col-xs-12 form-group">  
                                            <input class="form-control-file document_expiry_date" name="driving_license[expiry_date]" type="text" value="<?php if(isset($driving_license_detail[0]['expiry_date'])) echo date_format(date_create($driving_license_detail[0]['expiry_date']),'d-m-Y') ?>"
                                            id="dl_expiry_date" placeholder="Expiry Date">
                                        </div>
                                        <div class="document_image">
                                            <?php 
                                                if(!empty($data['application_document']) &&  count($data['application_document']) > 0){
                                                    foreach($data['application_document'] as $key => $document){
                                                        if($key == 'driving_license'){
                                                            foreach($document as $k => $doc){
                                                                if(count($doc) > 0){

                                            ?>              
                                                                    <div class="doc_image">
                                                                        <div class="doc_img">
                                                                            <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                        </div>
                                                                        <div class="doc_img doc_img_cross">
                                                                            <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                        </div>
                                                                        <div style="display:none" class="sure_delete_outer">
                                                                            <p>Are you sure delete ?</p>
                                                                            <button type="button" class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                            <button type="button" class="sure_delete" value="no">No</button> 
                                                                        </div>
                                                                    </div>    
                                            <?php               } 
                                                            }
                                                        } 
                                                    }
                                                 } 
                                            ?>
                                            <div class="clear"></div>    
                                        </div>
                                    </div>
                                    
                                    <div class="form-group upload_voter_id upload_optional_image_section passport_images">
                                        <p>3.Passport</p>
                                        <div class="col-xs-12 form-group">
                                            <input class="form-control-file" id="passport_image" name="passport[]" type="file" multiple>
                                        </div>
                                        <div class="col-xs-12 form-group">
                                            <?php 
                                                if(!empty($data['application_document']['passport']) && count($data['application_document']['passport']) > 0){
                                                    $passport_detail = array_values($data['application_document']['passport']);
                                                }
                                            ?>
                                            <input class="form-control-file" name="passport[number]" type="text" value="<?php echo $passport_detail[0]['document_number']??'' ?>" placeholder="Passport Number" minlength="8" maxlength="8" id="passport_number">
                                        </div>
                                        <div class="col-xs-12 form-group">  
                                            <input class="form-control-file document_expiry_date" name="passport[expiry_date]" type="text" value="<?php if(isset($passport_detail[0]['expiry_date'])) echo date_format(date_create($passport_detail[0]['expiry_date']),"d-m-Y") ?>" placeholder="Expiry Date" id="passport_expiry_date">
                                        </div>
                                        
                                        <div class="document_image">
                                            <?php 
                                                if(!empty($data['application_document']) && count($data['application_document']) > 0){
                                                    foreach($data['application_document'] as $key => $document){
                                                        if($key == 'passport'){
                                                            foreach($document as $k => $doc){
                                                                if(count($doc) > 0){

                                            ?>              
                                                                    <div class="doc_image">
                                                                        <div class="doc_img">
                                                                            <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                        </div>
                                                                        <div class="doc_img doc_img_cross">
                                                                            <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                        </div>
                                                                        <div style="display:none" class="sure_delete_outer">
                                                                            <p>Are you sure delete ?</p>
                                                                            <button type="button" class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                            <button type="button" class="sure_delete" value="no">No</button> 
                                                                        </div>
                                                                    </div>    
                                            <?php               } 
                                                            }
                                                        } 
                                                    }
                                                 } 
                                            ?>
                                            <div class="clear"></div>    
                                        </div>
                                        
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                            
                            <input name="document_type" type="hidden" value="application_document"> 
                            <input name="customer_id" type="hidden" value="<?php echo $customer_id; ?>"> 
                            <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id; ?>">
                            <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id; ?>">
                            <input name="delete_document" type="hidden" value=""> 
                            <input name="draft" type="hidden" value="3">
                            <div class="pull-right">
                                <input type='button' class='form-control form-control-button btn btn-next btn-fill btn-warning btn-wd btn-sm' name='next' value='Next' />
                            </div>

                            <div class="pull-left">
                                <input type='button' class='form-control btn btn-previous btn-fill btn-default btn-wd btn-sm' name='previous' value='Previous' />
                            </div>
                        </form>    
                        </div>

                        <div id="bussines_document" class="tab-pane bussines_document form_section">
                        <form id="credit_application_form4" name="bussines_document" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">    
                            <p>
                                <span class="note">Upload all mandatory <b>Business documents</b>.<br> 
                                    Please upload only JPG, JPEG, PNG or PDF file formats.
                                    <div class="MT5">
                                      <b>Please select document type and upload the same. You can
                                        upload mutiple files if the documents is too long to scan.</b>
                                    </div>
                                </span>
                            </p>

                            <div class="form-group form-group-outer"> 
                                <div id="mandatory_documents" class=" upload_image_section">
                                    <p><b>Mandatory Documents <span class="required">*</span></b></p>
                                    <p><lable>Document Type</lable></p>
                                    <div class="row">
                                        <div class="col-xs-12 upload_image_outer">
                                            <div class="col-xs-12 upload_style_box upload_image upload_image_0">
                                                <div class="col-xs-12 col-md-6">
                                                    <select class="form-control mandatory_doc" name="business_proof[]" doc_no="0">
                                                        <option value="">Proof of Business Address</option>
                                                        <option value="electricity_bill">Latest Electricity Bill</option>
                                                        <option value="phone_landline_bill">Latest Phone Landline Bill</option>
                                                        <option value="registered_leave_license_agreement">Registered Leave and License Agreement</option>
                                                        <option value="maintenance_receipt">Maintenance Receipt</option>
                                                        <option value="rental_agreement">Rental Agreement</option>
                                                    </select>
                                                </div>
                                                <div class="col-xs-12 col-md-4">
                                                    <input class="required_doc" name="required_mandatory_document" type="hidden" value="<?php echo $required_mandatory_document; ?>">
                                                    <input class="form-control" type="file" name="">
                                                </div>
                                                
                                            </div>
                                        </div>
                                         <div class="col-xs-12">
                                        <a class="add_more pull-right">Add More</a> 
                                        </div>
                                        <div class="clear"></div>
                                        <div class="col-xs-12 document_image" style="padding-left: 15px;">
                                        <?php 
                                            if(count($data['bussiness_document']) > 0){
                                                foreach($data['bussiness_document'] as $key => $document){
                                                    if($key == 'electricity_bill' 
                                                        || $key == 'phone_landline_bill' 
                                                        || $key == 'registered_leave_license_agreement' 
                                                        || $key == 'maintenance_receipt'  
                                                        || $key == 'rental_agreement'
                                                    ){
                                        ?>                
                                                    <p><?php echo ucfirst(str_replace("_"," ",$key)) ?></p>
                                        <?php           foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="col-xs-10 doc_image">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p>Are you sure delete ?</p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                        <button type="button"  class="sure_delete" value="no">No</button> 
                                                                    </div>
                                                                </div>
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                            <div class="clear"></div>         
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="optional_documents" class="upload_image_section">
                                    <p><b>Optional Documents</b></p>
                                    <p><lable>Document Type</lable> </p>
                                
                                    <div class="row optional_doc">
                                        <div class="col-xs-12 upload_optional_image_outer">
                                            <div class="col-xs-12 upload_style_box upload_optional_image upload_optional_image_0">
                                                <div class="col-xs-12 col-md-6">
                                                    <select class="form-control font12 optional_doc" name="optional_document[]">
                                                        <option value="">Select Optional Type</option>
                                                        <option value="six_months_bank_statement">6 months Bank statement document</option>
                                                        <option value="last_quarter_vat_transactions">Last quarter VAT Transactions</option>
                                                        <option value="income_tax_returns">Income Tax Returns</option>
                                                        <option value="business_pan_no">Business Pan No</option>
                                                        <option value="vat_return">Vat Return</option>
                                                        <option value="business_entity_address_proof">Business Entity Address Proof</option>
                                                        <option value="certificate_of_registration">Certificate Of Registration</option>
                                                    </select>
                                                </div>
                                                <div class="col-xs-12 col-md-4">
                                                    <input name="required_optional_document" type="hidden" value="0">
                                                    <input class="form-control" type="file" name="">
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div> 
                                        <div class="col-xs-12">
                                        <a class="add_more_optional pull-right">Add More</a>
                                        </div>
                                        <div class="col-xs-12 document_image" style="padding-left: 15px;">
                                        <?php 
                                            if(count($data['bussiness_document']) > 0){
                                                foreach($data['bussiness_document'] as $key => $document){
                                                    if($key == 'six_months_bank_statement' 
                                                        || $key == 'last_quarter_vat_transactions' 
                                                        || $key == 'income_tax_returns' 
                                                        || $key == 'business_pan_no' 
                                                        || $key == 'vat_return' 
                                                        || $key == 'business_entity_address_proof' 
                                                        || $key == 'certificate_of_registration' 
                                                    ){
                                        ?>                
                                                    <p><?php echo ucfirst(str_replace("_"," ",$key)) ?></p>
                                        <?php           foreach($document as $k => $doc){
                                                            if(count($doc) > 0){
                                        ?>  
                                                                <div class="doc_image">
                                                                    <div class="doc_img">
                                                                        <img src="<?php echo STATIC_CONTENT_URL_SSL.$doc['path']; ?>" />
                                                                    </div>
                                                                    <div class="doc_img doc_img_cross">
                                                                        <a class="cross delete_image" id="<?php echo $doc['id']?>" >X</a>
                                                                    </div>
                                                                    <div style="display:none" class="sure_delete_outer">
                                                                        <p>Are you sure delete ?</p>
                                                                        <button type="button"  class="sure_delete" id="<?php echo $doc['id']?>" value="yes">yes</button>
                                                                        <button type="button"  class="sure_delete" value="no">No</button> 
                                                                    </div>
                                                                </div> 
                                                                
                                        <?php               } 
                                                        }
                                                    } 
                                                }
                                             }
                                        ?>
                                            <div class="clear"></div> 
                                        </div>
                                    </div>
                                
                                    
                                    
                                </div>
                            </div>
                            <input name="document_type" type="hidden" value="bussiness_document"> 
                            <input name="customer_id" type="hidden" value="<?php echo $customer_id; ?>"> 
                            <input name="crm_user_id" type="hidden" value="<?php echo $crm_user_id; ?>">
                            <input name="khufiya_user_id" type="hidden" value="<?php echo $khufiya_user_id; ?>">
                            <input name="delete_document" type="hidden" value=""> 
                            
                            <input name="draft" type="hidden" value="4">
                            <div class="pull-right">
                                <input type='button' class='form-control form-control-button btn btn-next btn-fill btn-warning btn-wd btn-sm' name='next' value='Next' />
                                <input id="submit_form" type='button' class='form-control form-control-button btn btn-finish btn-fill btn-warning btn-wd btn-sm' name='finish' value='Finish' />
                            </div>

                            <div class="pull-left">
                                <input type='button' class='form-control btn btn-previous btn-fill btn-default btn-wd btn-sm' name='previous' value='Previous' />
                            </div>
                        </form> 
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
        
        //datepicker
        var dt = new Date();
        dt.setFullYear(new Date().getFullYear()-18);
         $('#dob').datepicker({ 
                autoclose: true, 
                todayHighlight: true,
                format: 'dd-mm-yyyy',
                endDate: dt
        }).attr('readonly','readonly');
        
        $('#business_start_date').datepicker({ 
                autoclose: true, 
                todayHighlight: true,
                format: 'dd-mm-yyyy',
                endDate: '+0d'
        }).attr('readonly','readonly');
        
        // $('#residing_date').datepicker({ 
        //         autoclose: true, 
        //         todayHighlight: true,
        //         format: 'dd-mm-yyyy',
        //         endDate: '+0d'
        // }).attr('readonly','readonly');
        
        // $('#occupied_since').datepicker({ 
        //         autoclose: true, 
        //         todayHighlight: true,
        //         format: 'dd-mm-yyyy',
        //         endDate: '+0d'
        // }).attr('readonly','readonly');
        
        $('.document_expiry_date').datepicker({ 
                startDate: new Date(),
                autoclose: true, 
                todayHighlight: true,
                format: 'dd-mm-yyyy',
        }).attr('readonly','readonly');
        
        
        //both address are same or not
        $('input[name=same_address]').change(function (event) {
            
            var address = $('#current_address').val();
            var pincode = $('#current_pincode').val();
            var city = $('#current_city').val();
            var state = $('#current_state').val();
            var landline_phone_no = $('#current_landline_phone_no').val();
            var resident_premises = $('#current_resident_premises').val();
            var residing_date_year = $("#residing_date_year option:selected").val();
            var residing_date_month = $("#residing_date_month option:selected").val();

            if ($(this).prop('checked')==true){ 
                
                $('#permanent_address').val(address); 
                $('#permanent_address').attr('readonly', 'readonly');   
                $('#permanent_address').removeClass('error');
                $('#permanent_address-error').hide();
                
                $('#permanent_pincode').val(pincode);
                $('#permanent_pincode').attr('readonly', 'readonly');   
                $('#permanent_pincode').removeClass('error');
                $('#permanent_pincode-error').hide();
                
                $('#permanent_city').val(city);
                $('#permanent_city').attr('readonly', 'readonly');   
                $('#permanent_city').removeClass('error');
                $('#permanent_city-error').hide();
                
                $('#permanent_state').val(state);
                $('#permanent_state').attr('readonly', 'readonly');   
                $('#permanent_state').removeClass('error');
                $('#permanent_state-error').hide(); 
                
                $('#permanent_landline_phone_no').val(landline_phone_no);
                $('#permanent_landline_phone_no').attr('readonly', 'readonly');   
                $('#permanent_landline_phone_no').removeClass('error');
                $('#permanent_landline_phone_no-error').hide(); 
                
                $('#permanent_resident_premises').val(resident_premises); 
                $('#permanent_resident_premises').attr('readonly', 'readonly');   
                $('#permanent_resident_premises').removeClass('error');
                $('#permanent_resident_premises-error').hide(); 

                $('#permanent_date_year').val(residing_date_year); 
                $('#permanent_date_year').attr('readonly', 'readonly');   
                $('#permanent_date_year').removeClass('error');
                $('#permanent_date_year-error').hide(); 

                $('#permanent_date_month').val(residing_date_month); 
                $('#permanent_date_month').attr('readonly', 'readonly');   
                $('#permanent_date_month').removeClass('error');
                $('#permanent_date_month-error').hide(); 

                $('.permanent_address_div').hide();
            }else{
                $('#permanent_address').val('');
                $('#permanent_address').removeAttr('readonly');   
                
                $('#permanent_pincode').val('');
                $('#permanent_pincode').removeAttr('readonly');   
                
                $('#permanent_city').val('');
                $('#permanent_city').removeAttr('readonly');   
                
                $('#permanent_state').val('');
                $('#permanent_state').removeAttr('readonly');    
                
                $('#permanent_landline_phone_no').val('');
                $('#permanent_landline_phone_no').removeAttr('readonly');    
                
                $('#permanent_resident_premises').val('');
                $('#permanent_resident_premises').removeAttr('readonly');

                $('#permanent_date_year').val('');
                $('#permanent_date_year').removeAttr('readonly');

                $('#permanent_date_month').val('');
                $('#permanent_date_month').removeAttr('readonly');

                $('.permanent_address_div').show();
            }
        });
        
        $('input[name=registered_office_address_same]').change(function (event) {
            
            var address = $('#business_address').val();
            var pincode = $('#business_pincode').val();
            var city = $('#business_city').val();
            var state = $('#business_state').val();
            if ($(this).prop('checked')==true){ 
                
                $('#reg_office_address').val(address);
                $('#reg_office_address').attr('readonly', 'readonly');   
                $('#reg_office_address').removeClass('error');
                $('#reg_office_address-error').hide();
                
                $('#reg_office_pincode').val(pincode);
                $('#reg_office_pincode').attr('readonly', 'readonly');   
                $('#reg_office_pincode').removeClass('error');
                $('#reg_office_pincode-error').hide();
                
                $('#reg_office_city').val(city);
                $('#reg_office_city').attr('readonly', 'readonly');   
                $('#reg_office_city').removeClass('error');
                $('#reg_office_city-error').hide();
                
                $('#reg_office_state').val(state);
                $('#reg_office_state').attr('readonly', 'readonly');   
                $('#reg_office_state').removeClass('error');
                $('#reg_office_state-error').hide(); 
                
                $('.registered_office_address_div').hide();
            }else{
                $('#reg_office_address').val('');
                $('#reg_office_address').removeAttr('readonly');   
                
                $('#reg_office_pincode').val('');
                $('#reg_office_pincode').removeAttr('readonly');   
                
                $('#reg_office_city').val('');
                $('#reg_office_city').removeAttr('readonly');   
                
                $('#reg_office_state').val('');
                $('#reg_office_state').removeAttr('readonly');    

                $('.registered_office_address_div').show();
            }
        });
        //***** onchnage current address change permanent address *****//
        //change address
        $("#current_address").change(function(){
            var current_address = $(this).val();
            var same_address = $('input[name=same_address]:checked').val();
            if(same_address == 'on'){
                $('#permanent_address').val(current_address);
            }
        });
        //change pincode
        $("#current_pincode").change(function(){
            var current_pincode = $(this).val();
            var same_address = $('input[name=same_address]:checked').val();
            if(same_address == 'on'){
                $('#permanent_pincode').val(current_pincode);
            }
        });
        //change city
        $("#current_city").change(function(){
            var current_city = $(this).val();
            var same_address = $('input[name=same_address]:checked').val();
            if(same_address == 'on'){
                $('#permanent_city').val(current_city);
            }
        });
        //change state
        $("#current_state").change(function(){
            var current_state = $(this).val();
            var same_address = $('input[name=same_address]:checked').val();
            if(same_address == 'on'){
                $('#permanent_state').val(current_state); 
            }
        });
        //landline_phone_no
        $("#current_landline_phone_no").change(function(){
            var current_landline_phone_no = $(this).val();
            var same_address = $('input[name=same_address]:checked').val();
            if(same_address == 'on'){
                $('#permanent_landline_phone_no').val(current_landline_phone_no); 
            }
        });
        //landline_phone_no
        $("#current_resident_premises").change(function(){
            var current_resident_premises = $(this).val();
            var same_address = $('input[name=same_address]:checked').val();
            if(same_address == 'on'){
                $('#permanent_resident_premises').val(current_resident_premises); 
            }
        });
        //***** END onchnage current address change permanent address *****//
        
        
        
        //remove default validation
        var is_contact_person_same = '<?php echo $is_contact_person_same ?>';
        if(is_contact_person_same == '1'){
            $("input[name=is_contact_person_same][value='1']").prop("checked",true);
            $('.contact_person_designation').hide(); 
            $('.contact_person_relation_with_borrower').hide(); 
            $('input[name=contact_person_designation]').val(''); 
            $('input[name=contact_person_relation_with_borrower]').val(''); 
        }
        
        
        $('input[name=is_contact_person_same]').change(function (event) {
            var v = $(this).val()
            
            var first_name = $('input[name=first_name]').val();
            var middle_name = $('input[name=middle_name]').val();
            var last_name = $('input[name=last_name]').val();
            var email = $('input[name=email]').val();
            var phone_no = $('input[name=phone_no]').val();
            
            var contact_person_designation = $('input[name=contact_person_designation]').val(); 
            var contact_person_relation_with_borrower = $('input[name=contact_person_relation_with_borrower]').val();
            
            if(v == 1){
                $('input[name=contact_person_first_name]').val(first_name);
                $('input[name=contact_person_first_name]').removeClass('error');
                $('#contact_person_first_name-error').hide();
                
                $('input[name=contact_person_middle_name]').val(middle_name);
                
                $('input[name=contact_person_last_name]').val(first_name);
                $('input[name=contact_person_last_name]').removeClass('error');
                $('#contact_person_last_name-error').hide(); 
                
                $('input[name=contact_person_email]').val(email);
                $('input[name=contact_person_email]').removeClass('error');
                $('#contact_person_email-error').hide();
                
                $('input[name=contact_person_phone_no]').val(phone_no); 
                $('input[name=contact_person_phone_no]').removeClass('error');
                $('#contact_person_phone_no-error').hide();
                
                $('input[name=contact_person_designation]').val(''); 
                $('.contact_person_designation').hide(); 
                
                $('input[name=contact_person_relation_with_borrower]').val('');
                $('.contact_person_relation_with_borrower').hide();
               
                
            }else{
                $('input[name=contact_person_first_name]').val(''); 
                $('input[name=contact_person_first_name]').addClass('error');
                $('#contact_person_first_name-error').show();
                
                $('input[name=contact_person_middle_name]').val(''); 
                
                $('input[name=contact_person_last_name]').val(''); 
                $('input[name=contact_person_last_name]').addClass('error');
                $('#contact_person_last_name-error').show();
                
                $('input[name=contact_person_email]').val('');
                $('input[name=contact_person_email]').addClass('error');
                $('#contact_person_email-error').show();
                
                $('input[name=contact_person_phone_no]').val('');
                $('input[name=contact_person_phone_no]').addClass('error');
                $('#contact_person_phone_no-error').show();
                
                $('input[name=contact_person_designation]').val(''); 
                $('.contact_person_designation').show(); 
                $('input[name=contact_person_designation]').addClass('error');
                
                $('input[name=contact_person_relation_with_borrower]').val('');
                $('.contact_person_relation_with_borrower').show(); 
                $('input[name=contact_person_relation_with_borrower]').addClass('error');
                
            }
        });
        
        //first check on load
        var litigation = '<?php echo substr($litigation, 0, 1); ?>';
        if(litigation != ''){
            $("input[name=has_litigation][value='yes']").prop("checked",true);
            $('#litigation').show();  
        }
        $('input[name=has_litigation]').change(function (event) {
            var v = $(this).val()
            
            if(v == 'yes'){
                $('#litigation').show();  
            }else{
                $('textarea[name=litigation]').val('');
                $('#litigation').hide(); 
                
            }
        });
        
        //first check on load
        var other_business_entity = '<?php echo substr($other_business_entity_detail, 0, 1); ?>';
        if(other_business_entity != ''){
            $("input[name=has_other_entity][value='yes']").prop("checked",true);
            $('#other_business_entity').show();  
        }
        $('input[name=has_other_entity]').change(function (event) {
            var v = $(this).val()
            
            if(v == 'yes'){
                $('#other_business_entity').show();  
            }else{
                $('textarea[name=other_business_entity_detail]').val('');
                $('#other_business_entity').hide(); 
                
            }
        });
        
        
        var i = 0;
        $('.add_more').click(function(){
            i++; 
            
            var html = '';
            html += '<div class="col-xs-12 upload_style_box upload_image upload_image_'+i+'">';
             html +=            '<div class="col-xs-12 col-md-6 mandatory_select">';
//             html +=        '<select class="form-control mandatory_doc" name="business_proof">';
//              html +=           '<option value="">Proof of Business Address</option>'
//               html +=          '<option value="electricity_bill">Latest Electricity Bill</option>'
//               html +=          '<option value="phone_landline_bill">Latest Phone Landline Bill</option>'
//                html +=         '<option value="registered_leave_license_agreement">Registered Leave and License Agreement</option>'
//                 html +=        '<option value="maintenance_receipt">Maintenance Receipt</option>'
//                  html +=       '<option value="rental_agreement">Rental Agreement</option>'
//                   html +=  '</select>'
                 html += '</div>'
                 html += '<div class="col-xs-12 col-md-4">'
                  html +=   '<input name="required_mandatory_document" type="hidden" value="0">'
                  html +=   '<input class="form-control" type="file" name="">'
                html += '</div>'
                html += '<div class="col-xs-12"><a class="remove pull-right">Remove</a></div>'
             html += '<div class="clear"></div>'
             html += '</div>'

            $('.upload_image_outer').append(html);

            var jsonArr = [
                        ['Proof of Business Address',''],
                        ['Latest Electricity Bill', 'electricity_bill'],
                        ['Latest Phone Landline Bill', 'phone_landline_bill'],
                        ['Registered Leave and License Agreement', 'registered_leave_license_agreement'],
                        ['Maintenance Receipt', 'maintenance_receipt'],
                        ['Rental Agreement', 'rental_agreement']
            ];
            
            var ddl = $('<select class="form-control mandatory_doc" doc_no="'+i+'" name="business_proof[]">');
            
            $.each(jsonArr, function (index, item) {
                var opt = $("<option></option>");
                opt.text(item[0]);
                opt.val(item[1]);
                ddl.append(opt);
            });

            $(".upload_image_"+i+' .mandatory_select').append(ddl);
                
                ddl.change(function () {
                    var v = $(this).val();
                    var doc_no = $(this).attr('doc_no');
                    if(v != ''){
                        $('.upload_image_'+doc_no+' input[type=file]').attr('name', v+'[]');
                    }else{
                        $('.upload_image_'+doc_no+' input[type=file]').removeAttr('name');
                    }
                });
                
                $('a.remove').click(function(){
                    $(this).closest(".upload_image").removeClass('upload_style_box');
                    $(this).closest(".upload_image").html('');
                    $(this).closest(".upload_image").fadeOut(300);

                });
              
        });
        $('select.mandatory_doc').on('change', function() {
            var v = $(this).val();
            var doc_no = $(this).attr('doc_no');
            if(v != ''){
                $('.upload_image_0 input[type=file]').attr('name', v+'[]');
            }else{
                $('.upload_image_0 input[type=file]').removeAttr('name');
            }
        });   
        
        
        //---- start optional document add more---->
        
        
        var i = 0;
        $('.add_more_optional').click(function(){
            i++; 
            
            var html = '';
            html += '<div class="col-xs-12 upload_style_box upload_optional_image upload_optional_image_'+i+'">';
             html +=            '<div class="col-xs-12 col-md-6 optional_select">';
                 html += '</div>'
                 html += '<div class="col-xs-12 col-md-4">'
                  html +=   '<input class="form-control" type="file" name="">'
                html += '</div>'
                html += '<div class="col-xs-12"><a class="remove pull-right">Remove</a></div>'
             html += '<div class="clear"></div>'
             html += '</div>'

            $('.upload_optional_image_outer').append(html);

            var jsonArr = [
                        ['Select Optional Type',''],
                        ['6 months Bank statement document', 'six_months_bank_statement'],
                        ['Last quarter VAT Transactions', 'last_quarter_vat_transactions'],
                        ['Income Tax Returns', 'income_tax_returns'],
                        ['Business Pan No', 'business_pan_no'],  
                        ['Vat Return', 'vat_return'], 
                        ['Business Entity Address Proof', 'business_entity_address_proof'],
                        ['Certificate Of Registration', 'certificate_of_registration']
            ];
            
            var ddl = $('<select class="form-control optional_doc" doc_no="'+i+'" name="optional_document[]">');
            
            $.each(jsonArr, function (index, item) {
                var opt = $("<option></option>");
                opt.text(item[0]);
                opt.val(item[1]);
                ddl.append(opt);
            });

            $(".upload_optional_image_"+i+' .optional_select').append(ddl);
                
                ddl.change(function () {
                    var v = $(this).val();
                    var doc_no = $(this).attr('doc_no');
                    if(v != ''){
                        $('.upload_optional_image_'+doc_no+' input[type=file]').attr('name', v+'[]');
                        $('input[name=required_optional_document]').val('1');
                    }else{
                        $('.upload_optional_image_'+doc_no+' input[type=file]').removeAttr('name');
                        $('input[name=required_optional_document]').val('0');
                    }
                });
                
                $('a.remove').click(function(){
                    $(this).closest(".upload_optional_image").removeClass('upload_style_box');
                    $(this).closest(".upload_optional_image").html('');
                    $(this).closest(".upload_optional_image").fadeOut(300);

                });
              
        });
        $('select.optional_doc').on('change', function() {
            var v = $(this).val();
            var doc_no = $(this).attr('doc_no');
            if(v != ''){
                $('.upload_optional_image_0 input[type=file]').attr('name', v+'[]');
                $('input[name=required_optional_document]').val('1');
            }else{
                $('.upload_optional_image_0 input[type=file]').removeAttr('name');
                $('input[name=required_optional_document]').val('0');
            }
        });   
        //---- end optional document add more---->
        
        
        $('.delete_image').click(function(){
            //alert('asda');
            $(this).closest('.doc_image').children(".sure_delete_outer").show();
        });
        
        $('.sure_delete').click(function(event){
            
            event.preventDefault();
            var value = $(this).attr('value');
            
            if(value == 'yes'){
                var doc_id = $(this).attr('id');
                
                var form_name = $(this).closest("form").attr('name');

                var delete_doc_ids = '';
                //first get value
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
                var img_count = $(this).closest('.document_image').children('.doc_image').length;
                if(img_count == 1){
                    var v = $(this).closest('.upload_image_section').find('input.required_doc').val('1');
                }

                //remove image
                $(this).closest('.doc_image').remove();
            }else{
                //remove sure div
                $(this).parent('.sure_delete_outer').hide();
            }
            
            
        });
        
        
        
        
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
        
        $('input[name=declaration]').change(function (event) {
            if($(this).prop('checked') == 1){
                $(this).val('1');
            }else{
                $(this).val('0');
            }
        });

    });
    
</script>          

