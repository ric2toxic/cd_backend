<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="seller_profile">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" id="ms-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>  
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        <?php if( $pending_verification ) { ?>
            <span class="alert-warning">
                <i class="fa fa-exclamation-circle validation_pending"> Verification Pending</i> 
            </span>    
        <?php } ?>  
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" onsubmit="return validation();" method="post" enctype="multipart/form-data" id="form-seller" class="form-horizontal">
            <div class="form-inline new_nickname">
                <fieldset>
                    <legend>Nickname</legend>
                    <?php if( empty($nickname) ){ ?>
                        <select class="form-control" name="new_nickname">
                            <option value="*">--SELECT--</option>
                            <option value="JP">JP</option>
                            <option value="ST">ST</option>
                            <option value="DL">DL</option>
                            <option value="MU">MU</option>
                            <option value="KL">KL</option>
                        </select>
                        <input type="form-control" name="seller_nickname" value="" readonly /> 
                        <label class="set_new_nickname"></label>
                        <?php if ($error_seller_nickname) { ?>
                          <div class="text-danger"><?php echo $error_seller_nickname; ?></div>
                        <?php } ?>
                    <?php } else { ?>
                        <input type="form-control" name="seller_nickname" value="<?php echo $nickname; ?>" readonly /> 
                    <?php } ?>
                </fieldset>
            </div>
            <input type="hidden" id="seller_id" name="seller_id" value="<?php echo $seller_id; ?>" />
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                <li><a href="#product-review" data-toggle="tab"><?php echo 'Product Review'; ?></a></li>
                <li><a href="#change-log" data-toggle="tab"><?php echo 'Change Log'; ?></a></li>
                <li><a href="#promotion" data-toggle="tab"><?php echo $text_promotion; ?></a></li>
                <?php  if( $pending_verification ) { ?>
                <li>
                    <a href="#verification-request" data-toggle="tab" class="verification_request">
                        <?php echo 'Verification Request'; ?>
                            <span class="alert-warning">
                                <i class="fa fa-exclamation-circle"></i> 
                            </span>    
                    </a>
                </li>
                <?php }  ?>  
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-general">
                    <?php if (!empty($number_of_pending_verification) && $number_of_pending_verification > 0 ) { ?>
                        <div class="col-sm-12">
                            <h3 class="alert-warning"> <?php echo $verification_warning_message; ?> </h3>
                        </div>
                    <?php } ?>
                    <div class="col-sm-6">
                        <fieldset>
                            <legend><?php echo $label_basic_data; ?></legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_firm_name; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="seller_firmname" data-change="false" data-old-value="<?php echo $seller_firmname; ?>" name="seller_firmname" value="<?php echo $seller_firmname; ?>" placeholder="<?php echo $entry_firm_name; ?>" />
                                    <?php if ($error_seller_firmname) { ?>
                                      <div class="text-danger"><?php echo $error_seller_firmname; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_product_name_prefix; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="product_name_prefix" data-change="false" data-old-value="<?php echo $product_name_prefix; ?>" name="product_name_prefix" value="<?php echo $product_name_prefix; ?>" placeholder="<?php echo $entry_product_name_prefix; ?>" />
                                    <?php if ($error_product_name_prefix) { ?>
                                      <div class="text-danger"><?php echo $error_product_name_prefix; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_prefix_mode; ?></label>
                                <div class="col-sm-9">
                                    <input type="checkbox" name="prefix_mode"  value="1" class="form-control edit_track" data-block-name="profile" id="prefix_mode" data-change="false" data-old-value="<?php echo $prefix_mode;?>" <?php if( !empty($prefix_mode) ){ ?> checked <?php } ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_telephone; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track mobileno" data-block-name="profile" id="seller_telephone" data-change="false" data-old-value="<?php echo $seller_telephone; ?>" data-field-name="Seller Telephone" name="seller_telephone" value="<?php echo $seller_telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" maxlength="10"/>
                                    <?php if ($error_seller_telephone) { ?>
                                      <div class="text-danger"><?php echo $error_seller_telephone; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_email; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="seller_email" data-change="false" data-old-value="<?php echo $seller_email; ?>" name="seller_email" value="<?php echo $seller_email; ?>" placeholder="<?php echo $entry_email; ?>" />
                                    <?php if ($error_seller_email) { ?>
                                      <div class="text-danger"><?php echo $error_seller_email; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_additional_email; ?></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control edit_track" data-block-name="profile" id="seller_additional_email" data-change="false" data-old-value="<?php echo $seller_additional_email; ?>" rows="5" name="seller_additional_email" placeholder="<?php echo $entry_additional_email; ?>"><?php echo $seller_additional_email; ?></textarea>
                                </div>
                            </div>
                            <?php if( $show_password_box) { ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_password; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="" data-change="false" data-old-value="" name="seller_password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>"/>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_firm_address; ?></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control edit_track" data-block-name="profile" id="firm_address" data-change="false" data-old-value="<?php echo $seller_firm_address; ?>" rows="5" name="seller_firm_address" placeholder="<?php echo $entry_firm_address; ?>"><?php echo $seller_firm_address; ?></textarea>
                                    <?php if ($error_firm_address) { ?>
                                      <div class="text-danger"><?php echo $error_firm_address; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_seller_pincode ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="pincode" data-change="false" data-old-value="<?php echo $pincode; ?>" name="pincode" value="<?php echo $pincode; ?>" placeholder="<?php echo $entry_seller_pincode; ?>" maxlength="6" />
                                    <?php if ($error_pincode) { ?>
                                      <div class="text-danger"><?php echo $error_pincode; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_city ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="city" data-change="false" data-old-value="<?php echo $city; ?>" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" />
                                    <?php if ($error_city) { ?>
                                      <div class="text-danger"><?php echo $error_city; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_country; ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control edit_track" data-block-name="profile" id="seller_country" data-change="false" data-old-value="<?php echo $country_id; ?>" name="country_id" readonly>
                                        <option value="99">India</option>>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_zone; ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control edit_track" data-block-name="profile" id="zone_id" data-change="false" data-old-value="<?php echo $zone_id; ?>" name="zone_id">
                                    </select>
                                    <p class="ms-note"><?php echo $label_entry_zone; ?></p>
                                    <?php /* if ($error_zone_id) { ?>
                                      <div class="text-danger"><?php echo $error_zone_id; ?></div>
                                    <?php }  */ ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input edit_track" data-block-name="profile"
                                           id="same_as_primary_address"
                                           data-old-value="<?php echo $same_as_primary_address; ?>"
                                           data-change="false"  
                                           name="same_as_primary_address" 
                                           value="<?php echo $same_as_primary_address; ?>" 
                                        <?php echo ($same_as_primary_address) ? 'checked' : ''; ?> >
                                    <?php echo $label_same_as_primary; ?>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_pickup_address; ?></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control edit_track" data-block-name="profile" id="pickup_address" data-change="false" data-old-value="<?php echo $pickup_address; ?>" rows="5" name="pickup_address" placeholder="<?php echo $entry_pickup_address; ?>" <?php echo ($same_as_primary_address) ? 'readonly' : ''; ?>><?php echo $pickup_address; ?></textarea>
                                    <?php if ($error_pickup_address) { ?>
                                      <div class="text-danger"><?php echo $error_pickup_address; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_pickup_pincode ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="pickup_pincode" data-change="false" data-old-value="<?php echo $pickup_pincode; ?>" name="pickup_pincode" value="<?php echo $pickup_pincode; ?>" placeholder="<?php echo $entry_pickup_pincode; ?>" <?php echo ($same_as_primary_address) ? 'readonly' : ''; ?> maxlength="6"/>
                                    <?php if ($error_pickup_pincode) { ?>
                                      <div class="text-danger"><?php echo $error_pickup_pincode; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_pickup_city ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="pickup_city" data-change="false" data-old-value="<?php echo $pickup_city; ?>" name="pickup_city" value="<?php echo $pickup_city; ?>" placeholder="<?php echo $entry_pickup_city; ?>" <?php echo ($same_as_primary_address) ? 'readonly' : ''; ?>/>
                                    <?php if ($error_pickup_city) { ?>
                                      <div class="text-danger"><?php echo $error_pickup_city; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_pickup_country; ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control edit_track" data-block-name="profile" id="pickup_country" data-change="false" data-old-value="<?php echo $pickup_country_id; ?>" name="pickup_country" readonly>
                                       <option value="99">India</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_pickup_zone; ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control edit_track" data-block-name="profile" id="pickup_zone" data-change="false" data-old-value="<?php echo $pickup_zone_id; ?>" name="pickup_zone" <?php echo ($same_as_primary_address) ? 'readonly' : ''; ?>>
                                    </select>
                                    <p class="ms-note"><?php echo $label_entry_zone; ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_pickup_city_code; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="profile" id="pickup_city_code" data-change="false" data-old-value="<?php echo $pickup_city_code; ?>" name="pickup_city_code" value="<?php echo $pickup_city_code; ?>" placeholder="<?php echo $entry_pickup_city_code; ?>" maxLength="2">
                                    <?php if ($error_pickup_city_code) { ?>
                                      <div class="text-danger"><?php echo $error_pickup_city_code; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_status; ?></label>
                                <div class="col-sm-9">
                                    <?php $status_value = array('0'=>'--Select--', '1'=>'Active','2'=>'Inactive','3'=>'Disabled',);?>
                                    <select class="form-control edit_track" data-block-name="profile" id="seller_status" data-change="false" data-old-value="<?php echo $seller_status; ?>" name="seller_status">
                                        <?php if($status_value){ ?>
                                        <?php foreach($status_value as $key => $values){ ?>
                                            <?php if($key == $seller_status){ $selected = 'selected'; }else{ $selected = ' ';} ?>
                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $values?></option>
                                        <?php }?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label seller_sor_enable"><?php echo $entry_sor_enabled; ?></label>
                                <div class="col-sm-9">
                                    <div class="sor_enable_checked col-sm-1">
                                        <?php if($sor_enable_checkbox == 1){ ?>
                                            <input type="checkbox" name="sor_enable_checkbox" value="<?php echo $sor_enable_checkbox;?>" class="form-control edit_track" data-block-name="profile" id="sor_enable_checkbox" data-change="false" data-old-value="<?php echo $sor_enable_checkbox;?>" checked />
                                        <?php }else{ ?>
                                            <input type="checkbox" name="sor_enable_checkbox" value="" class="form-control edit_track" data-block-name="profile" id="sor_enable_checkbox" data-change="false" data-old-value="" />
                                        <?php } ?>
                                    </div>
                                    <div class="sor_enable_text_box col-sm-5">
                                        <input type="text" name="seller_markup" value="<?php echo $seller_markup; ?>" class="form-control edit_track" data-block-name="profile" id="seller_markup" data-change="false" data-old-value="<?php echo $seller_markup; ?>" placeholder="<?php echo $entry_seller_markup; ?>" size="10" />
                                        <span><?php echo $entry_seller_markup; ?></span>
                                    </div>
                                    <div class="sor_enable_text_box col-sm-5">
                                        <input type="text" name="wsb_commission" value="<?php echo $wsb_commission; ?>" class="form-control edit_track" data-block-name="profile" id="wsb_commission" data-change="false" data-old-value="<?php echo $wsb_commission; ?>" placeholder="<?php echo $entry_wsb_commission; ?>" size="10"/>
                                        <span><?php echo $entry_wsb_commission; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_app_only; ?></label>
                                <div class="col-sm-9">
                                    <input type="checkbox" name="seller_app_only"  value="1" class="form-control edit_track" data-block-name="profile" id="seller_app_only" data-change="false" data-old-value="<?php echo $seller_app_only;?>" <?php if( $seller_app_only ){ ?> checked <?php } ?> />
                                </div>
                                <?php if ($error_app_only) { ?>
                                  <div class="text-danger"><?php echo $error_app_only; ?></div>
                                <?php } ?>
                                
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_non_returnable; ?></label>
                                <div class="col-sm-9">
                                    <input type="checkbox" name="seller_non_returnable"  value="1" class="form-control edit_track" data-block-name="profile" id="seller_non_returnable" data-change="false" data-old-value="<?php echo $seller_non_returnable;?>" <?php if( $seller_non_returnable ){ ?> checked <?php } ?> />
                                </div>                                
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_non_serviceable_areas; ?></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control edit_track" data-block-name="profile" id="non_serviceable_areas" data-change="false" data-old-value="<?php echo $non_serviceable_areas; ?>" rows="5" name="non_serviceable_areas" placeholder="<?php echo $entry_non_serviceable_areas; ?>" ><?php echo $non_serviceable_areas; ?></textarea>
                                    <p class="ms-note"><?php echo $label_non_service_area; ?></p>
                                    <?php if ($error_service_area) { ?>
                                      <p class="text-danger"><?php echo $error_service_area; ?></p>
                                    <?php } ?>
                                </div>
                            </div>                            
                        </fieldset>
                    </div>
                    <div class="col-sm-6">
                        <fieldset>
                            <legend><?php echo $label_business_data; ?></legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_primary_contact_name ; ?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control edit_track" data-block-name="business" id="primary_contact_name" data-change="false" data-old-value="<?php echo $primary_contact_name; ?>" name="primary_contact_name" value="<?php echo $primary_contact_name; ?>" placeholder="<?php echo $entry_primary_contact_name; ?>" />
                                    <?php if ($error_primary_contact_name) { ?>
                                      <div class="text-danger"><?php echo $error_primary_contact_name; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control edit_track mobileno" data-block-name="business" id="primary_contact_no" data-field-name="Primary Contact No." data-change="false" data-old-value="<?php echo $primary_contact_no; ?>" name="primary_contact_no" value="<?php echo $primary_contact_no; ?>" placeholder="<?php echo $entry_primary_contact_no; ?>" maxlength="10"/>
                                    <?php if ($error_primary_contact_no) { ?>
                                      <div class="text-danger"><?php echo $error_primary_contact_no; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input edit_track" data-block-name="business"
                                               id="same_as_primary_pickup"
                                               data-old-value="<?php echo $same_as_primary_pickup; ?>"
                                               data-change="false"  
                                               name="same_as_primary_pickup" 
                                               value="<?php echo $same_as_primary_pickup; ?>" 
                                            <?php echo ($same_as_primary_pickup) ? 'checked' : ''; ?> >
                                        <?php echo $label_same_as_primary; ?>
                                    </label>
                                </div>
                                <label class="col-sm-3 control-label">
                                    <?php echo $entry_pickup_holder_name ; ?>
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" 
                                            class="form-control edit_track" data-block-name="business" 
                                            id="pickup_holder_name" 
                                            data-change="false" 
                                            data-old-value="<?php echo $pickup_holder_name; ?>" 
                                            name="pickup_holder_name" 
                                            value="<?php echo $pickup_holder_name; ?>" 
                                            placeholder="<?php echo $entry_pickup_holder_name; ?>" 
                                            <?php echo ($same_as_primary_pickup) ? 'readonly' : ''; ?> />
                                    <?php if ($error_pickup_holder_name) { ?>
                                      <div class="text-danger"><?php echo $error_pickup_holder_name; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control edit_track mobileno" data-block-name="business" id="pickup_contact_no" data-field-name="Pickup Contact No." data-change="false" data-old-value="<?php echo $pickup_contact_no; ?>" name="pickup_contact_no" value="<?php echo $pickup_contact_no; ?>" placeholder="<?php echo $entry_pickup_contact_no; ?>" maxlength="10" <?php echo ($same_as_primary_pickup) ? 'readonly' : ''; ?>/>
                                    <?php if ($error_pickup_contact_no) { ?>
                                      <div class="text-danger"><?php echo $error_pickup_contact_no; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input edit_track" data-block-name="business" 
                                                id="same_as_primary_account"
                                                data-old-value="<?php echo $same_as_primary_account; ?>"
                                                data-change="false"
                                             name="same_as_primary_account" 
                                             value="<?php echo $same_as_primary_account; ?>"
                                                <?php echo ($same_as_primary_account) ? 'checked' : '' ; ?> >
                                        <?php echo $label_same_as_primary; ?>
                                    </label>
                                </div>
                                <label class="col-sm-3 control-label"><?php echo $entry_account_holder_name ; ?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control edit_track" data-block-name="business" id="account_holder_name" data-change="false" data-old-value="<?php echo $account_holder_name; ?>" name="account_holder_name" value="<?php echo $account_holder_name; ?>" placeholder="<?php echo $entry_account_holder_name; ?>" <?php echo ($same_as_primary_account) ? 'readonly' : ''; ?> />
                                    <?php if ($error_account_holder_name) { ?>
                                      <div class="text-danger"><?php echo $error_account_holder_name; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control edit_track mobileno" data-block-name="business" id="account_contact_no" data-field-name="Account Contact No." data-change="false" data-old-value="<?php echo $account_contact_no; ?>" name="account_contact_no" value="<?php echo $account_contact_no; ?>" placeholder="<?php echo $entry_account_contact_no; ?>" maxlength="10" <?php echo ($same_as_primary_account) ? 'readonly' : ''; ?> />
                                    <?php if ($error_account_contact_no) { ?>
                                      <div class="text-danger"><?php echo $error_account_contact_no; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="form-check-label">
                                      <input type="checkbox" class="form-check-input edit_track" data-block-name="business" 
                                             data-old-value="<?php echo $same_as_primary_inventory; ?>" 
                                             data-change="false"  
                                             id="same_as_primary_inventory" 
                                             name="same_as_primary_inventory" 
                                             value="<?php echo $same_as_primary_inventory; ?>" 
                                      <?php echo ($same_as_primary_inventory) ? 'checked' : ''; ?> >
                                      <?php echo $label_same_as_primary; ?>
                                    </label>
                                </div>
                                <label class="col-sm-3 control-label"><?php echo $entry_inventory_holder_name ; ?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control edit_track" data-block-name="business" id="inventory_holder_name" data-change="false" data-field-name="Inventory Contact No." data-old-value="<?php echo $inventory_holder_name; ?>" name="inventory_holder_name" value="<?php echo $inventory_holder_name; ?>" placeholder="<?php echo $entry_inventory_holder_name; ?>" <?php echo ($same_as_primary_inventory) ? 'readonly' : ''; ?>/>
                                    <?php if ($error_inventory_holder_name) { ?>
                                      <div class="text-danger"><?php echo $error_inventory_holder_name; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control edit_track mobileno" data-block-name="business" id="inventory_contact_no" data-field-name="Inventory Contact No." data-change="false" data-old-value="<?php echo $inventory_contact_no; ?>" name="inventory_contact_no" value="<?php echo $inventory_contact_no; ?>" placeholder="<?php echo $entry_inventory_contact_no; ?>" maxlength="10" <?php echo ($same_as_primary_inventory) ? 'readonly' : ''; ?> />
                                    <?php if ($error_inventory_contact_no) { ?>
                                      <div class="text-danger"><?php echo $error_inventory_contact_no; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="gst_block_portion">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="col-sm-3 control-label"><?php echo $entry_gst_arn ; ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control edit_track" data-block-name="business" id="gst_arn" data-change="false" data-old-value="<?php echo $gst_arn; ?>" name="gst_arn" value="<?php echo $gst_arn; ?>" placeholder="<?php echo $entry_gst_arn; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <div class="col-sm-12">
                                        <label class="col-sm-3 control-label"><?php echo $entry_gst_provisional_id ; ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control edit_track" data-block-name="business" id="gst_provisional_id" data-change="false" data-old-value="<?php echo $gst_provisional_id; ?>" name="gst_provisional_id" value="<?php echo $gst_provisional_id; ?>" placeholder="<?php echo $entry_gst_provisional_id; ?>" />
                                            <input type="hidden" name="old_gst_provisional_id" value="<?php echo $gst_provisional_id; ?>" />
                                            <?php if ($error_seller_gstin_validation) { ?>
                                                <div class="text-danger"><?php echo $error_seller_gstin_validation; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_pan ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track pan_uppercase" data-block-name="business" id="pan" data-change="false" data-old-value="<?php echo $pan; ?>" name="pan" value="<?php echo $pan; ?>" placeholder="<?php echo $entry_pan; ?>" maxLength="10" readonly/>
                                    <?php if ($error_pan) { ?>
                                      <div class="text-danger"><?php echo $error_pan; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if($tin_taxs_type) { ?>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="col-sm-3 control-label"> <?php echo $label_type_goods; ?></label>
                                    <div class="col-sm-9">
                                        <?php foreach($tin_taxs_type as $tin_val) { ?>
                                            <span class="btn btn-success btn-sm"><?php echo $tin_val; ?></span>
                                        <?php } ?>   
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="col-sm-3 control-label"><?php echo $entry_tin ; ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control edit_track" data-block-name="business" id="tin" data-change="false" data-old-value="<?php echo $tin; ?>" name="tin" value="<?php echo $tin; ?>" placeholder="<?php echo $entry_tin; ?>" />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend><?php echo $label_bank_details; ?></legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_bank_holder_name ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="business" id="bank_ac_holder_name" data-change="false" data-old-value="<?php echo $bank_ac_holder_name; ?>" name="bank_ac_holder_name" value="<?php echo $bank_ac_holder_name; ?>" placeholder="<?php echo $entry_bank_holder_name; ?>" />
                                    <?php if ($error_bank_ac_holder_name) { ?>
                                      <div class="text-danger"><?php echo $error_bank_ac_holder_name; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_account_no ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track" data-block-name="business" id="bank_ac_number" data-change="false" data-old-value="<?php echo $bank_ac_number; ?>" name="bank_ac_number" value="<?php echo $bank_ac_number; ?>" placeholder="<?php echo $entry_account_no; ?>" />
                                    <?php if ($error_bank_ac_number) { ?>
                                      <div class="text-danger"><?php echo $error_bank_ac_number; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $entry_bnk_ifsc_code ; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edit_track seller_ifsc_code" data-block-name="business" id="ifsc_code" data-change="false" data-old-value="<?php echo $ifsc_code; ?>" name="ifsc_code" value="<?php echo $ifsc_code; ?>" placeholder="<?php echo $entry_bnk_ifsc_code; ?>" autocomplete="off" />
                                    <span class="sample_ifsc_code">IFSC Code Ex:- KARB0000001 or BARB0DIGJAI</span>
                                    <?php if ($error_ifsc_code) { ?>
                                      <div class="text-danger"><?php echo $error_ifsc_code; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    <button type="button" id="ifsc_code_loading" class="btn btn-primary btn-xs hidden"></button>
                                    <div class="bank_details_block"></div>
                                </div>
                            </div>
                            <div class="form-group required <?php echo ( !$comment_on ) ? 'hidden' : ''; ?>" >
                                <label class="col-sm-4 control-label"><?php echo $entry_additional_detail; ?></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control edit_track" data-block-name="profile" id="additional_details" data-change="false" data-old-value="" rows="10" name="additional_details" placeholder="<?php echo $entry_additional_detail; ?>"><?php echo $additional_details; ?></textarea>
                                </div>
                                <span class="col-sm-12"><?php echo $label_additional_details; ?></span>
                            </div>
                        </fieldset>
                        <input type="hidden" id="changes_data" name="changes_data" value="<?php echo $changes_data; ?>" />
                    </div>
                </div>
                <div class="tab-pane" id="product-review">
                    <fieldset>
                        <legend><?php echo 'Product Review'; ?></legend>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label"><?php echo 'Global Review'; ?></label>
                                <!--<input type="number" min="3" max="5" class="form-control edit_track" data-block-name="rating" name="categories_global_review" data-change="false" data-old-value="<?php  if(isset($categories_global_review['rating'])) { echo $categories_global_review['rating']; } else{ echo '0';}?>" value="<?php  if(isset($categories_global_review['rating'])) { echo $categories_global_review['rating']; } else{ echo '0';}?>" placeholder="Review" /> -->
                                <select id="select-product-ratings" 
                                        class="form-control edit_track" 
                                        data-block-name="rating" 
                                        name="categories_global_review" 
                                        data-change="false" 
                                        data-old-value="<?php  if(isset($categories_global_review['rating'])) { echo $categories_global_review['rating']; } else{ echo '0';}?>" >
                                    <option value="">--Select--</option>
                                    <?php 
                                        foreach(PRODUCT_RATING_CONFIG as $rating_key => $rating_title) { 
                                            $selected = '';
                                            if($rating_key == $categories_global_review['rating']){
                                                $selected = 'selected';
                                            }
                                    ?>
                                      <option value="<?php echo $rating_key;?>" <?php echo $selected;?>><?php echo $rating_title;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label"><?php echo 'Categories'; ?></label>
                                <?php if($categories){ ?>
                                <select name="categories" class="form-control ">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categories as $category_1) { ?>
                                    <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
                                    <?php foreach ($category_1['children'] as $category_2) { ?>
                                    <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                                    <?php foreach ($category_2['children'] as $category_3) { ?>
                                    <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label"><?php echo 'Review'; ?></label>
                                <!-- <input type="number" value="0" min="3" max="5" class="form-control" name="categories_review" value="0" placeholder="Review" /> -->
                                <select name="categories_review" 
                                        id="select-category-product-ratings" 
                                        class="form-control">
                                    <option value="">--Select--</option>
                                    <?php foreach(PRODUCT_RATING_CONFIG as $rating_key => $rating_title) { ?>
                                      <option value="<?php echo $rating_key;?>"><?php echo $rating_title;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" onclick="addCategoryRow();" data-toggle="tooltip" title="<?php echo 'Add'; ?>" class="btn btn-primary add_button"><i class="fa fa-plus-circle"></i></button>
                            </div>
                        </div>
                    </fieldset>
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="category_rating">
                            <thead>
                            <tr>
                                <td class="text-left">Category</td>
                                <td class="text-left">Rating</td>
                                <td class="text-left">Action</td>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $category_row = 0; ?>
                                <?php foreach ($category_rating as $cate_rating) { ?>
                                <tr id="category-row<?php echo $category_row; ?>">
                                    <td class="text-left"><?php echo $cate_rating['name'];?><input type="hidden" name="category_rating[<?php echo $cate_rating['category_id']; ?>][category_id]" value="<?php echo $cate_rating['category_id']; ?>" id="lable-category<?php echo $category_row; ?>" /></td>
                                    <td class="text-right">
                                        <select name="category_rating[<?php echo $cate_rating['category_id']; ?>][rating]"
                                                id="select-category-product-ratings" 
                                                class="form-control edit_track" 
                                                data-change="false" 
                                                data-block-name="rating" 
                                                data-old-value="<?php echo $cate_rating['rating']; ?>">
                                            <option value="">--Select--</option>
                                            <?php 
                                                foreach(PRODUCT_RATING_CONFIG as $rating_key => $rating_title) { 
                                                    // if any product have prevous rating is 1 or 2 than these rating set with 3.
                                                    if($cate_rating['rating']==1 OR $cate_rating['rating']==2){
                                                        $cate_rating['rating'] = 3;
                                                    }
                                                    
                                                    $selected = '';
                                                    if($rating_key == $cate_rating['rating']){
                                                        $selected = 'selected';
                                                    }
                                            ?>
                                                <option value="<?php echo $rating_key;?>" <?php echo $selected; ?>><?php echo $rating_title;?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="text-left"><button type="button" data-row-no="<?php echo $category_row; ?>" data-toggle="tooltip" title="<?php echo 'Remove'; ?>" class="btn btn-danger remove_row"><i class="fa fa-minus-circle"></i></button></td>
                                </tr>
                                <input type="hidden" name="category_rating[<?php echo $cate_rating['category_id']; ?>][rating]" value="0" placeholder="Rating" class="form-control edit_track product_review_hidden_input" data-change="false" data-block-name="rating" data-old-value="<?php echo $cate_rating['rating']; ?>" id="hidden_row_input_box_<?php echo $category_row; ?>" />
                                <?php $category_row++; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="change-log">
                    <?php if( !empty($change_log) ) { ?>
                        <?php foreach( $change_log as $log_data ) {  ?>
                            <?php
                                $alert_show = '';
                                if($log_data['verification_status'] == 'pending'){
                                    $alert_show = 'alert-warning';      
                                }
                                if($log_data['verification_status'] == 'not_required'){
                                    $alert_show = 'alert-info';      
                                }
                                if($log_data['verification_status'] == 'approved'){
                                    $alert_show = 'alert-success';      
                                }
                                if($log_data['verification_status'] == 'cancelled'){
                                    $alert_show = 'alert-danger';      
                                }
                                if($log_data['verification_status'] == 'admin_updated'){
                                    $alert_show = 'alert-info';      
                                }
                            ?>
                            <div class="alert <?php echo $alert_show; ?>">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                <?php $seller_account_status = array("1" => "Active", "2" => "Inactive","3" => "Disabled");?>
                                <?php $same_as_primary = array("0"=>"Unchecked","1"=>"Checked");?>
                                <?php 
                                        
                                    if( $log_data['update_type'] == 'seller_status'){ 
                                        $log_data['new_value'] = $seller_account_status[$log_data['new_value']] ;
                                    } 

                                    if( $log_data['update_type'] == 'same_as_primary_address'  ||
                                        $log_data['update_type'] == 'same_as_primary_pickup'   ||
                                        $log_data['update_type'] == 'same_as_primary_account'  ||
                                        $log_data['update_type'] == 'same_as_primary_inventory'||
                                        $log_data['update_type'] == 'seller_app_only' ){ 
                                        
                                        if($log_data['previous_value'] == '--NONE--'){ 
                                            $log_data['previous_value'] = $same_as_primary[0] ;
                                        }else{
                                            $log_data['previous_value'] = $same_as_primary[$log_data['previous_value']] ;
                                        }

                                        if($log_data['new_value'] == '--NONE--'){ 
                                            $log_data['new_value'] = $same_as_primary[0] ;
                                        }else{
                                            $log_data['new_value'] = $same_as_primary[$log_data['new_value']] ;
                                        }    
                                    }  
                                ?>
                                <?php if( $log_data['verification_status'] != 'cancelled') { ?>
                                    Previous value: <strong><u><?php echo $log_data['previous_value'];?></u></strong> of <strong><u><?php echo $log_data['update_type'];?></u></strong>
                                    updated with current value: <strong><u> <?php echo $log_data['new_value'];?></u></strong>
                                    in <?php echo $log_data['update_group'];?>
                                    on <?php echo $log_data['date_added'];?> with <strong><u> <?php echo ucfirst($log_data['verification_status']);?></u></strong> status 
                                    <?php if( $log_data['additional_details'] ) { ?>
                                        and additional details is : <?php echo $log_data['additional_details']; ?>
                                    <?php } ?> .
                                <?php } else { ?>
                                    Previous value: <strong><u><?php echo $log_data['previous_value'];?></u></strong> of <strong><u><?php echo $log_data['update_type'];?></u></strong>
                                    not updated with current value: <strong><u> <?php echo $log_data['new_value'];?></u></strong>
                                    in <?php echo $log_data['update_group'];?>
                                    on <?php echo $log_data['date_added'];?> with <strong><u> <?php echo ucfirst($log_data['verification_status']);?></u></strong> status
                                    <?php if( $log_data['additional_details'] ) { ?>
                                        and additional details is : <?php echo $log_data['additional_details']; ?>
                                    <?php } ?> .
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?> 
                </div> 
                <div class="tab-pane" id="promotion">
                    <fieldset>
                        <legend><?php echo $text_seller_promotion; ?></legend>
                    </fieldset>
                    <div class="row-12">
                        <table id="images" class="table table-striped table-bordered table-hover">
                            <thead>
                              <tr>
                                <td class="text-left"><?php echo $entry_category; ?></td>
                                <td class="text-left"><?php echo $entry_product_count; ?></td>
                                <td class="text-left"><?php echo $entry_status; ?></td>
                                <td></td>
                              </tr>
                            </thead>
                            <tbody>
                                <?php   
                                    $promo_row = 0;
                                ?>
                                <?php
                                if(!empty($promotion)){
                                    if( count($promotion) > 0){
                                      foreach ($promotion as $promo) { 
                                        if (!empty($error_promotion[$promo_row]['category']) || !empty($error_promotion[$promo_row]['count']) || !empty($error_promotion[$promo_row]['distinct_category'])) {
                                          $promo_row_error = 1;
                                        }
                                    ?>
                                    <tr id="promo-row<?php echo $promo_row; ?>">
                                        <td class="text-left">
                                            <select id="promo-cat<?php echo $promo_row; ?>" class="form-control" onchange="setProductCount(<?php echo $promo_row; ?>)" name="promotion[<?php echo $promo_row; ?>][category_id]" >
                                                <option value="0" <?php if(empty($promo_row_error)) { echo 'disabled';} ?> ><?php echo $text_select_category; ?></option>    
                                                <?php foreach($promoted_seller_category_list as $cat) { ?>
                                                <option <?php if($promo['category_id'] == $cat['category_id']) echo 'selected'; if(empty($promo_row_error)){ echo 'disabled';} ?> data-count="<?php echo $cat['product_count']; ?>" value="<?php echo $cat['category_id']; ?>" > <?php echo $cat['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if (isset($error_promotion[$promo_row]['category'])) { ?>
                                                <div class="text-danger"><?php echo $error_promotion[$promo_row]['category']; ?></div>
                                            <?php }
                                                  if (isset($error_promotion[$promo_row]['distinct_category'])) { ?>
                                                      <div class="text-danger"><?php echo $error_promotion[$promo_row]['distinct_category']; ?></div>
                                            <?php } ?>
                                            
                                            
                                        </td>
                                        <td class="text-left">
                                            <input class="form-control" type="number" name="promotion[<?php echo $promo_row; ?>][product_count]" value="<?php echo $promo['product_count']; ?>" onchange="validateCount(<?php echo $promo_row; ?>)" <?php if(empty($promo_row_error)) {echo "readonly";} ?> >
                                            <?php if (isset($error_promotion[$promo_row]['count'])) { ?>
                                                <div class="text-danger"><?php echo $error_promotion[$promo_row]['count']; ?></div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-left">
                                            <select class="form-control" name="promotion[<?php echo $promo_row; ?>][status]" <?php if(!empty($promo_row_error)) {echo "readonly";} ?>>
                                                <option <?php if($promo['status'] == 1) echo 'selected'; ?> value="1" ><?php echo $text_enable; ?></option>
                                                <?php if (empty($error_promotion[$promo_row]['category']) && empty($error_promotion[$promo_row]['count']) && empty($error_promotion[$promo_row]['distinct_category'])) { ?>
                                                  <option <?php if($promo['status'] == 0) echo 'selected'; ?> value="0" ><?php echo $text_disable; ?></option>
                                                <?php } ?>
                                                
                                            </select>
                                        </td>
                                        <td class="text-left">
                                          <?php if (!empty($error_promotion[$promo_row]['category']) || !empty($error_promotion[$promo_row]['count']) || !empty($error_promotion[$promo_row]['distinct_category'])) { ?>
                                            <button type="button" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#promo-row<?php echo $promo_row; ?>, .tooltip').remove() : false;" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                                          <?php } ?>
                                        </td>
                                    </tr>
                                    <?php $promo_row++; } 
                                    }else{ ?>
                                        <tr>
                                          <td colspan=4><?php echo $text_no_results; ?></td>
                                        </tr>
                                    
                                    <?php } ?>
                                <?php } ?>    
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="text-left">
                                        <?php 
                                          if(!empty($seller_category_list)) { ?>
                                            <button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_banner_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                                        <?php 
                                          }
                                        ?>
                                        
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="tab-pane" id="verification-request">
                    <?php if( $pending_verification ) { //echo "<pre>"; print_r($pending_verification['gst']); die;?> 
                       
                            <div class="tin">
                                <?php if(!empty($pending_verification['tin']['tin_tax_type'])) {
                                    $tinVal = $pending_verification['tin']['tin_tax_type'];
                                    //echo "<pre>"; print_r($tinVal); die;
                                ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$tinVal['update_type'])); ?> </label>
                                         <div class="col-sm-5">
                                        <?php  foreach($tinVal['tin_tax_type'] as $val) { ?>
                                            <span class="btn btn-success btn-sm"><?php echo $val; ?></span>
                                        <?php } ?>
                                        <input type="hidden" 
                                               class="form-control verified_<?php echo $tinVal['update_type']; ?>" 
                                               name="verified_<?php echo $tinVal['update_type']; ?>" 
                                               value="<?php echo $tinVal['new_value']; ?>"
                                               data-update-id="<?php echo $tinVal['update_id']; ?>" /> 
                                            </div>
                                    </div>       
                                <?php } ?>
                                <?php if(!empty($pending_verification['tin']['tin'])) { 
                                    $tinVal = $pending_verification['tin']['tin'];
                                    $update_id = $pending_verification['tin']['tin_tax_type']['update_id']  . ',' .
                                                 $pending_verification['tin']['tin']['update_id']  . ',' . 
                                                 $pending_verification['tin']['tin_image']['update_id'] ;


                                    $update_type = $pending_verification['tin']['tin_tax_type']['update_type']  . ',' .
                                                  $pending_verification['tin']['tin']['update_type']  . ',' . 
                                                  $pending_verification['tin']['tin_image']['update_type'] ;

                                    $address1 = '';
                                    $address2 = '';
                                    $city     = '';
                                    $pincode  = '';
                                    $seller_zone_id = '';
                                    $show_tin_button = false;          

                                    if( !empty( $pending_verification['tin']['primary_address_verify'] ) ){
                                        $update_id .= ',' .implode(',',array_values($data['pending_verification']['tin']['primary_address_verify']['update_id']));
                                        $update_type .= ',' .
                                                  implode(',',array_keys($data['pending_verification']['tin']['primary_address_verify']['update_type']));
                                        $address1 = $pending_verification["tin"]["primary_address_verify"]["update_type"]["address1"];
                                        $address2 = $pending_verification["tin"]["primary_address_verify"]["update_type"]["address2"];
                                        $city     = $pending_verification["tin"]["primary_address_verify"]["update_type"]["city"];
                                        $pincode  = $pending_verification["tin"]["primary_address_verify"]["update_type"]["pincode"];
                                        $seller_zone_id  = $pending_verification["tin"]["primary_address_verify"]["update_type"]["zone_id"];

                                        $show_tin_button = true;   

                                    }

                                ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$tinVal['update_type'])); ?> </label>
                                        <div class="col-sm-5">
                                            <input type="text" 
                                                   class="form-control verified_<?php echo $tinVal['update_type']; ?>" 
                                                   name="verified_<?php echo $tinVal['update_type']; ?>" 
                                                   value="<?php echo $tinVal['new_value']; ?>" 
                                                   placeholder="<?php echo $tinVal['update_type']; ?>" 
                                                   data-update-id="<?php echo $tinVal['update_id']; ?>"/>
                                        </div>

                                        <?php if ( $show_tin_button ) { ?>  
                                        
                                        <a href="javascript:void(0);"
                                            id="approved_<?php echo $tinVal['update_type']; ?>" 
                                            class="btn btn-success btn_approved" 
                                            data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"approved", "update_type" => $update_type, "seller_data" => array("tin_tax_type"=>$pending_verification["tin"]["tin_tax_type"]["new_value"], "tin"=>$tinVal["new_value"], "tin_image"=>$pending_verification["tin"]["tin_image"]["new_value"],"address1"=>$address1,"address2"=>$address2,"city"=>$city,"pincode"=>$pincode, "zone_id"=>$seller_zone_id )));?>' 
                                            data-check-update-type = "tin">
                                            <i class="fa fa-check"></i>
                                        </a>
                                        <a href="javascript:void(0);" 
                                            id="cancel_<?php echo $tinVal['update_type']; ?>"
                                            class="btn btn-danger btn_cancel" 
                                            data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"cancelled", "update_type" => $update_type));?>'
                                            data-check-update-type = "tin">
                                            <i class="fa fa-trash"></i>
                                        </a>

                                        <?php if(!empty($pending_verification['tin']['tin_image'])) { 
                                            $tinImage = $pending_verification['tin']['tin_image'];
                                        ?>                                                           
                                            <a id="test_<?php echo $tinImage['update_id']; ?>" href="" type="button" class="btn btn-warning  download_verify_image" download><i class="fa fa-download"></i></a>
                                            <input type="hidden" value="<?php echo $tinImage['images']; ?>" id="img" class="verification_image" data-id="<?php echo $tinImage['update_id']; ?>" name="download_img_<?php echo $tinImage['update_type']; ?>" data-update-id="<?php echo $tinImage['update_id']; ?>"  />
                                        <?php } ?>
                                        <?php } ?>                                       
                                    </div>
                                <?php } ?>
                            </div>

                        <div class="pan">
                        <?php if(!empty($pending_verification['pan']['pan'])) {
                            $tinVal = $pending_verification['pan']['pan'];
                            $update_id = $pending_verification['pan']['pan']['update_id']  . ',' .
                                         $pending_verification['pan']['pan_image']['update_id'];

                            $update_type = $pending_verification['pan']['pan']['update_type']  . ',' .
                                         $pending_verification['pan']['pan_image']['update_type'];                                             

                         ?> 
                        
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$tinVal['update_type'])); ?> </label>
                                <div class="col-sm-5">
                                    <input type="text" 
                                           class="form-control verified_<?php echo $tinVal['update_type']; ?>" 
                                           name="verified_<?php echo $tinVal['update_type']; ?>" 
                                           value="<?php echo $tinVal['new_value']; ?>" 
                                           placeholder="<?php echo $tinVal['update_type']; ?>" 
                                           data-update-id="<?php echo $tinVal['update_id']; ?>"/>
                                </div>
                                
                                <a href="javascript:void(0);"
                                    id="approved_<?php echo $tinVal['update_type']; ?>" 
                                    class="btn btn-success btn_approved" 
                                    data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"approved", "update_type" => $update_type, "seller_data" => array("pan"=>$tinVal["new_value"], "pan_image"=>$pending_verification["pan"]["pan_image"]["new_value"])));?>'
                                    data-check-update-type = "pan">
                                    <i class="fa fa-check"></i>
                                </a>
                                <a href="javascript:void(0);" 
                                    id="cancel_<?php echo $tinVal['update_type']; ?>"
                                    class="btn btn-danger btn_cancel" 
                                    data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"cancelled", "update_type" => $update_type));?>'
                                    data-check-update-type = "pan">
                                    <i class="fa fa-trash"></i>
                                </a>
                                <?php if(!empty($pending_verification['pan']['pan_image'])) { 
                                    $panImage = $pending_verification['pan']['pan_image'];
                                ?>
                                    <a id="test_<?php echo $panImage['update_id']; ?>" href="" type="button" class="btn btn-warning  download_verify_image" download><i class="fa fa-download"></i></a>
                                    <input type="hidden" value="<?php echo $panImage['images']; ?>" id="img" class="verification_image" data-id="<?php echo $panImage['update_id']; ?>" name="download_img_<?php echo $panImage['update_type']; ?>" data-update-id="<?php echo $panImage['update_id']; ?>"  />                                    
                                <?php } ?>  
                            </div>
                        <?php } ?> 
                        </div>

                        <div class="bank">
                        <?php if(!empty($pending_verification['bank']['bank_ac_holder_name'])) {
                            $tinVal = $pending_verification['bank']['bank_ac_holder_name'];
                         ?>    
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$tinVal['update_type'])); ?> </label>
                            <div class="col-sm-5">
                                <input type="text" 
                                       class="form-control verified_<?php echo $tinVal['update_type']; ?>" 
                                       name="verified_<?php echo $tinVal['update_type']; ?>" 
                                       value="<?php echo $tinVal['new_value']; ?>" 
                                       placeholder="<?php echo $tinVal['update_type']; ?>" 
                                       data-update-id="<?php echo $tinVal['update_id']; ?>"/>
                            </div>
                        </div>
                       <?php } ?>

                        <?php if(!empty($pending_verification['bank']['bank_ac_number'])) {
                            $tinVal = $pending_verification['bank']['bank_ac_number'];
                            $update_id = $pending_verification['bank']['bank_ac_holder_name']['update_id'] . ',' .
                                         $pending_verification['bank']['bank_ac_number']['update_id']  . ',' .
                                         $pending_verification['bank']['ifsc_code']['update_id']  . ',' .
                                         $pending_verification['bank']['cancel_cheque_image']['update_id'];

                            $update_type = $pending_verification['bank']['bank_ac_holder_name']['update_type'] . ',' .
                                         $pending_verification['bank']['bank_ac_number']['update_type']  . ',' .
                                         $pending_verification['bank']['ifsc_code']['update_type']  . ',' .
                                         $pending_verification['bank']['cancel_cheque_image']['update_type'];             
                        ?>    
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$tinVal['update_type'])); ?> </label>
                                <div class="col-sm-5">
                                    <input type="text" 
                                           class="form-control verified_<?php echo $tinVal['update_type']; ?>" 
                                           name="verified_<?php echo $tinVal['update_type']; ?>" 
                                           value="<?php echo $tinVal['new_value']; ?>" 
                                           placeholder="<?php echo $tinVal['update_type']; ?>" 
                                           data-update-id="<?php echo $tinVal['update_id']; ?>"/>
                                </div>
                                <?php if(!empty($tinVal['update_type']) && $tinVal['update_type'] == "bank_ac_number") { ?>
                                    <a href="javascript:void(0);"
                                        id="approved_<?php echo $tinVal['update_type']; ?>" 
                                        class="btn btn-success btn_approved" 
                                        data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"approved", "update_type" => $update_type, "seller_data" => array("bank_ac_holder_name"=>$pending_verification["bank"]["bank_ac_holder_name"]["new_value"], "bank_ac_number"=>$tinVal["new_value"], "ifsc_code"=>$pending_verification["bank"]["ifsc_code"]["new_value"], "cancel_cheque_image" =>$pending_verification["bank"]["cancel_cheque_image"]["new_value"] )));?>'
                                        data-check-update-type = "bank_ac_number">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a href="javascript:void(0);" 
                                        id="cancel_<?php echo $tinVal['update_type']; ?>"
                                        class="btn btn-danger btn_cancel" 
                                        data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"cancelled", "update_type" => $update_type));?>'
                                        data-check-update-type = "bank_ac_number">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <?php if(!empty($pending_verification['bank']['cancel_cheque_image'])) { 
                                            $bankImage = $pending_verification['bank']['cancel_cheque_image'];
                                    ?>
                                        <a id="test_<?php echo $bankImage['update_id']; ?>" href="" type="button" class="btn btn-warning download_verify_image" download><i class="fa fa-download"></i></a>
                                        <input type="hidden" value="<?php echo $bankImage['images']; ?>" id="img" class="verification_image" data-id="<?php echo $bankImage['update_id']; ?>" name="download_img_<?php echo $bankImage['update_type']; ?>" data-update-id="<?php echo $bankImage['update_id']; ?>"  />                                    
                                    <?php } ?>    
                                <?php } ?>
                            </div>
                       <?php } ?>
                       <?php if(!empty($pending_verification['bank']['ifsc_code'])) {
                            $tinVal = $pending_verification['bank']['ifsc_code'];
                        ?>    
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$tinVal['update_type'])); ?> </label>
                                <div class="col-sm-5">
                                    <input type="text" 
                                           class="form-control verified_<?php echo $tinVal['update_type']; ?>" 
                                           name="verified_<?php echo $tinVal['update_type']; ?>" 
                                           value="<?php echo $tinVal['new_value']; ?>" 
                                           placeholder="<?php echo $tinVal['update_type']; ?>" 
                                           data-update-id="<?php echo $tinVal['update_id']; ?>"/>
                                    <span class="sample_ifsc_code">IFSC Code Ex:- KARB0000001 or BARB0DIGJAI</span>
                                    <button type="button" id="verified_ifsc_code_loading" class="btn btn-primary btn-xs hidden"></button>
                                    <div class="verified_bank_details_block"></div>      
                                </div>
                            </div>
                       <?php } ?>
                       </div>
                       <!-- GST Details -->
                       <div class="gst">
                        <?php if(!empty($pending_verification['gst']['gst_arn'])) {
                            $gstVal = $pending_verification['gst']['gst_arn'];
                         ?>    
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$gstVal['update_type'])); ?> </label>
                            <div class="col-sm-5">
                                <input type="text" 
                                       class="form-control verified_<?php echo $gstVal['update_type']; ?>" 
                                       name="verified_<?php echo $gstVal['update_type']; ?>" 
                                       value="<?php echo $gstVal['new_value']; ?>" 
                                       placeholder="<?php echo $gstVal['update_type']; ?>" 
                                       data-update-id="<?php echo $gstVal['update_id']; ?>"/>
                            </div>
                        </div>
                       <?php } ?>

                        <?php if(!empty($pending_verification['gst']['gst_provisional_id'])) {
                            $gstVal = $pending_verification['gst']['gst_provisional_id'];
                            $update_id = $pending_verification['gst']['gst_arn']['update_id'] . ',' .
                                         $pending_verification['gst']['gst_provisional_id']['update_id']  . ',' .
                                         $pending_verification['gst']['gst_certificate_image']['update_id'];

                            $update_type = $pending_verification['gst']['gst_arn']['update_type'] . ',' .
                                         $pending_verification['gst']['gst_provisional_id']['update_type']  . ',' .
                                         $pending_verification['gst']['gst_certificate_image']['update_type'];             
                        ?>    
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_',' ',$gstVal['update_type'])); ?> </label>
                                <div class="col-sm-5">
                                    <input type="text" 
                                           class="form-control verified_<?php echo $gstVal['update_type']; ?>" 
                                           name="verified_<?php echo $gstVal['update_type']; ?>" 
                                           value="<?php echo $gstVal['new_value']; ?>" 
                                           placeholder="<?php echo $gstVal['update_type']; ?>" 
                                           data-update-id="<?php echo $gstVal['update_id']; ?>"/>
                                </div>
                                <?php if(!empty($gstVal['update_type']) && $gstVal['update_type'] == "gst_provisional_id") { ?> 
                                    <a href="javascript:void(0);"
                                        id="approved_<?php echo $gstVal['update_type']; ?>" 
                                        class="btn btn-success btn_approved" 
                                        data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"approved", "update_type" => $update_type, "seller_data" => array("gst_arn"=>$pending_verification["gst"]["gst_arn"]["new_value"], "gst_provisional_id"=>$gstVal["new_value"], "gst_certificate_image" =>$pending_verification["gst"]["gst_certificate_image"]["new_value"] )));?>'
                                        data-check-update-type = "gst_provisional_id">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a href="javascript:void(0);" 
                                        id="cancel_<?php echo $gstVal['update_type']; ?>"
                                        class="btn btn-danger btn_cancel" 
                                        data-payload = '<?php echo json_encode(array("seller_id"=>$seller_id, "update_id"=> $update_id, "verification_status"=>"cancelled", "update_type" => $update_type));?>'
                                        data-check-update-type = "gst_provisional_id">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <?php if(!empty($pending_verification['gst']['gst_certificate_image'])) { 
                                            $gstImage = $pending_verification['gst']['gst_certificate_image'];
                                    ?>
                                        <a id="test_<?php echo $gstImage['update_id']; ?>" href="" type="button" class="btn btn-warning download_verify_image" download><i class="fa fa-download"></i></a>
                                        <input type="hidden" value="<?php echo $gstImage['images']; ?>" id="img" class="verification_image" data-id="<?php echo $gstImage['update_id']; ?>" name="download_img_<?php echo $gstImage['update_type']; ?>" data-update-id="<?php echo $gstImage['update_id']; ?>"  />                                    
                                    <?php } ?>    
                                <?php } ?>
                            </div>
                       <?php } ?>
                       </div>

                    <?php } ?>
                </div>
                <!-- END -->
            </div>
        </form>

      </div>
    </div>
    </div>
  </div>

<script type="text/javascript">
    $(function() {

        $("select[name='country_id']").bind('change', function() {
            $.ajax({
                url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + this.value,
                dataType: 'json',
                beforeSend: function() {
                    $("select[name='country_id']").after('<i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                complete: function() {
                    $('.fa-spin').remove();
                },
                success: function(json) {
                    html = '';
                    //html = '<option value=""><?php echo $text_select; ?></option>';
                    if (json['zone']) {
                        for (i = 0; i < json['zone'].length; i++) {
                            html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                            if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                                html += ' selected="selected"';
                            }

                            html += '>' + json['zone'][i]['name'] + '</option>';
                        }
                    } else {
                        html += '<option value="0" selected="selected"><?php echo $text_select; ?></option>';
                    }

                    $("select[name='zone_id']").html(html);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }).trigger('change');

        $("select[name='pickup_country']").bind('change', function() {
            $.ajax({
                url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + this.value,
                dataType: 'json',
                beforeSend: function() {
                    $("select[name='pickup_country']").after('<i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                complete: function() {
                    $('.fa-spin').remove();
                },
                success: function(json) {
                    html = '';
                    //html = '<option value=""><?php echo $text_select; ?></option>';
                    if (json['zone']) {
                        for (i = 0; i < json['zone'].length; i++) {
                            html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                            if (json['zone'][i]['zone_id'] == '<?php echo $pickup_zone_id; ?>') {
                                html += ' selected="selected"';
                            }

                            html += '>' + json['zone'][i]['name'] + '</option>';
                        }
                    } else {
                        html += '<option value="0" selected="selected"><?php echo $text_select; ?></option>';
                    }

                    $("select[name='pickup_zone']").html(html);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }).trigger('change');
    });


    $(document).ready(function(){
        $('input[name=\'sor_enable_checkbox\']').on('click',function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('.sor_enable_text_box').show();
            }else{
                $(this).val(0);
                $('.sor_enable_text_box').hide();
            }
        });

        if($('input[name=\'sor_enable_checkbox\']').val()== 1){
            $('.sor_enable_text_box').show();
        }else{
            $('.sor_enable_text_box').hide();
        }
    });

</script>

<script type="text/javascript">

    var category_row = <?php echo $category_row; ?>;

    function addCategoryRow() {
        var categories_id = $('select[name=\'categories\'] option:selected').val();
        var category_name = $('select[name=\'categories\'] option:selected').text();
        var rating = $('select[name=\'categories_review\']').val();
        var product_rating_config = JSON.parse('<?php echo json_encode(PRODUCT_RATING_CONFIG); ?>');

        if(categories_id != 0){
            html  = '<tr id="category-row' + category_row + '">';
            //html += '  <td class="text-left">'+category_name+'<input type="hidden" data-block-name="rating" name="category_rating[' + category_row + '][category_id]" data-old-value="0" value="'+categories_id+'" id="lable-category'+ category_row + '" class="edit_track" data-change="true"/></td>';
            //html += '  <td class="text-right"><input type="number" min="0" max="5" data-block-name="rating" name="category_rating[' + category_row + '][rating]" data-old-value="0" value="'+rating+'" placeholder="Rating" class="form-control edit_track" data-change="true" /></td>';
            html += '  <td class="text-left">'+category_name+'<input type="hidden" name="category_rating[' + categories_id + '][category_id]" value="'+categories_id+'" id="lable-category'+ category_row + '" /></td>';
            
            //html += '  <td class="text-right"><input type="number" min="0" max="5" name="category_rating[' +categories_id + '][rating]" value="'+rating+'" placeholder="Rating" class="form-control edit_track" data-change="true" data-block-name="rating" data-old-value="0" /></td>';

            html += '<td class="text-right"><select name="category_rating[' +categories_id + '][rating]" id="select-category-product-ratings" class="form-control edit_track" data-change="true" data-block-name="rating" data-old-value="0">';
                html += '<option value="">--Select--</option>';
                     $.each(product_rating_config,function(key, value){
                        var selected_val = '';
                        if(key == rating){
                            selected_val = 'selected';
                        }
                        html += '<option value="'+key+'" ' + selected_val + '>'+value+'</option>';
                     });
            html += '</select> </td>';


            html += '  <td class="text-left"><button type="button" onclick="$(\'#category-row' + category_row  + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
            html += '</tr>';
            $('#category_rating tbody').append(html);

            $('select[name=\'categories_review\']').val('');
            $('select[name=\'categories\']').val(0);
        }else{
            alert('Please select any category !');
        }
        category_row++;
    }

    $('.remove_row').click(function(){
        var row_no = $(this).data('row-no');
        $('#category-row'+row_no).remove();
        $('#hidden_row_input_box_'+row_no).attr('data-change','true');
        $('#hidden_row_input_box_'+row_no).attr('disabled',true);
    });
</script>



<?php echo $footer; ?>
<!-- FOR IFSC code -->
<script type="text/javascript">
    $(document).ready(function(){
        $('input[name=\'ifsc_code\']').on('keyup',function(e){
            var filter = /^([a-zA-Z0-9]){1}$/;
            if (filter.test(e.key)){
                getIfscCodeDetails(this,'.bank_details_block', '#ifsc_code_loading');   
            }else{
                $('.bank_details_block').hide();
               // $('#ms-submit-button').attr('disabled',true);
            }       
        });
    });

    function getIfscCodeDetails(obj, class_ctn, id_loading){
        var ifsc_code = $(obj).val().toUpperCase();
        //var filter = /^([A-Z]{4})+([0-9]{7})$/;
        if(ifsc_code.length == 11){
            flag = false;
            $.ajax({
                //url: 'https://ifsc.razorpay.com/'+ifsc_code,
                url : 'index.php?route=sellers/sellers/bankDetails&token=<?php echo $token;?>&ifsc_code='+ifsc_code ,
                dataType : 'json',
                async: false,
                beforeSend: function() {
                    setTimeout(function(){
                        $(id_loading).button('loading');
                        $(id_loading).removeClass('hidden');
                    },0);
                },
                complete: function() {
                    setTimeout(function(){
                        $(id_loading).button('reset');
                        if(id_loading == '#ifsc_code_loading'){
                            $(id_loading).addClass('hidden');
                        }
                    },1000);
                },

                success:function(json){
                    $('.seller-ifsc').hide();
                    if(typeof(json) == 'object'){ 
                        $(".seller-ifsc").remove();
                        html = '<table class="table table-responsive table-bordered">';
                        html += '<caption align="center"><h4>Bank Detail</h4></caption>';
                        html +=     '<tr>';
                        html +=         '<td><b>Bank Name: </b></td>';
                        html +=         '<td>'+json['BANK']+'</td>';
                        html +=     '</tr>';
                        html +=     '<tr>';
                        html +=         '<td><b>Branch: </b></td>';
                        html +=         '<td>'+json['BRANCH']+'</td>';
                        html +=     '</tr>';
                        html +=     '<tr>';
                        html +=         '<td><b>Address: </b></td>';
                        html +=         '<td>'+json['ADDRESS']+'</td>';
                        html +=     '</tr>';
                        html +=     '<tr>';
                        html +=         '<td><b>Place: </b></td>';
                        html +=         '<td>'+json['DISTRICT']+'<br> '+json['CITY']+', '+json['STATE']+'</td>';
                        html +=     '</tr>';
                        html += '</table>';

                        $(class_ctn).html(html);
                        $(class_ctn).show();
                        // $('input[name=\'seller[bank_name]\']').val(json['BANK']);
                        // $('input[name=\'seller[bank_branch]\']').val(json['BRANCH']);
                        // $('input[name=\'seller[bank_city]\']').val(json['CITY']);
                        // $('input[name=\'seller[bank_state]\']').val(json['STATE']);
                        $('#ms-submit-button').removeAttr('disabled',true);
                        validateBankDetails(false);
                        flag = true;
                    }else{
                         
                        $('#ms-submit-button').attr('disabled',true);
                        $('.error-ms-bank').remove();
                        $(obj).parent().find('.sample_ifsc_code').after('<span class="alert-danger seller-ifsc">'+ json +'</span>');
                        $(class_ctn).html('');
                        flag = false;
                    }
                },
            });

            return flag;
        }else if(ifsc_code.length == 0){
            validateBankDetails(false);
            $(class_ctn).html(''); 
            return false;
                       
        }else{
            $('#ms-submit-button').attr('disabled',true);
            $(class_ctn).html('');
            return false;
        }
    }

    $(function(){
        getIfscCodeDetails('input[name=\'ifsc_code\']','.bank_details_block','#ifsc_code_loading');
    });
    // $('input[name="bank_ac_holder_name"], input[name="bank_ac_number"], input[name="ifsc_code"]').on('keyup', function(){
    //     validateBankDetails(true)
    // });

    function validateBankDetails(callgetifsc = true){
        var ac_name = $('input[name="bank_ac_holder_name"]').val().trim();
        var ac_no = $('input[name="bank_ac_number"]').val().trim();
        var ifsc = $('input[name="ifsc_code"]').val().trim();
        var error = [];
        if(ac_name.length == 0){
            error.push("error");

        }
        if(ac_no.length == 0 ){
            error.push("error");
        }
        if(ifsc.length == 0 ){
            error.push("error");
        }
        if(error.length > 0 && error.length < 3){
           $('#ms-submit-button').attr('disabled',true);
        }
        else{
           $('#ms-submit-button').attr('disabled',false);
           if(callgetifsc){
             getIfscCodeDetails('input[name=\'ifsc_code\']','.bank_details_block', '#ifsc_code_loading'); 
           }
        }
    }

</script>
<script type="text/javascript">
    $(document).ready(function(){   

        $("input[name=\'primary_contact_name\']").on('keyup',function(){
            $("input[name=\'same_as_primary_pickup\']").val('0').attr('checked',false);
            $("input[name=\'same_as_primary_account\']").val('0').attr('checked',false);
            $("input[name=\'same_as_primary_inventory\']").val('0').attr('checked',false);

            $("input[name=\'pickup_holder_name\']").val('').attr('readonly',false);
            $("input[name=\'account_holder_name\']").val('').attr('readonly',false);
            $("input[name=\'inventory_holder_name\']").val('').attr('readonly',false);
            $("input[name=\'pickup_contact_no\']").val('').attr('readonly',false);
            $("input[name=\'account_contact_no\']").val('').attr('readonly',false);
            $("input[name=\'inventory_contact_no\']").val('').attr('readonly',false);
        });

       $("input[name=\'primary_contact_no\']").on('keyup',function(){
            $("input[name=\'same_as_primary_pickup\']").val('0').attr('checked',false);
            $("input[name=\'same_as_primary_account\']").val('0').attr('checked',false);
            $("input[name=\'same_as_primary_inventory\']").val('0').attr('checked',false);

            $("input[name=\'pickup_holder_name\']").val('').attr('readonly',false);
            $("input[name=\'account_holder_name\']").val('').attr('readonly',false);
            $("input[name=\'inventory_holder_name\']").val('').attr('readonly',false);
            $("input[name=\'pickup_contact_no\']").val('').attr('readonly',false);
            $("input[name=\'account_contact_no\']").val('').attr('readonly',false);
            $("input[name=\'inventory_contact_no\']").val('').attr('readonly',false);
        }); 


        // same as primary
        $("input[name=\'same_as_primary_pickup\']").on('click',function(){
            var primary_name = $("input[name=\'primary_contact_name\']").val();
            var primary_contact = $("input[name=\'primary_contact_no\']").val();

            if($(this).is(':checked')){
                $(this).val(1);

                $("input[name=\'pickup_holder_name\']").val(primary_name).attr('readonly',true);
                $("input[name=\'pickup_contact_no\']").val(primary_contact).attr('readonly',true);
                $("input[name=\'pickup_holder_name\']").attr('data-change','true');
                $("input[name=\'pickup_contact_no\']").attr('data-change','true');

            }else{
                $(this).val(0);
                $("input[name=\'pickup_holder_name\']").val('').attr('readonly',false);
                $("input[name=\'pickup_contact_no\']").val('').attr('readonly',false);
                $("input[name=\'pickup_holder_name\']").attr('data-change','true');
                $("input[name=\'pickup_contact_no\']").attr('data-change','true');
            }
        });

        $("input[name=\'same_as_primary_account\']").on('click',function(){
            var primary_name = $("input[name=\'primary_contact_name\']").val();
            var primary_contact = $("input[name=\'primary_contact_no\']").val();

            if($(this).is(':checked')){
                $(this).val(1);
                
                $("input[name=\'account_holder_name\']").val(primary_name).attr('readonly',true);
                $("input[name=\'account_contact_no\']").val(primary_contact).attr('readonly',true);
                $("input[name=\'account_holder_name\']").attr('data-change','true');
                $("input[name=\'account_contact_no\']").attr('data-change','true');

            }else{
                $(this).val(0);
                $("input[name=\'account_holder_name\']").val('').attr('readonly',false);
                $("input[name=\'account_contact_no\']").val('').attr('readonly',false);
                $("input[name=\'account_holder_name\']").attr('data-change','true');
                $("input[name=\'account_contact_no\']").attr('data-change','true');
            }
        });

        $("input[name=\'same_as_primary_inventory\']").on('click',function(){
            var primary_name = $("input[name=\'primary_contact_name\']").val();
            var primary_contact = $("input[name=\'primary_contact_no\']").val();

            if($(this).is(':checked')){
                $(this).val(1);
                $("input[name=\'inventory_holder_name\']").val(primary_name).attr('readonly',true);
                $("input[name=\'inventory_contact_no\']").val(primary_contact).attr('readonly',true);
                $("input[name=\'inventory_holder_name\']").attr('data-change','true');
                $("input[name=\'inventory_contact_no\']").attr('data-change','true');

            }else{
                $(this).val(0);
                $("input[name=\'inventory_holder_name\']").val('').attr('readonly',false);
                $("input[name=\'inventory_contact_no\']").val('').attr('readonly',false);
                $("input[name=\'inventory_holder_name\']").attr('data-change','true');
                $("input[name=\'inventory_contact_no\']").attr('data-change','true');
            }
        });

        $('input[name=\'same_as_primary_address\']').on('click',function(){
            var default_zone = 1475;
            if($(this).is(':checked')){
                $(this).val(1);
                var firm_address = $("textarea[name=\'seller_firm_address\']").val();
                var firm_city = $("input[name=\'city\']").val();
                var firm_region = $("select[name=\'zone_id\']").val();
                var firm_pincode = $("input[name=\'pincode\']").val();

                $('textarea[name=\'pickup_address\']').val(firm_address).attr('readonly',true);
                $('input[name=\'pickup_city\']').val(firm_city).attr('readonly',true);
                $('select[name=\'pickup_zone\']').val(firm_region).attr('readonly',true);
                $('input[name=\'pickup_pincode\']').val(firm_pincode).attr('readonly',true);
                $('textarea[name=\'pickup_address\']').attr('data-change','true');
                $('input[name=\'pickup_city\']').attr('data-change','true');
                $('select[name=\'pickup_zone\']').attr('data-change','true');
                $('input[name=\'pickup_pincode\']').attr('data-change','true');

            }else{
                $(this).val(0);
                $('textarea[name=\'pickup_address\']').val('').attr('readonly',false);
                $('input[name=\'pickup_city\']').val('').attr('readonly',false);
                $('select[name=\'pickup_zone\']').val(default_zone).attr('readonly',false);
                $('input[name=\'pickup_pincode\']').val('').attr('readonly',false);
                $('select[name=\'pickup_zone\'] option[value='+default_zone+']').attr('selected',true);
                $('textarea[name=\'pickup_address\']').attr('data-change','true');
                $('input[name=\'pickup_city\']').attr('data-change','true');
                $('select[name=\'pickup_zone\']').attr('data-change','true');
                $('input[name=\'pickup_pincode\']').attr('data-change','true');
            }
        });

        $("textarea[name=\'seller_firm_address\'], input[name=\'city\'],select[name=\'zone_id\'],input[name=\'pincode\'] ").on('keyup',function(){
            var default_zone = 1475;
            $('input[name=\'same_as_primary_address\']').val('0').attr('checked',false);
            $('textarea[name=\'pickup_address\']').val('').attr('readonly',false);
            $('input[name=\'pickup_city\']').val('').attr('readonly',false);
            $('select[name=\'pickup_zone\'] option[value='+default_zone+']').attr('selected',true);
            $('select[name=\'pickup_zone\']').val(default_zone).attr('readonly',false);
            $('input[name=\'pickup_pincode\']').val('').attr('readonly',false);
            $('textarea[name=\'pickup_address\']').attr('data-change','true');
            $('input[name=\'pickup_city\']').attr('data-change','true');
            $('select[name=\'pickup_zone\']').attr('data-change','true');
            $('input[name=\'pickup_pincode\']').attr('data-change','true');

        });

    });
</script>
<!-- for verification -->
<script type="text/javascript">
    
    $(document).ready(function(){

        $('.verification_request').on('click',function(){
            var ifsc_code = $('#verification-request').find('.verified_ifsc_code');
            if( ifsc_code.length > 0 ){
                getIfscCodeDetails(ifsc_code,'.verified_bank_details_block', ''); 
            }
        });

        $('.btn_approved').click(function(){

            var btn = $(this);

            var check_update_type = $(this).data('check-update-type');
            
            var dataSeller = $(this).data('payload'); 
          

            var flag = true;
            if(check_update_type == "pan"){
                flag = pan_card($(".verified_pan").val());
            }

            if(check_update_type == "gst_provisional_id"){
                flag = gstin_validatation($(".verified_gst_provisional_id").val());
            }

            if(check_update_type == 'bank_ac_number'){
                flag = getIfscCodeDetails('.verified_ifsc_code',
                                          '.verified_bank_details_block',
                                          '#approved_ifsc_code');
            }

            if( flag ) {
                $.ajax({
                    url: 'index.php?route=sellers/sellers/updateVerificationRequest&token=<?php echo $token;?>',
                    type:'post',
                    dataType: 'json',
                    data: dataSeller,
                    beforeSend: function() {
                        btn.button('loading');
                    },
                    complete: function() {
                        btn.button('reset');
                    },    
                    success: function(json) {
                        if(json['status'] == 0){
                           alert(json['error']);
                           return false;
                        }
                        if(json['status'] == 1){
                           $("#approved_"+check_update_type).addClass('hidden');
                           $("#cancel_"+check_update_type).addClass('hidden'); 
                        }
                    },
                });
            } 
        });

        $('.btn_cancel').click(function(){

            var dataSeller = $(this).data('payload'); 
            var check_update_type = $(this).data('check-update-type');

            $.ajax({
                url: 'index.php?route=sellers/sellers/updateVerificationRequest&token=<?php echo $token;?>',
                type:'post',
                dataType: 'json',
                data: dataSeller,
                beforeSend: function() {
                    $("#cancel_"+check_update_type).button('loading');
                },
                complete: function() {
                    $("#cancel_"+check_update_type).button('reset');
                },      
                success: function(json) {
                    if(json['status'] == 1){
                      $("#approved_"+check_update_type).addClass('hidden'); 
                      $("#cancel_"+check_update_type).addClass('hidden'); 
                    }
                },
            });
        });
        

        // set new nick name
        $("select[name=\'new_nickname\']").on('change',function(){
            var citycode = $(this).val();
            var seller_id = $("input[name=\'seller_id\'").val();
            $.ajax({
                url : 'index.php?route=sellers/sellers/getNewNickname&token=<?php echo $token?>',
                type: 'POST',
                data:{'citycode':citycode, 'seller_id':seller_id},
                success: function(json_data){
                    if((json_data['nickname'])){
                        $("input[name=\'seller_nickname\']").val(json_data['nickname']);
                        $('.set_new_nickname').text('');
                    } else {
                        $('.set_new_nickname').text(json_data['error']);
                        $("input[name=\'seller_nickname\']").val('');
                    }
                }

            });
        });


        //additional_details show on status change
        $("select[name=\'seller_status\']").on('change',function(){
            var current_value = $(this).val();
            var previous_value = $(this).data('old-value');
            if(previous_value != current_value){
                $("textarea[name=\'additional_details\']").parent().parent().removeClass('hidden');
                $('#ms-submit-button').addClass('disabled');
            }else{
                $("textarea[name=\'additional_details\']").parent().parent().addClass('hidden');
                $('#ms-submit-button').removeClass('disabled');
            }
        });

        $("textarea[name=\'additional_details\']").on('keyup',function(){
            var textlen = $(this).val().trim().length;

            if(textlen > 50){
                $('#ms-submit-button').removeClass('disabled');
            } else{
                $('#ms-submit-button').addClass('disabled');
            }
        });

        // form submit after some information change of seller
        $('.edit_track').on('change',function(){
            $(this).attr('data-change','true');
        });


        $("input[name=\'pincode\']").on('keyup',function(){
            if($(this).val().length == 6){
                var pincode = $(this).val();
                var CheckZipCode = /(^\d{6}$)/;
                if((!CheckZipCode.test(pincode))){
                    alert("Pincode is invalid");
                    return false;
                } else {
                    $.ajax({
                        url:'index.php?route=sellers/sellers/getAutoGenerateState&token=<?php echo $token; ?>',
                        type:'POST',
                        dataType:'json',
                        data:{"pincode":pincode},
                        success:function(json){
                            if( !json['error'] ){
                              $("select[name=\'zone_id\']").val(json['zone_id']);
                              $("input[name=\'city\']").val(json['city']);
                            } else {
                                $("select[name=\'zone_id\']").val(1475);
                                $("input[name=\'city\']").val('');
                                alert(json['error']);
                            }
                        },
                    });
                }
            }
        });

        $("input[name=\'pickup_pincode\']").on('keyup',function(){
            if($(this).val().length == 6){
                var pincode = $(this).val();
                var CheckZipCode = /(^\d{6}$)/;
                if((!CheckZipCode.test(pincode))){
                    alert("Pincode is invalid");
                    return false;
                } else {
                    $.ajax({
                        url:'index.php?route=sellers/sellers/getAutoGenerateState&token=<?php echo $token; ?>',
                        type:'POST',
                        dataType:'json',
                        data:{"pincode":pincode},
                        success:function(json){
                            if( !json['error'] ){
                              $("select[name=\'pickup_zone\']").val(json['zone_id']);
                              $("input[name=\'pickup_city\']").val(json['city']);
                            } else {
                                alert(json['error']);
                                $("input[name=\'pickup_city\']").val('');
                            }
                        },
                    });
                }
            }
        });

        $("input[name=\'pickup_city_code\']").on('keyup',function(){
            var value = $(this).val().toUpperCase();
            if(value.length == 2 ){
                $(this).val(value);
                return false;    
            }
        });

        $("input[name=\'gst_provisional_id\']").on('keyup',function(){
            var value = $(this).val().toUpperCase();
            if(value.length >= 2 ){                
                $(this).val(value);

                $('#pan').val(value.substring(2,12));
                return false;    
            }
        });
        
        $("input[name=\'gst_provisional_id\']").on('blur',function(){
            var gst_number = $(this).val().toUpperCase();
            return gstin_validatation(gst_number);
        });

        loadImageLink();

        $("#additional_details").on('keyup',function(){
            if( $(this).val().length <= 50){
                var remaining_character = 50 - $(this).val().length;
                $(this).next('span').remove();
                $(this).after('<span class="text-danger"> Your remaining character is '+remaining_character+' . </span>');
            } else {
                $(this).next('span').remove();
            }
        });
        
    });

    function getPincode(pincode){
        
    }
    
    function loadImageLink(){    
        //var image_name = $('#img').val(); 
        var image_name = {};
        var id = '';

        $('.verification_image').each(function(){
            id = $(this).data('id');
            image_name['test_'+id] = $(this).val().trim();
        });

        if($('.verification_image').length > 0){
            $.ajax({
                url: 'index.php?route=sellers/sellers/verify_dwnlod_img&token=<?php echo $token;?>',
                type: 'POST',
                data: {"image_name":image_name},
                dataType: 'json',
                success: function(data){
                    $(Object.keys(data)).each(function(index,element){
                        $('#'+element).attr('href',data[element]);
                    });
                }
            });
        }
    }

    function validation(){

        flag = true;
        if($('input[name=\'sor_enable_checkbox\']').val()== 1){
            if($('input[name=\'seller_markup\']').val() == '' && $('input[name=\'wsb_commission\']').val() == '' ){
                alert('Please enter seller markup and wsb commission !');
                flag = false;
            }
        }

        if($('input[name=\'pan\']').val() != ''){
            flag = pan_card($('input[name=\'pan\']').val());
        } 

        if($('input[name=\'gst_provisional_id\']').val() != ''){
            flag = gstin_validatation($('input[name=\'gst_provisional_id\']').val());
        }

        $('.mobileno').each(function(){
            if($('select[name="seller_status"]').val() == 1 || $('select[name="seller_status"]').val() == 0){
                validateValue =  phonenumber($(this).val(), $(this).data('field-name') , $(this).attr('id'));

                if(validateValue == 2 ){
                    flag = 0;
                }
            }
        });
       
        var product_name_prefix = $('input[name=\'product_name_prefix\']').val().trim();
        if( product_name_prefix.length > 12 ){
            alert("Product Name Prefix must be less then equals to 12 !");
            flag = 0;
        }
        if( product_name_prefix.length > 0 && !product_name_prefix.match(/^([a-zA-Z0-9]+)$/)){
            alert("Product Name Prefix must be only Alpha-Numeric !");
            flag = 0;
        }
        $('input[name=\'product_name_prefix\']').val( product_name_prefix );

        // if user something change values of seller information
        if( flag ) {
            var old_data_format = {};
            $('.edit_track').each(function(){
                var data_change = $(this).data('change');
                var previous_value = $(this).data('old-value');
                if(typeof previous_value === 'string'){
                    previous_value = previous_value.replace(/\r?\n|\r/g,'');
                    previous_value = previous_value.replace(/"/g, '\\"'); 
                }
                var new_value = $(this).val();
                if(typeof new_value === 'string'){
                    new_value = new_value.replace(/\r?\n|\r/g,'');
                    new_value = new_value.replace(/"/g, '\\"'); 
                }
                var update_group = $(this).data('block-name');
                if(typeof update_group === 'string'){
                    update_group = update_group.replace(/\r?\n|\r/g,'');
                    update_group = update_group.replace(/"/g, '\\"'); 
                }
                var name = $(this).attr('name');
            
                if( data_change ) {
                    old_data_format[name] = $.parseJSON('{"previous_value":"'+  previous_value + '","new_value":"' + new_value + '","update_group":"' + update_group + '"}');
                    
                }
            });

            if( $('#changes_data').val().length > 0 ){
                old_data_format = $.parseJSON($('#changes_data').val());
            }
            $('#changes_data').val( JSON.stringify(old_data_format) );       
        } else{
            return false;
        }

        return true;
    }

    /*  This function validates for PAN Card No.*/
    function pan_card(textObj) {
        var textObj = textObj.toUpperCase();
        var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
        /*C - Company  
        P - Person 
        H - HUF(Hindu Undivided Family) 
        F - Firm 
        A - Association of Persons (AOP) 
        T - AOP (Trust) 
        B - Body of Individuals (BOI) 
        L - Local Authority 
        J - Artificial Juridical Person 
        E - Limited Liability Partnership
        G - Govt.*/
        var code= /([C,P,H,F,A,T,B,L,J,G,E])/;
        var code_chk=textObj.substring(3,4);

        if (textObj!=="") {
            if(regpan.test(textObj) == false) {
                alert("Invaild PAN Card No.");
                return false; 
            }
            if (code.test(code_chk)==false) {
                alert("Invaild PAN Card No.");
                return false;
            }
        }
        return true;
    }

    /* This function validates for GSTIN */
    /*function gstin_validatation(textObj){
        var reggstin = /^([0-9]){2}([A-Z]){3}([C,P,H,F,A,T,B,L,J,G,E]){1}([A-Z]){1}([0-9]){4}([A-Z]){1}([A-Z0-9]){1}([A-Z]){1}([A-Z0-9]){1}?$/;

        if (textObj!=="") {
            if(reggstin.test(textObj) == false) {
                alert("Invalid GST Provisional ID");
               return false;
            }
        }
        return true;
    }*/       

    function containsAny(str, substrings) {
        for (var i = 0; i != substrings.length; i++) {
            var substring = substrings[i];
            if (str.indexOf(substring) != - 1) {
                return substring;
            }
        }
        return null; 
    }

    function phonenumber(inputtxt,inputname,id){
        var flag = 1;
        var phoneno = /^\d{10}$/;
        var res = inputtxt.charAt(0);
        var result = containsAny(res, ["9", "8", "7","6"]);
        
        if( !result ){
            flag = 0;
        }

        if( flag == 1 ){
            if( inputtxt.match(phoneno) ) {
                $('#'+id).next('div').remove();
                return 1;
            } else {
                $('#'+id).next('div').remove();
                $('#'+id).after('<div class="text-danger">Please Enter Valid Mobile Number in ' + inputname+'</div>');
                //alert('Please Enter Valid Mobile Number in ' + inputname);
                return 2;
            }
        } else {
            $('#'+id).next('div').remove();
            $('#'+id).after('<div class="text-danger">Please Enter Valid Mobile Number in ' + inputname+'</div>');
           //alert('Please Enter Valid Mobile Number in ' + inputname);
           return 2;
        }
    }


    $(document).ready(function(){
        var number_of_pending_verification = <?php echo !empty($number_of_pending_verification) ? $number_of_pending_verification : 0; ?>;
        if( number_of_pending_verification > 0 ){
            $('#form-seller input').attr('disabled',true);
            $('#form-seller textarea').attr('disabled',true);
            $('#form-seller select').attr('disabled',true);
            $('button').attr('disabled',true);
            $('#form-seller .product_review_hidden_input').attr('disabled',true);
        } else{
            $('#form-seller input').attr('disabled',false);
            $('#form-seller textarea').attr('disabled',false);
            $('#form-seller select').attr('disabled',false);
            $('button').attr('disabled',false);
            $('#form-seller .product_review_hidden_input').attr('disabled',true);
        }
    });
    
    
    /* Added by amarat */
    var promo_row = <?php echo $promo_row; ?>;
    function addImage() {
        
        var html  = '';
        
        html = '<tr id="promo-row' + promo_row +'">';
        html +=     '<td class="text-left">';
        html +=        '<select id="promo-cat' + promo_row +'" class="form-control" name="promotion['+ promo_row +'][category_id]" onchange="setProductCount(' + promo_row + ')">';
        html +=             '<option value="0">--select category--</option>';
                                       <?php if(!empty($seller_category_list)) { 
                                                foreach($seller_category_list as $cat) { ?>
        html +=                                    '<option data-count="<?php echo $cat['product_count']; ?>" value="<?php echo $cat['category_id']; ?>" > <?php echo $cat['name']; ?></option>';
                                            <?php } }?>
        html +=                                '</select>';
        html +=                            '</td>';
        html +=                            '<td class="text-left">';
        html +=                                '<input class="form-control" type="number" name="promotion[' + promo_row +'][product_count]" onchange="validateCount(' + promo_row + ')">';
        html +=                            '</td>';
        html +=                            '<td class="text-left">';
        html +=                                '<select class="form-control" name="promotion['+ promo_row +'][status]" readonly>';
        html +=                                    '<option value="1" selected>Enable</option>';
        // html +=                                    '<option value="2" >Disable</option>';
        html +=                                '</select>';
        html +=                            '</td>';
        html +=                            '<td class="text-left">';
        html += '  <button type="button" onclick="$(\'#promo-row' + promo_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>';
        html +=                            '</td>';
        html +=                        '</tr>';
        
        $('#images tbody').append(html);
        
            promo_row++;
    }
    
    
    function setProductCount(val){
        var product_count = '';
        //product_count = $('#promo-row'+ val +' option:selected', val).attr('data-count');
        product_count = $('#promo-cat'+ val +' option:selected').data('count');
        $( "input[name*='promotion[" + val + "][product_count]']" ).val(product_count); 
    } 
    
    function validateCount(val) {
      var product_count = $('#promo-cat'+ val +' option:selected').data('count');
      var input_count = $( "input[name*='promotion[" + val + "][product_count]']" ).val();
      if(input_count > product_count) {
        alert("Maximum Product Count for this category is "+product_count+".");
        $( "input[name*='promotion[" + val + "][product_count]']" ).val(product_count);
      }
    }
    /* End by amarat */
    
    
</script>

<style type="text/css">
    #promotion input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>